# MetalLB Deployment Guide (Ansible Role)

This repository provides an automated workflow for deploying **MetalLB** on a kubeadm‑based Kubernetes cluster using **Ansible**.  
The role installs MetalLB via Helm, configures a Layer‑2 IP address pool, and ensures all components are fully operational before proceeding.

---

## 1. Role Overview

The MetalLB role enables LoadBalancer functionality on **bare‑metal** or **on‑premises** Kubernetes clusters where no cloud load balancer is available.  
The role performs the following actions:

- Installs required Python dependencies for Kubernetes modules  
- Installs Helm (idempotent)  
- Adds the MetalLB Helm repository  
- Installs or upgrades the MetalLB Helm chart  
- Waits for controller and speaker pods to become ready  
- Applies:
  - `IPAddressPool` (Layer‑2 address pool)
  - `L2Advertisement` (ARP/NDP announcements)  
- Waits for all cluster pods to reach Ready state  
- Outputs MetalLB status for verification  

---

## 2. Requirements

Ensure the following prerequisites are met before running the role:

- Kubernetes cluster deployed via kubeadm  
- Access to `/etc/kubernetes/admin.conf` on the control plane  
- Required Ansible collection:
  - `kubernetes.core`
- A free IP range on your LAN for MetalLB LoadBalancer allocation  
- Variables defined in `variables.yml` or inventory  

---

## 3. Role Variables

Define the following variables to configure MetalLB:

| Variable | Description | Example |
|---------|-------------|---------|
| `metallb_namespace` | Namespace where MetalLB will be installed | `metallb-system` |
| `metallb_ip_pool_start` | Start of the LoadBalancer IP range | `192.168.1.240` |
| `metallb_ip_pool_end` | End of the LoadBalancer IP range | `192.168.1.250` |

Example `variables.yml`:

```yaml
metallb_namespace: metallb-system
metallb_ip_pool_start: 192.168.1.240
metallb_ip_pool_end: 192.168.1.250
```

---

## 4. How the Role Works

### a. Helm Installation  
The role installs Helm only if it is not already present:

- Downloads the official Helm installer  
- Executes the script  
- Removes the installer afterward  

### b. MetalLB Deployment  
MetalLB is deployed using Helm:

```yaml
kubernetes.core.helm:
  name: metallb
  chart_ref: metallb/metallb
  release_namespace: metallb-system
  create_namespace: true
  wait: true
```

### c. Readiness Checks  
The role waits for:

- MetalLB **controller** pod  
- MetalLB **speaker** pods  

This ensures MetalLB is fully operational before applying configuration.

### d. Layer‑2 Configuration  
The role applies:

- An `IPAddressPool` using your configured IP range  
- An `L2Advertisement` referencing that pool  

This enables MetalLB to assign LoadBalancer IPs using ARP/NDP.

---

## 5. Deployment
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


## 6. Verification

After running the role, verify MetalLB status:

```bash
kubectl get pods -n metallb-system
kubectl get ipaddresspools.metallb.io -n metallb-system
kubectl get l2advertisements.metallb.io -n metallb-system
```

Test LoadBalancer functionality:

```bash
kubectl create deployment nginx --image=nginx
kubectl expose deployment nginx --port=80 --type=LoadBalancer
kubectl get svc nginx
```

You should see an external IP from your configured pool.

---

## 7. Notes

- This role configures **Layer‑2 mode**, compatible with any standard LAN.  
- Ensure the MetalLB IP pool does **not** overlap with DHCP ranges.  
- MetalLB speaker pods must run on **all nodes** for proper ARP/NDP announcements.  
- The role is safe to re‑run; Helm and Kubernetes resources are idempotent.
