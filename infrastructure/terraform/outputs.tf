################################################################################
# Terraform Outputs for Kubernetes Cluster on Proxmox
# Includes Master nodes, Worker nodes, HA Proxy, and Cloud-init users
################################################################################

# ----------------------------
# Kubernetes Master Nodes
# ----------------------------
output "master_ips" {
  description = "IP addresses of Kubernetes master nodes"
  value = [
    for i in range(var.ms_count) : "${var.network}.${10 + i}"
  ]
}

output "master_users" {
  description = "Cloud-init users for Kubernetes master nodes"
  value = [
    for i in range(var.ms_count) : var.ms_ci_user
  ]
}

# ----------------------------
# Kubernetes Worker Nodes
# ----------------------------
output "worker_ips" {
  description = "IP addresses of Kubernetes worker nodes"
  value = [
    for i in range(var.wk_count) : "${var.network}.${20 + i}"
  ]
}

output "worker_users" {
  description = "Cloud-init users for Kubernetes worker nodes"
  value = [
    for i in range(var.wk_count) : var.wk_ci_user
  ]
}

# ----------------------------
# HA Proxy Node
# ----------------------------
output "haproxy_ip" {
  description = "IP address of HA Proxy node"
  value = [
    for i in range(var.pr_count) : "${var.network}.${30 + i}"
  ]
}

output "haproxy_user" {
  description = "Cloud-init user for HA Proxy node"
  value = [
    for i in range(var.pr_count) : var.pr_ci_user
  ]
}
