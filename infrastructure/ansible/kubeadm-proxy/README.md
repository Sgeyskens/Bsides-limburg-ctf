# HAProxy Load Balancer Deployment Guide (Ansible)

This repository provides an automated workflow for deploying **HAProxy** as a load balancer for a **high‑availability Kubernetes control plane** using **Ansible**.  
Follow the steps below to prepare your environment, run the playbook, and verify the load balancer.

---

## 1. Requirements

Ensure the following components are available before running the playbook:

- An Ubuntu host that will run **HAProxy**
- An Ansible inventory defining:
  - `haproxy` — the load balancer node
  - `masters` — Kubernetes control plane nodes

---

## 2. Deployment

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

### c. Playbook Actions

The playbook performs the following tasks:

- Installs **HAProxy** and **UFW**
- Configures firewall rules for Kubernetes API traffic
- Deploys the HAProxy configuration file
- Validates configuration using:

  ```bash
  haproxy -c -f /etc/haproxy/haproxy.cfg
  ```

- Enables and starts the HAProxy service

---

## 3. Verification

### a. Check HAProxy Service Status

```bash
sudo systemctl status haproxy
```

### b. Test Kubernetes API Through Load Balancer

```bash
curl -k https://<haproxy-ip>:6443/version
```

### c. Validate Failover Behavior

Stop the API server on one master:

```bash
sudo systemctl stop kube-apiserver
```

Test health endpoint:

```bash
curl -k https://<haproxy-ip>:6443/healthz
```

---

## 4. Troubleshooting

Common issues and resolutions:

### a. Stats Page Not Accessible  
Check firewall rules (UFW/iptables) and any upstream network ACLs.

### b. HAProxy Reload Fails  
Validate configuration manually:

```bash
sudo haproxy -c -f /etc/haproxy/haproxy.cfg
```

### c. Kubernetes API Unreachable  
Verify:

- IPs in the Ansible inventory
- `kube-apiserver` status on master nodes
- HAProxy logs:

  ```bash
  sudo journalctl -u haproxy -f
  ```

- Port bindings:

  ```bash
  ss -tulpn | grep 6443
  ```

---