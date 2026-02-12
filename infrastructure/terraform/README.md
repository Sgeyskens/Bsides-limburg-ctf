Here’s a refined, professional, and security‑safe README based on the content you provided.  
I’ve kept all the technical steps, improved clarity, and removed sensitive credentials so the file is safe to publish on GitHub.

If you want, I can also generate a **secure private version** that keeps the real secrets.

---

# Terraform Proxmox Deployment Guide

This repository provides a complete workflow for deploying virtual machines on **Proxmox VE** using **Terraform** and a prepared **Ubuntu cloud image**.  
Follow the steps below to configure Proxmox, prepare the cloud image, and deploy infrastructure using Terraform.

---

## 1. Create Terraform Role & User in Proxmox

### a. Create a Terraform User  
Navigate to:

**Datacenter → Permissions → Users → Create**

- **Username:** `Terraform@pve`  
- **Password:** *(choose a secure password)*

### b. Create a Terraform Role  
Go to:

**Datacenter → Permissions → Roles → Create**

- **Name:** `Terraform`
- **Privileges:**

```
Datastore.AllocateSpace
Datastore.Audit
Pool.Allocate
Sys.Audit
Sys.Console
Sys.Modify
VM.Allocate
VM.Audit
VM.Clone
VM.Config.CDROM
VM.Config.Cloudinit
VM.Config.CPU
VM.Config.Disk
VM.Config.HWType
VM.Config.Memory
VM.Config.Network
VM.Config.Options
VM.Migrate
VM.Monitor
VM.PowerMgmt
SDN.Use
```

### c. Assign Role to User  
Navigate to:

**Datacenter → Permissions → Add**

- **Path:** `/`
- **User:** `Terraform@pve`
- **Role:** `Terraform`

### d. Create API Token  
Go to:

**Datacenter → Permissions → API Tokens → Add**

- **User:** `Terraform@pve`
- **Token ID:** `TerraformToken`
- **Privileges Separation:** *unchecked*

**Important:** Copy the generated **Token Secret** and store it securely.

---

### Optional: Create User/Role via CLI

```bash
pveum role add Terraform -privs "Datastore.AllocateSpace Datastore.Audit Pool.Allocate Sys.Audit Sys.Console Sys.Modify VM.Allocate VM.Audit VM.Clone VM.Config.CDROM VM.Config.Cloudinit VM.Config.CPU VM.Config.Disk VM.Config.HWType VM.Config.Memory VM.Config.Network VM.Config.Options VM.Migrate VM.Monitor VM.PowerMgmt SDN.Use"

pveum user add Terraform@pve --password <password>

pveum aclmod / -user Terraform@pve -role Terraform
```

---

## 2. Prepare the Ubuntu Cloud Image

### a. Download Cloud Image  
Navigate to:

**Local (pve) → ISO Images → Download from URL**

- **URL:**  
  `https://cloud-images.ubuntu.com/noble/current/noble-server-cloudimg-amd64.img` [(cloud-images.ubuntu.com in Bing)](https://www.bing.com/search?q="https%3A%2F%2Fcloud-images.ubuntu.com%2Fnoble%2Fcurrent%2Fnoble-server-cloudimg-amd64.img")  
- **Filename:** `ubuntu`

Click **Query URL**, then **Download**.

### b. Create Cloud-Init Template via CLI

```bash
qm create 7000 --memory 2048 --cores 2 --name ubuntu-cloud --net0 virtio,bridge=vmbr0
cd /var/lib/vz/template/iso
qm importdisk 7000 noble-server-cloudimg-amd64.img local-lvm
qm set 7000 --scsihw virtio-scsi-single --scsi0 local-lvm:vm-7000-disk-0
qm set 7000 --ide2 local-lvm:cloudinit
qm set 7000 --boot order=scsi0
```

This creates a reusable VM template for Terraform deployments.

---

## 3. Terraform Setup

### a. Install Terraform  
Follow the official installation guide:  
[https://developer.hashicorp.com/terraform/install](https://developer.hashicorp.com/terraform/install)

### b. Configure Environment Variables  
Rename:

```
env.example → env.tfvars
```

Edit `env.tfvars` to match your Proxmox environment.  
Comments in the file explain each variable.

### c. Deploy Infrastructure

Run the following inside the Terraform directory:

```bash
terraform init
terraform plan -var-file="env.tfvars"
terraform apply -var-file="env.tfvars"
terraform output -json > tf_output.json
sudo python3 main.py
```

### d. Destroy Infrastructure

```bash
terraform destroy -auto-approve -var-file="env.tfvars"
```

---

## Notes

- Never commit API tokens, passwords, or secrets to GitHub.
- Ensure your Proxmox host has cloud-init support enabled.
- The `main.py` script likely processes Terraform output—ensure Python 3 is installed.