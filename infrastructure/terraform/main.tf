################################################################################
# Terraform Configuration for a Kubernetes Cluster on Proxmox
#
# This configuration provisions:
# - Kubernetes Control Plane (Master) nodes
# - Kubernetes Worker nodes
# - A HAProxy load balancer node
#
# All virtual machines are created from a cloud-init enabled template.
################################################################################

###############################################################################
# VARIABLES
###############################################################################

###############################################################################
# Proxmox API & Cluster Settings
###############################################################################

variable "pm_hosts" {
  description = "List of Proxmox node names where VMs can be scheduled. Index-based selection is used."
  type        = list(string)
}

variable "pm_api_url" {
  description = "Proxmox API endpoint URL (e.g. https://pve.example.com:8006/api2/json)"
  type        = string
}

variable "pm_api_token_id" {
  description = "Proxmox API token ID in the format: user@realm!token-name"
  type        = string
}

variable "pm_api_token_secret" {
  description = "Secret associated with the Proxmox API token"
  type        = string
  sensitive   = true
}

variable "template_name" {
  description = "Name of the cloud-init enabled VM template used for cloning"
  type        = string
  default     = "ubuntu-cloud"
}

variable "SSH_public_key" {
  description = "SSH public key injected into all VMs via cloud-init"
  type        = string
  sensitive   = true
}

###############################################################################
# Network Configuration
###############################################################################

variable "network" {
  description = "Base network address (first three octets), e.g. 192.168.1"
  type        = string
}

variable "subnet_mask" {
  description = "Subnet mask in CIDR notation (e.g. /24)"
  type        = string
}

variable "gateway" {
  description = "Default gateway for all virtual machines"
  type        = string
}

variable "disabled_ipv6" {
  description = "Disable IPv6 on all VMs (true = disabled, false = enabled)"
  type        = bool
  default     = true
}

variable "dns_servers" {
  description = "DNS server IP addresses separated by spaces"
  type        = string
}

###############################################################################
# Kubernetes Master (Control Plane) Configuration
###############################################################################

variable "ms_count" {
  description = "Number of Kubernetes master (control-plane) nodes"
  type        = number
}

variable "ms_vmid" {
  description = "Starting VMID for master nodes (incremented by count index)"
  type        = number
}

variable "ms_cpu_cores" {
  description = "Number of CPU cores allocated to each master node"
  type        = number
}

variable "ms_cpu_sockets" {
  description = "Number of CPU sockets allocated to each master node"
  type        = number
}

variable "ms_memory" {
  description = "Amount of RAM (in MB) allocated to each master node"
  type        = number
}

variable "ms_storage_size" {
  description = "Disk size allocated to each master node (e.g. 50G)"
  type        = string
}

variable "ms_network_interface" {
  description = "Proxmox bridge/interface used by master nodes (e.g. vmbr1)"
  type        = string
}

variable "ms_ci_user" {
  description = "Cloud-init username for master nodes"
  type        = string
  default     = "master"
}

variable "ms_ci_password" {
  description = "Cloud-init password for master nodes (use SSH keys where possible)"
  type        = string
  sensitive   = true
  default     = "NotSecure"
}

###############################################################################
# Kubernetes Worker Configuration
###############################################################################

variable "sl_count" {
  description = "Number of Kubernetes worker nodes"
  type        = number
}

variable "sl_vmid" {
  description = "Starting VMID for worker nodes (incremented by count index)"
  type        = number
}

variable "sl_cpu_cores" {
  description = "Number of CPU cores allocated to each worker node"
  type        = number
}

variable "sl_cpu_sockets" {
  description = "Number of CPU sockets allocated to each worker node"
  type        = number
}

variable "sl_memory" {
  description = "Amount of RAM (in MB) allocated to each worker node"
  type        = number
}

variable "sl_storage_size" {
  description = "Disk size allocated to each worker node (e.g. 75G)"
  type        = string
}

variable "sl_network_interface" {
  description = "Proxmox bridge/interface used by worker nodes"
  type        = string
}

variable "sl_ci_user" {
  description = "Cloud-init username for worker nodes"
  type        = string
  default     = "worker"
}

variable "sl_ci_password" {
  description = "Cloud-init password for worker nodes"
  type        = string
  sensitive   = true
  default     = "NotSecure"
}

###############################################################################
# HAProxy Load Balancer Configuration
###############################################################################

variable "pr_count" {
  description = "Number of HAProxy nodes (usually 1)"
  type        = number
}

variable "pr_vmid" {
  description = "Starting VMID for HAProxy node(s)"
  type        = number
}

variable "pr_cpu_cores" {
  description = "Number of CPU cores allocated to the HAProxy node"
  type        = number
}

variable "pr_cpu_sockets" {
  description = "Number of CPU sockets allocated to the HAProxy node"
  type        = number
}

