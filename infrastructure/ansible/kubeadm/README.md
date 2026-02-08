# Proxmox + Ansible + kubeadm Kubernetes HA Cluster

This repository contains Ansible playbooks to deploy a **Kubernetes HA cluster** (stacked etcd) on Proxmox VMs using **kubeadm.**

The setup uses:

- Containerd as container runtime
- Kubernetes v1.35 (pkgs.k8s.io repositories)
- Calico as CNI (configurable)
- External control-plane endpoint (e.g., via HAProxy)

## Cluster Overview

- **Control-plane nodes**: 3 (recommended odd number for etcd quorum)
- **Worker nodes**: Any number
- **Control-plane endpoint**: `192.168.1.30:6443` (configure your load balancer to point here)
- **Pod network CIDR**: `10.8.0.0/16` (Calico default)
- **Container runtime**: containerd
- : Calico (via `https://docs.projectcalico.org/manifests/calico.yaml`)

## Deploy Ansible

```bash
ansible-playbook playbook.yml
```