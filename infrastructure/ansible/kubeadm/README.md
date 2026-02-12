# Kubernetes HA Cluster Deployment Guide (Proxmox + Ansible + kubeadm)

This repository provides an automated workflow for deploying a **high‑availability Kubernetes cluster** (stacked etcd) on virtual machines hosted in **Proxmox VE**, using **Ansible** and **kubeadm**.  
The cluster uses containerd as the runtime, Calico as the CNI, and an external load‑balanced control‑plane endpoint.

---

## 1. Cluster Overview

The deployment creates a production‑ready Kubernetes HA setup with the following characteristics:

- **Control‑plane nodes:** 3 (recommended for etcd quorum)
- **Worker nodes:** Any number
- **External control‑plane endpoint:** `192.168.1.30:6443`  
  *(Configure your load balancer—e.g., HAProxy—to forward traffic here.)*
- **Pod network CIDR:** `10.8.0.0/16` (Calico default)
- **Container runtime:** containerd
- **CNI plugin:** Calico  
  *(Using `https://docs.projectcalico.org/manifests/calico.yaml`)*  
- **Kubernetes version:** v1.35 (via pkgs.k8s.io repositories)

---

## 2. Requirements

Ensure the following components are prepared before running the playbook:

- Proxmox VMs for:
  - 3 control‑plane nodes
  - 1 or more worker nodes
- Ansible installed on your management machine
- An inventory defining:
  - `masters` — control‑plane nodes
  - `workers` — worker nodes
- A load balancer (e.g., HAProxy) configured to expose the Kubernetes API endpoint

---

## 3. Deployment

Run the Ansible playbook to configure the full Kubernetes HA cluster:
### a. Dry Run (Optional)

Run a check without applying changes:

```bash
ansible-playbook playbook.yml --check
```

### b. Apply Configuration

Execute the playbook:

```bash
ansible-playbook playbook.yml
```

The playbook performs the following actions:

- Installs required Kubernetes packages from pkgs.k8s.io
- Configures containerd
- Initializes the first control‑plane node with kubeadm
- Joins additional control‑plane nodes to the cluster
- Joins worker nodes
- Deploys Calico CNI
- Configures cluster networking and prerequisites

---

## 4. Notes

- Ensure your load balancer is reachable from all nodes.
- Control‑plane nodes must have stable, unique hostnames.
- Proxmox cloud‑init templates can simplify VM provisioning.
- Never store kubeadm join tokens or certificates in version control.

