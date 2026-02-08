Here’s a professional README for your Ansible playbook that clearly explains its purpose, requirements, and usage:

---

# Deploy Prometheus, Grafana, Loki, and Promtail

This Ansible playbook automates the deployment of **Prometheus**, **Grafana**, and related monitoring components (optionally Loki and Promtail) on a Kubernetes cluster using Helm. It sets up a fully functional monitoring stack in the `monitoring` namespace.

---

## Features

* Adds the **Prometheus community Helm repository**.
* Installs **Prometheus** and **Grafana** via the `kube-prometheus-stack` Helm chart.
* Configures Grafana to use a **LoadBalancer** service on port `3000`.
* Enables **admission webhooks** required by some Prometheus components.
* Waits for all monitoring pods to be ready before completing.

---

## Requirements

* A Kubernetes cluster with **kubectl** configured on the control plane.
* **Helm** installed on the host where the playbook runs.
* **Ansible** installed (tested on Ansible 2.9+).
* The playbook is executed on a **control plane node** with access to the cluster kubeconfig (`/etc/kubernetes/admin.conf`).

---

## Usage

1. Clone the repository or place the playbook on your Ansible control host.

2. Run the playbook with:

```bash
ansible-playbook -i inventory.ini playbook.yml
```

* Ensure the inventory contains the control plane node in the `control_plane` group.

3. The playbook performs the following steps:

* Adds and updates the Prometheus Helm repository.
* Installs or upgrades the `kube-prometheus-stack` Helm chart.
* Configures Grafana with a LoadBalancer service on port 3000.
* Waits for all pods in the `monitoring` namespace to be ready.

---

## Access Grafana

After the playbook completes:

1. Get the Grafana LoadBalancer IP:

```bash
kubectl get svc -n monitoring prometheus-grafana
```

2. Open your browser to `http://<LOADBALANCER_IP>:3000`.

3. Default login credentials (from Helm chart values):

* **Username:** `admin`
* **Password:** `execute command below`
```bash
kubectl get secret prometheus-grafana -n monitoring \
  -o jsonpath='{.data.admin-password}' | base64 -d
```
---

## Notes

* You can customize Helm chart values by modifying the `helm upgrade --install` command in the playbook.
* Ensure your cluster supports **LoadBalancer services**, or modify Grafana to use `NodePort` if needed.

---