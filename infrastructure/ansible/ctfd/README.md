# CTFd Deployment on Kubernetes (kubeadm HA Cluster)

This Ansible playbook deploys a **CTFd** (Capture The Flag platform) environment on a Kubernetes cluster with the following components:

* **CTFd** (custom Helm chart)
* **MariaDB** (Bitnami Helm chart)
* **Redis** (internal, via the custom CTFd chart)
* **Longhorn** for persistent storage
* Configurable **namespaces** and **service types**

---

## Features

* Deploy CTFd on a high-availability kubeadm cluster
* Automated setup of **Longhorn** for persistent volumes
* Helm-based deployment of **MariaDB** and **CTFd**
* Configurable Redis backend
* Persistent storage for CTFd uploads
* Automatic waits for pods and deployments to become ready

---

## Prerequisites

* **Kubernetes cluster** (HA kubeadm)
* **kubectl** and **Helm 3+** installed on the control plane node
* Network access to **GitLab Container Registry** (if using private CTFd image)
* Optional: MetalLB or other LoadBalancer support for external access

---

## Deployment Steps

The playbook performs the following steps:

### 1. Longhorn Installation

* Creates the `longhorn` namespace
* Deploys Longhorn via official manifest
* Waits for Longhorn pods to be ready
* Verifies that a **StorageClass** is available

### 2. Namespace Creation

* Creates a dedicated namespace for CTFd and MariaDB (e.g., `ctfd`)

### 3. Helm Repository Setup

* Adds the Bitnami Helm repository
* Updates all Helm repositories

### 4. MariaDB Deployment

* Deploys MariaDB via Bitnami Helm chart in the CTFd namespace
* Configures username, password, database, and persistent storage
* Waits for MariaDB pods to be ready

### 5. CTFd Deployment

* Copies custom CTFd Helm chart to the control plane node
* Creates a Kubernetes secret for pulling images from GitLab Container Registry
* Installs or upgrades the CTFd Helm chart with:

  * Redis (internal)
  * External MariaDB connection
  * Persistent storage via Longhorn
  * Configurable service type (ClusterIP, LoadBalancer, NodePort)
* Waits for CTFd deployment and pods to become ready

### 6. Access Information

After deployment:

* Check CTFd pods:

```bash
kubectl get pods -n ctfd
```

* Check CTFd service and external IP:

```bash
kubectl get svc -n ctfd
```

* Access CTFd via:

```
http://<EXTERNAL-IP>:<SERVICE-PORT>
```

* View logs for debugging:

```bash
kubectl logs -n ctfd -l app.kubernetes.io/name=ctfd -f
```

---

## Variables

The playbook uses a `variables.yml` file for configuration. Key variables include:

| Variable               | Description                   | Example                      |
| ---------------------- | ----------------------------- | ---------------------------- |
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

> You can adjust these variables for your environment before running the playbook.

---

## Notes

* CTFd requires **MariaDB** to be reachable from the cluster.
* Internal Redis is deployed automatically via the custom CTFd Helm chart.
* Persistent storage uses Longhorn by default; you can change the StorageClass in `variables.yml`.
* The playbook includes wait tasks to ensure all components are ready before proceeding.
* Private container registry access requires a GitLab imagePullSecret.

---

## Cleanup

To uninstall CTFd and MariaDB:

```bash
helm uninstall ctfd -n ctfd
helm uninstall mariadb -n ctfd
kubectl delete namespace ctfd
```

To uninstall Longhorn:

```bash
kubectl delete -f https://raw.githubusercontent.com/longhorn/longhorn/master/deploy/longhorn.yaml
kubectl delete namespace longhorn
```

---

This README covers **everything needed to deploy a complete CTFd environment** on a kubeadm HA cluster with persistent storage and external MariaDB.