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
    [masters]
    192.168.1.[10:12]
    
    [workers]
    192.168.1.[20:22]
    
    [haproxy]
    192.168.1.30
    
    [k8s_cluster:children]
    masters
    workers
    
    [all:vars]
    ansible_user=root
    ```
    

## Deploy Ansible

```bash
ansible-playbook -i inventory.ini playbook.yml
```