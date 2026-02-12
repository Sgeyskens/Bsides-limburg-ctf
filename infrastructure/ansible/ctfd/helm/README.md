### 1️⃣ Current setup

From your `values.yaml`:

```yaml
mariadb:
  # We assume an external MariaDB instance
  host: mariadb.default.svc.cluster.local
  port: 3306
  database: ctfd
  username: ctfd
  password: changeme
  existingSecret: ""
  existingSecretKey: password
```

* The comment explicitly says **“We assume an external MariaDB instance”**.
* No StatefulSet or Deployment for MariaDB exists in your chart.
* Helm will just point CTFd at the hostname you specify (`mariadb.default.svc.cluster.local`).

✅ This is why when you deploy, the CTFd pods start but MariaDB never appears.

---

### 2️⃣ How to deploy MariaDB with the chart

There are two approaches:

#### Option A: Use a **built-in MariaDB Helm chart** as a dependency

1. Add a `Chart.yaml` dependency:

```yaml
dependencies:
  - name: mariadb
    version: 12.1.1   # example version
    repository: https://charts.bitnami.com/bitnami
```

2. Update `values.yaml` to configure it:

```yaml
mariadb:
  auth:
    rootPassword: ctfdrootpassword
    username: ctfd
    password: ctfdpassword
    database: ctfd
  primary:
    persistence:
      enabled: true
      storageClass: longhorn
      size: 8Gi
```

3. Now when you install your CTFd chart, Helm will also install MariaDB automatically.

---

#### Option B: Deploy MariaDB **separately**, outside of the chart

```bash
helm repo add bitnami https://charts.bitnami.com/bitnami
helm install mariadb bitnami/mariadb \
  --namespace ctfd --create-namespace \
  --set auth.username=ctfd \
  --set auth.password=ctfdpassword \
  --set auth.database=ctfd \
  --set primary.persistence.storageClass=longhorn \
  --set primary.persistence.size=8Gi
```

* Then point your CTFd `values.yaml` at this database:

```yaml
mariadb:
  host: mariadb.ctfd.svc.cluster.local
  port: 3306
  username: ctfd
  password: ctfdpassword
  database: ctfd
```