variable "pr_memory" {
  description = "Amount of RAM (in MB) allocated to the HAProxy node"
  type        = number
}

variable "pr_storage_size" {
  description = "Disk size allocated to the HAProxy node (e.g. 25G)"
  type        = string
}

variable "pr_network_interface" {
  description = "Proxmox bridge/interface used by the HAProxy node"
  type        = string
}

variable "pr_ci_user" {
  description = "Cloud-init username for the HAProxy node"
  type        = string
  default     = "proxy"
}

variable "pr_ci_password" {
  description = "Cloud-init password for the HAProxy node"
  type        = string
  sensitive   = true
  default     = "NotSecure"
}

###############################################################################
# Storage Configuration
###############################################################################

variable "storage_location" {
  description = "Proxmox storage backend where VM disks are created (e.g. local-lvm)"
  type        = string
}

variable "cloud_image_location" {
  description = "Proxmox storage backend containing the cloud-init image/template"
  type        = string
  default     = "storage"
}

###############################################################################
# Terraform & Provider Configuration
###############################################################################

terraform {
  required_providers {
    proxmox = {
      source  = "telmate/proxmox"
      version = "3.0.2-rc07"
    }
  }
}

provider "proxmox" {
  pm_api_url          = var.pm_api_url
  pm_api_token_id     = var.pm_api_token_id
  pm_api_token_secret = var.pm_api_token_secret
  pm_tls_insecure     = true
  pm_parallel         = 2
}

# ==============================================================================
# KUBERNETES MASTER NODES - Kubernetes control plane (3 instances)
# ==============================================================================
# Purpose: Run etcd, API Server, Controller Manager, Scheduler
# IPs: 192.168.1.10 - 192.168.1.12
# Resources: 3 cores, 6GB RAM, 50GB disk per node

resource "proxmox_vm_qemu" "Kubernetes-Master" {
  name        = "Kubernetes-Master-${count.index}"  # VM name: k8s-master-0, k8s-master-1, k8s-master-2
  vmid        = 200 + count.index                   # VMID: 200, 201, 202
  count       = var.ms_count                                   # Create 3 master nodes
  target_node = var.pm_hosts[count.index]           # Proxmox node to deploy on
  clone       = var.template_name                   # Clone from cloud image template
  boot        = "order=scsi0"                       # Boot from SCSI disk 0
  agent       = 1                                   # Enable Proxmox agent

  os_type     = "cloud-init"                        # Cloud-init OS type for cloud image support
  cpu {
    cores   = var.ms_cpu_cores                                     # 3 CPU cores per node
    sockets = var.ms_cpu_sockets                                     # Single socket
    type    = "host"                                # Use host CPU type for performance
  }
  memory           = var.ms_memory                           # 6GB RAM per node
  scsihw           = "virtio-scsi-pci"              # High-performance SCSI controller
  ciupgrade        = true                           # Auto-upgrade cloud-init packages
  vm_state         = "running"                      # Keep VM running
  automatic_reboot = true                           # Reboot on kernel updates

  ipconfig0  = "ip=${var.network}.${10 + count.index}${var.subnet_mask},gw=${var.gateway}"  # Static IP assignment
  skip_ipv6  = var.disabled_ipv6                                 # Disable IPv6
  nameserver = var.dns_servers                   # DNS servers (Cloudflare, Google)
  sshkeys = var.SSH_public_key                      # SSH public key for authentication
  serial {
    id   = 0                                        # Serial port 0
    type = "socket"                                 # Socket-based serial connection
  }

  disks {
    ide {
      ide2 {
        cloudinit {
          storage = var.cloud_image_location                      # Cloud-init config storage location
        }
      }
    }
    scsi {
      scsi0 {
        disk {
          storage = var.storage_location                       # Main disk storage location
          size    = var.ms_storage_size                           # Main disk size (50GB)
        }
      }
    }
  }

  network {
    id     = 0                                      # Network device 0
    bridge = var.ms_network_interface                                # Bridge to vmbr1 (internal network)
    model  = "virtio"                               # High-performance virtio network model
  }

  ciuser     = var.ms_ci_user                              # Cloud-init user
  cipassword = var.ms_ci_password                         # Cloud-init password
}

# ==============================================================================
# KUBERNETES WORKER NODES - Container runtime & workloads (3 instances)
# ==============================================================================
# Purpose: Run application containers and Kubernetes node components
# IPs: 192.168.1.20 - 192.168.1.22
# Resources: 7 cores, 16GB RAM, 75GB disk per node

