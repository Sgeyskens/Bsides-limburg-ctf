# Proxmox + Ansible + kubeadm Kubernetes HA Cluster

This repository contains Ansible playbooks to deploy a **Kubernetes HA cluster** (stacked etcd) on Proxmox VMs using **kubeadm.**

The setup uses:

- Containerd as container runtime
- Kubernetes v1.35 (pkgs.k8s.io repositories)
- Calico as CNI (configurable)
- External control-plane endpoint (e.g., via HAProxy)

**Important**: The playbook **only initializes the first control-plane** automatically.  
Additional control-plane nodes and all workers are joined **manually** (recommended for reliability and to avoid etcd learner races/promotion issues)

## Cluster Overview

- **Control-plane nodes**: 3 (recommended odd number for etcd quorum)
- **Worker nodes**: Any number
- **Control-plane endpoint**: `192.168.1.30:6443` (configure your load balancer to point here)
- **Pod network CIDR**: `10.8.0.0/16` (Calico default)
- **Container runtime**: containerd
- : Calico (via `https://docs.projectcalico.org/manifests/calico.yaml`)

## Prerequisites

- Proxmox VMs (Debian 12 / Ubuntu 22.04+ recommended)
- All nodes:
    - SSH access from your Ansible controller
    - Static IPs
    - Time synchronized (NTP/chrony)
    - Swap disabled
    - Firewall ports open (see kubeadm docs)
    - Inventory file (`inventory.ini`) with groups:
    
    ```jsx
    [control_plane]
    192.168.1.[10:12]
    
    [workers]
    192.168.1.[20:22]
    
    [HAProxy]
    192.168.1.30
    
    [k8s_cluster:children]
    control_plane
    workers
    
    [all:vars]
    ansible_user=root
    ```
    

## Deploy Ansible

```bash
ansible-playbook -i inventory.ini playbook.yml
```

### Manually Join Additional Control-Plane Nodes (Masters)

**Important:** Join **one at a time** — wait 3–5 minutes between each to allow etcd learner promotion.

On the **first master** (Kubernetes-Master-0):

```bash
# Re-upload certs (key expires after 2 hours!)
sudo kubeadm init phase upload-certs --upload-certs
# Example output: Using certificate key: abcdef1234567890abcdef1234567890...

# Get fresh base join command
kubeadm token create --print-join-command
# Example: kubeadm join 192.168.1.30:6443 --token xxxxx... --discovery-token-ca-cert-hash sha256:yyyyy...

# Combine into FULL control-plane join (replace values):
kubeadm join 192.168.1.30:6443 --token xxxxx... \
  --discovery-token-ca-cert-hash sha256:yyyyy... \
  --control-plane \
  --certificate-key abcdef1234567890abcdef1234567890...
```

Run the last full command **as root** on the next master node.

Repeat for the third master (re-upload certs first if >2h passed).

**Verify after each join** (from first master):

```bash
kubectl get nodes -o wide
etcdctl member list   # (with cert flags) — expect growing number of members
```

### Manually Join Worker Nodes

On the **first master**:

```bash
kubeadm token create --print-join-command
# Example output:
# kubeadm join 192.168.1.30:6443 --token zzzzz... --discovery-token-ca-cert-hash sha256:wwwww...
```

Run the output command as root on each worker node.