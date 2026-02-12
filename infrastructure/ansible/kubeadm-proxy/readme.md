Here’s a concise, purely technical version of your README:

---

# HAProxy Deployment for Kubernetes Control Plane (Ansible)

Ansible playbook to deploy HAProxy as a load balancer for a kubeadm HA Kubernetes control plane.

---

## 1. Requirements

* Ubuntu host for HAProxy
* Ansible inventory defining:

  * `haproxy` (load balancer)
  * `masters` (control plane nodes)

Example:

```ini
[asters]
192.168.1.10
192.168.1.11
192.168.1.12

[workers]
192.168.1.20
192.168.1.21

[haproxy]
192.168.1.30
```

---

## 2. Variables

| Variable                | Default  | Description             |
| ----------------------- | -------- | ----------------------- |
| `haproxy_frontend_port` | 6443     | Kubernetes API port     |


---

## 3. Deployment

Dry run:

```bash
ansible-playbook  playbook.yml --check
```

Apply configuration:

```bash
ansible-playbook  playbook.yml
```

Playbook actions:

* Install HAProxy and UFW
* Configure firewall rules
* Deploy HAProxy configuration
* Validate configuration (`haproxy -c -f /etc/haproxy/haproxy.cfg`)
* Enable and start HAProxy

---

## 4. Verification

Check service:

```bash
sudo systemctl status haproxy
```

Test Kubernetes API:

```bash
curl -k https://<haproxy-ip>:6443/version
```

Check failover:

```bash
sudo systemctl stop kube-apiserver
curl -k https://<haproxy-ip>:6443/healthz
```

---

## 5. Troubleshooting

* Stats page blocked → check UFW/iptables and network ACLs
* HAProxy reload fails → validate config manually:

  ```bash
  sudo haproxy -c -f /etc/haproxy/haproxy.cfg
  ```
* Kubernetes API unreachable → verify inventory IPs, kube-apiserver status, HAProxy logs:

  ```bash
  sudo journalctl -u haproxy -f

  ss -tulpn | grep 6443
  ```

---