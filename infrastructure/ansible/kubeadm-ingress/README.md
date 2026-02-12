# NGINX Ingress Controller Deployment with Helm

This Ansible playbook automates the installation of the **NGINX Ingress Controller** on a Kubernetes cluster using Helm. It sets up a fully functional Ingress controller in its own namespace, ready to route traffic to your services.

---

## Features

* Adds the **official ingress-nginx Helm repository**.
* Creates a dedicated **namespace** (`ingress-nginx`) for the controller.
* Installs or upgrades the **ingress-nginx Helm chart**.
* Configures the controller with a **LoadBalancer service**.
* Enables **admission webhooks** required for certain Ingress features.
* Waits for all Ingress controller pods to be ready before completing.

---

## Requirements

* A Kubernetes cluster with `kubectl` access on the control plane.
* **Helm** installed on the host running the playbook.
* **Ansible** installed (tested on Ansible 2.9+).
* The playbook runs on a **control plane node** with access to the kubeconfig (`/etc/kubernetes/admin.conf`).

---

## Usage

1. Clone the repository or place the playbook on your Ansible control host.

2. Run the playbook:

```bash
ansible-playbook -i inventory.ini playbook.yml
```

* Ensure your inventory contains the control plane node under the `control_plane` group.

---

## Variables

| Variable            | Default Value                 | Description                                       |
| ------------------- | ----------------------------- | ------------------------------------------------- |
| `ingress_namespace` | `ingress-nginx`               | Namespace to deploy the ingress controller        |
| `ingress_release`   | `ingress-nginx`               | Helm release name for the ingress deployment      |
| `ingress_chart`     | `ingress-nginx/ingress-nginx` | Helm chart for ingress controller                 |
| `kubeconfig_path`   | `/etc/kubernetes/admin.conf`  | Path to kubeconfig used to connect to the cluster |

---

## Access the Ingress Controller

After deployment:

1. Get the LoadBalancer IP of the NGINX controller:

```bash
kubectl get svc -n ingress-nginx
```

2. The `controller` service will expose an **external IP** (if your cluster supports LoadBalancers). Use this IP to configure DNS or access your services via Ingress.

---

## Notes

* If your cluster does **not support LoadBalancer services**, you can change:

```yaml
--set controller.service.type=NodePort
```

* The playbook **waits** for all pods to be ready before completing.
* Admission webhooks are enabled by default to allow advanced routing and validation features.

---
