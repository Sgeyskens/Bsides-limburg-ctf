
# MetalLB Deployment Role (Ansible)

This role automates the installation and configuration of MetalLB on a Kubernetes cluster using Helm.  
It is designed for kubeadm‑based clusters and deploys MetalLB in Layer 2 mode using the modern CRD‑based configuration (`IPAddressPool` and `L2Advertisement`).

The role runs only once, on the first control plane node.

---

## 1. Purpose

This role provides a fully automated and idempotent MetalLB deployment that:

- Installs required Python and Kubernetes libraries.
- Installs Helm if not already present.
- Adds the MetalLB Helm repository.
- Installs or upgrades the MetalLB Helm chart.
- Waits for controller and speaker pods to become ready.
- Configures an IPAddressPool for Layer 2 load balancing.
- Configures an L2Advertisement referencing the pool.
- Verifies the deployment by retrieving pod status.

This enables Kubernetes Services of type `LoadBalancer` to receive external IPs on bare‑metal clusters.

---

## 2. Requirements

### 2.1 Ansible Collections

This role requires:

- `kubernetes.core`

Install it with:

```
ansible-galaxy collection install kubernetes.core
```

### 2.2 Kubernetes Access

The target host must have:

- `/etc/kubernetes/admin.conf` available  
- Permissions to run `kubectl` and Helm  
- Network access to the Kubernetes API  

### 2.3 Inventory

Your inventory must define a `control_plane` group.  
The role runs on the first host in that group:

```ini
[control_plane]
192.168.1.[10:12]
```

---

## 3. Variables

The following variables control the MetalLB configuration:

| Variable | Description | Default |
|----------|-------------|---------|
| `metallb_namespace` | Namespace where MetalLB is deployed | `metallb-system` |
| `metallb_ip_pool_start` | First IP in the MetalLB pool | `192.168.1.100` |
| `metallb_ip_pool_end` | Last IP in the MetalLB pool | `192.168.1.199` |

Ensure the IP range is:

- Not used by DHCP  
- Within the same L2 broadcast domain as your nodes  
- Not assigned to any host  

---

## 4. Role Behavior

### 4.1 Package Installation

Installs:

- `python3-pip`
- `python3-kubernetes`

These are required for the `kubernetes.core` modules.

### 4.2 Helm Installation

Downloads and installs Helm using the official script.  
Idempotency is ensured using:

```
creates: /usr/local/bin/helm
```

### 4.3 Helm Repository Management

Adds the MetalLB Helm repository:

```
https://metallb.github.io/metallb
```

### 4.4 MetalLB Deployment

Installs or upgrades the MetalLB Helm chart:

- Namespace: `metallb-system`
- Waits for resources to become ready
- Creates namespace if missing

### 4.5 Pod Readiness Checks

The role waits for:

- The MetalLB controller pod  
- At least one MetalLB speaker pod  

This ensures MetalLB is operational before applying configuration.

### 4.6 CRD‑Based Configuration

Applies:

1. **IPAddressPool**  
   Defines the range of IPs MetalLB may assign.

2. **L2Advertisement**  
   Enables Layer 2 mode for the pool.

### 4.7 Verification

Retrieves and displays the list of MetalLB pods.

---

## 5. Usage

Run the playbook that includes this role:

```
ansible-playbook -i inventory.ini playbook.yml
```

Dry run:

```
ansible-playbook -i inventory.ini playbook.yml --check
```

---

## 6. Post‑Deployment Verification

### 6.1 Check MetalLB Pods

```
kubectl get pods -n metallb-system
```

Expected:

- One controller pod  
- One speaker pod per node  

### 6.2 Check Custom Resources

```
kubectl get ipaddresspools.metallb.io -n metallb-system
kubectl get l2advertisements.metallb.io -n metallb-system
```

### 6.3 Test LoadBalancer Service

Deploy a test service:

```
kubectl expose deployment nginx --port=80 --type=LoadBalancer
```

Check assigned IP:

```
kubectl get svc nginx
```

The external IP should fall within your configured pool.

---

## 7. Troubleshooting

### Controller or Speaker Pods Not Ready

Check logs:

```
kubectl logs -n metallb-system -l app.kubernetes.io/component=controller
kubectl logs -n metallb-system -l app.kubernetes.io/component=speaker
```

### No External IP Assigned

Possible causes:

- IP range overlaps with DHCP  
- Speaker pods not running on all nodes  
- ARP blocked by network equipment  
- L2Advertisement missing or misconfigured  

### Helm Not Found

Ensure `/usr/local/bin` is in PATH.

---

## 8. Notes

- The role is fully idempotent and safe to re-run.
- It uses the recommended CRD‑based configuration (MetalLB v0.13+).
- It avoids deprecated ConfigMap‑based configuration.
- It runs only once using `run_once: true` to prevent redundant Helm operations.
