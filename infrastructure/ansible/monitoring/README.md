# Monitoring Stack Deployment Guide (Prometheus, Grafana, Loki, Promtail)

This repository provides an automated workflow for deploying a full monitoring and observability stack on a Kubernetes cluster using **Ansible** and **Helm**.  
The playbook installs Prometheus, Grafana, and optional Loki/Promtail components in the `monitoring` namespace and configures Grafana for external access.

---

## 1. Features

The playbook performs the following actions:

- Adds the **Prometheus Community** Helm repository  
- Installs or upgrades the `kube-prometheus-stack` Helm chart  
- Deploys Prometheus, Grafana, Alertmanager, and related CRDs  
- Configures Grafana with a **LoadBalancer** service on port `3000`  
- Enables admission webhooks required by Prometheus components  
- Waits for all monitoring pods to reach Ready state  
- Optionally deploys **Loki** and **Promtail** for log aggregation  

---

## 2. Requirements

Ensure the following prerequisites are met before running the playbook:

- A Kubernetes cluster with access to `/etc/kubernetes/admin.conf`  
- Helm installed on the Ansible control host  
- Ansible installed (tested with Ansible 2.9+)  
- The playbook must run on a control‑plane node with kubeconfig access  

---

## 3. Deployment

Run the playbook from your Ansible control host:

```bash
ansible-playbook playbook.yml
```

The playbook will:

- Add and update the Prometheus Helm repository  
- Install or upgrade the `kube-prometheus-stack` chart  
- Configure Grafana with a LoadBalancer service  
- Wait for all monitoring components to become Ready  

Ensure your inventory defines the control‑plane node under the appropriate group.

---

## 4. Accessing Grafana

After deployment, retrieve the Grafana service details:

### a. Get the LoadBalancer IP

```bash
kubectl get svc -n monitoring prometheus-grafana
```

### b. Access the Web Interface

```
http://<LOADBALANCER_IP>:3000
```

### c. Retrieve the Admin Password

```bash
kubectl get secret prometheus-grafana -n monitoring \
  -o jsonpath='{.data.admin-password}' | base64 -d
```

---

## 5. Notes

- Modify Helm values in the playbook if you need custom dashboards, datasources, or retention settings.  
- If your environment does **not** support LoadBalancer services, switch Grafana to NodePort.  
- Loki and Promtail can be enabled for log aggregation if required.  
- The playbook is idempotent and safe to re‑run.

