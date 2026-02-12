# CTFd Deployment Guide (Kubernetes HA Cluster + Ansible + Helm)

This repository provides an automated workflow for deploying a complete **CTFd** platform on a high‑availability Kubernetes cluster built with **kubeadm**.  
The playbook installs Longhorn for persistent storage, deploys MariaDB and CTFd via Helm, and configures all required namespaces, secrets, and dependencies.

---

## 1. Features

The playbook performs the following actions:

- Deploys **Longhorn** for persistent storage  
- Creates dedicated namespaces for CTFd and MariaDB  
- Installs **MariaDB** using the Bitnami Helm chart  
- Deploys **CTFd** using a custom Helm chart  
- Configures internal Redis (via the CTFd chart)  
- Creates imagePullSecrets for private GitLab registries  
- Waits for all pods and deployments to become Ready  
- Supports configurable service types (LoadBalancer, NodePort, ClusterIP)
- Deploy all web challanges (webshop, webgame)
---

## 2. Prerequisites

Ensure the following components are available before running the playbook:

- A high‑availability Kubernetes cluster deployed via kubeadm  
- `kubectl` and **Helm 3+** installed on the control‑plane node  
- Network access to GitLab Container Registry (if using private images)  
- LoadBalancer support (e.g., MetalLB) for external access  
- Ansible installed on the management host  

---
## 3. Configure Environment Variables  
Rename:

```
example.yml → variables.yml
```

Edit `variables.yml` to match your Proxmox environment.  
Comments in the file explain each variable.

## 4. Deployment Workflow

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

The playbook executes the following steps:

### c. Longhorn Installation

- Creates the `longhorn` namespace  
- Deploys Longhorn using the official manifest  
- Waits for all Longhorn pods to become Ready  
- Verifies that a StorageClass is available  

### d. Namespace Creation

- Creates a dedicated namespace for CTFd and MariaDB (e.g., `ctfd`)

### e. Helm Repository Setup

- Adds the Bitnami Helm repository  
- Updates all Helm repositories  

### f. MariaDB Deployment

- Installs MariaDB using the Bitnami Helm chart  
- Configures:
  - Database name  
  - Username and password  
  - Persistent storage via Longhorn  
- Waits for MariaDB pods to become Ready  

### g. CTFd Deployment

- Copies the custom CTFd Helm chart to the control‑plane node  
- Creates a Kubernetes secret for GitLab registry authentication  
- Installs or upgrades the CTFd Helm chart with:
  - Internal Redis  
  - External MariaDB connection  
  - Persistent storage  
  - Configurable service type  
- Waits for all CTFd pods and deployments to become Ready  

---

## 5. Accessing CTFd

After deployment, verify the CTFd components:

### a. Check Pods

```bash
kubectl get pods -n ctfd
```

### b. Check Service and External IP

```bash
kubectl get svc -n ctfd
```

### c. Access the Web Interface

```
http://<EXTERNAL-IP>:<SERVICE-PORT>
```

### d. View Logs

```bash
kubectl logs -n ctfd -l app.kubernetes.io/name=ctfd -f
```

---

## 6. Variables

The playbook uses a `variables.yml` file for configuration. Key variables include:

| Variable               | Description                   | Example                      |
|------------------------|-------------------------------|------------------------------|
| `kubeconfig_path`      | Path to kubeconfig            | `/etc/kubernetes/admin.conf` |
| `longhorn_namespace`   | Namespace for Longhorn        | `longhorn`                   |
| `ctfd_namespace`       | Namespace for CTFd            | `ctfd`                       |
| `ctfd.release_name`    | Helm release name for CTFd    | `ctfd`                       |
| `ctfd.service_type`    | Kubernetes service type       | `LoadBalancer`               |
| `mariadb.release_name` | Helm release name for MariaDB | `mariadb`                    |
| `mariadb.user`         | Database username             | `ctfd`                       |
| `mariadb.password`     | Database password             | `ctfdpassword`               |
| `mariadb.database`     | Database name                 | `ctfd`                       |
| `storage.class_name`   | StorageClass for PVCs         | `longhorn`                   |

Adjust these variables as needed before running the playbook.

---

## 7. Notes

- CTFd requires MariaDB to be reachable from within the cluster.  
- Redis is deployed internally via the custom CTFd chart.  
- Longhorn is the default StorageClass but can be changed in `variables.yml`.  
- The playbook includes readiness checks to ensure all components are fully operational.  
- Private registry access requires a GitLab `imagePullSecret`.

---

## 8. Cleanup

### a. Remove CTFd and MariaDB

```bash
helm uninstall ctfd -n ctfd
helm uninstall mariadb -n ctfd
kubectl delete namespace ctfd
```

### b. Remove Longhorn

```bash
kubectl delete -f https://raw.githubusercontent.com/longhorn/longhorn/master/deploy/longhorn.yaml
kubectl delete namespace longhorn
```
