# NGINX Ingress Controller Deployment Guide (Ansible + Helm)

This repository provides an automated workflow for deploying the **NGINX Ingress Controller** on a Kubernetes cluster using **Ansible** and **Helm**.  
The playbook installs the ingress controller in a dedicated namespace and configures it to expose traffic through a LoadBalancer service.

---

## 1. Features

The playbook performs the following actions:

- Adds the official **ingress-nginx Helm repository**
- Creates the `ingress-nginx` namespace (configurable)
- Installs or upgrades the **ingress-nginx** Helm chart
- Deploys the controller with a **LoadBalancer** service type
- Enables admission webhooks for advanced Ingress functionality
- Waits for all ingress controller pods to become Ready before completing

---

## 2. Requirements

Ensure the following prerequisites are met before running the playbook:

- A Kubernetes cluster with access to `/etc/kubernetes/admin.conf`
- Helm installed on the Ansible control host
- Ansible installed (tested with Ansible 2.9+)
- The playbook must run on a control‑plane node with kubeconfig access

---

## 3. Variables

Configure the deployment using the following variables:

| Variable            | Default Value                 | Description                                       |
|--------------------|-------------------------------|---------------------------------------------------|
| `ingress_namespace` | `ingress-nginx`               | Namespace where the ingress controller is deployed |
| `ingress_release`   | `ingress-nginx`               | Helm release name                                  |
| `ingress_chart`     | `ingress-nginx/ingress-nginx` | Helm chart reference                               |

Example override file:

```yaml
ingress_namespace: ingress-nginx
ingress_release: ingress-nginx
ingress_chart: ingress-nginx/ingress-nginx
```

---

## 4. Deployment

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

The playbook will:

- Add the Helm repository  
- Install or upgrade the ingress controller  
- Wait for all pods to reach Ready state  

---

## 5. Accessing the Ingress Controller

After deployment, retrieve the LoadBalancer IP:

```bash
kubectl get svc -n ingress-nginx
```

The `controller` service will expose an external IP if your cluster supports LoadBalancer services (e.g., via MetalLB).  
Use this IP to configure DNS or access applications via Ingress resources.

---

## 6. Notes

- If your environment does **not** support LoadBalancer services, switch to NodePort:

  ```yaml
  --set controller.service.type=NodePort
  ```

- Admission webhooks are enabled by default for validation and advanced routing.
- The playbook is idempotent and safe to re‑run.

