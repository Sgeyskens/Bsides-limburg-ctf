Here’s a polished, clear README tailored specifically for the **MetalLB Ansible role** you shared.  
It explains *what the role does*, *how it works*, and *how to use it*, without overwhelming the reader.

You can drop this directly into:

```
roles/metallb/README.md
```

---

# **MetalLB Ansible Role**

This Ansible role installs and configures **MetalLB** on a kubeadm‑based Kubernetes cluster.  
It uses Helm for installation, ensures all MetalLB components are ready, and applies a Layer‑2 IP address pool for LoadBalancer services.

The role is designed for **bare‑metal** or **on‑prem** clusters where no cloud LoadBalancer is available.

---

## **What This Role Does**

This role performs the following actions:

- Installs required Python packages for Kubernetes modules  
- Installs Helm (idempotent)  
- Adds the MetalLB Helm repository  
- Installs or upgrades the MetalLB Helm chart  
- Waits for MetalLB controller and speaker pods to become ready  
- Configures:
  - `IPAddressPool` (Layer 2 address pool)
  - `L2Advertisement` (ARP/NDP announcements)
- Waits for all cluster pods to reach the Ready state  
- Outputs MetalLB pod status for verification  

---

## **Requirements**

- Kubernetes cluster installed via kubeadm  
- Control plane reachable via `/etc/kubernetes/admin.conf`  
- Ansible collections:
  - `kubernetes.core`
- A free IP range on your LAN for MetalLB to allocate  
- Variables defined in `variables.yml` (see below)

---

## **Role Variables**

These variables must be defined in `variables.yml` or passed via inventory.

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

## **How It Works**

### **1. Helm Installation**
The role installs Helm only if it is not already present:

- Downloads the official Helm installer script  
- Executes it  
- Removes the script afterward  

### **2. MetalLB Deployment**
MetalLB is installed via Helm:

```yaml
kubernetes.core.helm:
  name: metallb
  chart_ref: metallb/metallb
  release_namespace: metallb-system
  create_namespace: true
  wait: true
```

### **3. Readiness Checks**
The role waits for:

- MetalLB **controller** pod  
- MetalLB **speaker** pods  

This ensures MetalLB is fully operational before applying configuration.

### **4. Layer‑2 Configuration**
The role applies:

- An `IPAddressPool` with your configured IP range  
- An `L2Advertisement` referencing that pool  

This enables MetalLB to assign LoadBalancer IPs using ARP/NDP.

---

## **Example Usage**

In your playbook:

```yaml
- hosts: masters[0]
  roles:
    - metallb
```

Ensure `variables.yml` is included in your playbook or inventory.

---

## **Verifying the Deployment**

After running the role:

```bash
kubectl get pods -n metallb-system
kubectl get ipaddresspools.metallb.io -n metallb-system
kubectl get l2advertisements.metallb.io -n metallb-system
```

To test MetalLB:

```bash
kubectl create deployment nginx --image=nginx
kubectl expose deployment nginx --port=80 --type=LoadBalancer
kubectl get svc nginx
```

You should see an external IP from your configured pool.

---

## **Notes**

- This role uses **Layer 2 mode**, which works on any standard LAN.  
- Ensure your IP pool does **not** overlap with DHCP ranges.  
- MetalLB must run on **all nodes** for proper ARP/NDP announcements.  
- This role is safe to re‑run; Helm and Kubernetes resources are idempotent.
