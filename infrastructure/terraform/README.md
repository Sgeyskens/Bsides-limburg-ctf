# Skill 3 Project
# Terraform

1. Create new Terraform role for terraform user: 
    1. datacenter → permissions → user→ create:
        
        Username: Terraform@pve
        
        Password: SzRpGvFVw@$5cJSBYfvqJj495qrUBE
        
    2. datacenter → permissions → roles → create:
        
        name: Terraform
        
        ```jsx
        Datastore.AllocateSpace Datastore.Audit Pool.Allocate Sys.Audit Sys.Console Sys.Modify VM.Allocate VM.Audit VM.Clone VM.Config.CDROM VM.Config.Cloudinit VM.Config.CPU VM.Config.Disk VM.Config.HWType VM.Config.Memory VM.Config.Network VM.Config.Options VM.Migrate VM.Monitor VM.PowerMgmt SDN.Use
        ```
        
    3. datacenter → permissions → add → user permissions:
        
        Path: /
        
        User: Terraform@pve
        
        Role: Terraform
        
    
    Or execute in Proxmox CLI: 
    
    ```jsx
    pveum role add Terraform -privs "Datastore.AllocateSpace Datastore.Audit Pool.Allocate Sys.Audit Sys.Console Sys.Modify VM.Allocate VM.Audit VM.Clone VM.Config.CDROM VM.Config.Cloudinit VM.Config.CPU VM.Config.Disk VM.Config.HWType VM.Config.Memory VM.Config.Network VM.Config.Options VM.Migrate VM.Monitor VM.PowerMgmt SDN.Use"
    pveum user add Terraform@pve --password <password>
    pveum aclmod / -user terraform@pve -role Terraform
    ```
    
    d.   datacenter → permissions → API token → add:
    
    user: Terraform@pve
    
    TokenID: TerraformToken
    
    uncheck privileges separation
    
    **!!! copy secrets to clipboard!!!**
    
    Secret: 8b53e475-65cf-49a9-ac34-3d13172e80db
    
2. configure cloud image
    1. install required cloud image:
        
        Local (pve) → iso images → download from URL
        
        URL: [https://cloud-images.ubuntu.com/noble/current/noble-server-cloudimg-amd64.img](https://cloud-images.ubuntu.com/noble/current/noble-server-cloudimg-amd64.img)
        
        filename: “ubuntu”
        
        click “Query URL”
        
        click “Download”
        
    2. enter Proxmox CLI and execute:
        
        ```bash
        qm create 7000 --memory 2048 --cores 2 --name ubuntu-cloud --net0 virtio,bridge=vmbr0
        cd /var/lib/vz/template/iso
        qm importdisk 7000 noble-server-cloudimg-amd64.img local-lvm
        qm set 7000 --scsihw virtio-scsi-single --scsi0 local-lvm:vm-7000-disk-0
        qm set 7000 --ide2 local-lvm:cloudinit
        qm set 7000 --boot order=scsi0
        ```
        
3. Terraform 
    1. Install terraform on host system
        
        [https://developer.hashicorp.com/terraform/install](https://developer.hashicorp.com/terraform/install)
        
    2. in directory where terraform files execute 
        
        ```bash
        terraform init
        terraform plan -var-file=".vars.tfvars"
        terraform apply -var-file=".vars.tfvars"
        terraform output -json > tf_output.json
        python3 main.py
        ```
        
        ```bash
        terraform destroy -auto-approve -var-file=".vars.tfvars"

        ```