resource "proxmox_vm_qemu" "Kubernetes-Worker" {
    depends_on = [
    proxmox_vm_qemu.Kubernetes-Master  # Wait for masters to be created first
  ]

  name        = "Kubernetes-Worker-${count.index}"  # VM name: k8s-worker-0, k8s-worker-1, k8s-worker-2
  vmid        = 300 + count.index                   # VMID: 300, 301, 302
  count       = var.sl_count                                  # Create 3 worker nodes
  target_node = var.pm_hosts[count.index]           # Proxmox node to deploy on
  clone       = var.template_name                   # Clone from cloud image template
  boot        = "order=scsi0"                       # Boot from SCSI disk 0
  agent       = 1                                   # Enable Proxmox agent

  os_type     = "cloud-init"                        # Cloud-init OS type for cloud image support
  cpu {
    cores   = var.sl_cpu_cores                                    # 7 CPU cores per worker (more for workloads)
    sockets = var.sl_cpu_sockets                                     # Single socket
    type    = "host"                                # Use host CPU type for performance
  }
  memory           = var.sl_memory                          # 16GB RAM per worker (more for containers)
  scsihw           = "virtio-scsi-pci"              # High-performance SCSI controller
  ciupgrade        = true                           # Auto-upgrade cloud-init packages
  vm_state         = "running"                      # Keep VM running
  automatic_reboot = true                           # Reboot on kernel updates

  ipconfig0  = "ip=${var.network}.${20 + count.index}${var.subnet_mask},gw=${var.gateway}"  # Static IP assignment
  skip_ipv6  = var.disabled_ipv6                                 # Disable IPv6
  nameserver = var.dns_servers                    # DNS servers (Cloudflare, Google)
  sshkeys = var.SSH_public_key                      # SSH public key for authentication
  
  serial {
    id   = 0                                        # Serial port 0
    type = "socket"                                 # Socket-based serial connection
  }

  disks {
    ide {
      ide2 {
        cloudinit {
          storage = var.cloud_image_location                       # Cloud-init config storage location
        }
      }
    }
    scsi {
      scsi0 {
        disk {
          storage = var.storage_location                       # Main disk storage location
          size    = var.sl_storage_size                           # Main disk size (75GB - larger for containers)
        }
      }
    }
  }

  network {
    id     = 0                                      # Network device 0
    bridge = var.sl_network_interface                               # Bridge to vmbr1 (internal network)
    model  = "virtio"                               # High-performance virtio network model
  }

  ciuser     = var.sl_ci_user                               # Cloud-init user
  cipassword = var.sl_ci_password                        # Cloud-init password
}

# ==============================================================================
# HA PROXY NODE - Load balancer for Kubernetes API (1 instance)
# ==============================================================================
# Purpose: HAProxy load balancer for high-availability access to Kubernetes API
# IP: 192.168.1.30
# Resources: 2 cores, 4GB RAM, 25GB disk

resource "proxmox_vm_qemu" "HA-proxy" {

  name        = "Kubernetes-HA-proxy"               # VM name for the load balancer
  vmid        = 400 + count.index                   # VMID: 400
  count       = var.pr_count                                   # Single HA proxy node
  target_node = var.pm_hosts[count.index]                         # Proxmox node to deploy on
  clone       = var.template_name                   # Clone from cloud image template
  boot        = "order=scsi0"                       # Boot from SCSI disk 0
  agent       = 1                                   # Enable Proxmox agent

  os_type     = "cloud-init"                        # Cloud-init OS type for cloud image support
  cpu {
    cores   = var.pr_cpu_cores                                     # 2 CPU cores (load balancer needs less)
    sockets = var.pr_cpu_sockets                                     # Single socket
    type    = "host"                                # Use host CPU type for performance
  }
  memory           = var.pr_memory                           # 4GB RAM (load balancer needs less)
  scsihw           = "virtio-scsi-pci"              # High-performance SCSI controller
  ciupgrade        = true                           # Auto-upgrade cloud-init packages
  vm_state         = "running"                      # Keep VM running
  automatic_reboot = true                           # Reboot on kernel updates

  ipconfig0  = "ip=${var.network}.${30 + count.index}${var.subnet_mask},gw=${var.gateway}"  # Static IP assignment
  skip_ipv6  = var.disabled_ipv6                                 # Disable IPv6
  nameserver = var.dns_servers                   # DNS servers (Cloudflare, Google)
  sshkeys = var.SSH_public_key                      # SSH public key for authentication
  serial {
    id   = 0                                        # Serial port 0
    type = "socket"                                 # Socket-based serial connection
  }

  disks {
    ide {
      ide2 {
        cloudinit {
          storage = var.cloud_image_location                       # Cloud-init config storage location
        }
      }
    }
    scsi {
      scsi0 {
        disk {
          storage = var.storage_location                       # Main disk storage location
          size    = var.pr_storage_size                           # Main disk size (25GB - smaller for load balancer)
        }
      }
    }
  }

  network {
    id     = 0                                      # Network device 0
    bridge = var.pr_network_interface                                # Bridge to vmbr1 (internal network)
    model  = "virtio"                               # High-performance virtio network model
  }

  ciuser     = var.pr_ci_user                               # Cloud-init user
  cipassword = var.pr_ci_password                        # Cloud-init password
}