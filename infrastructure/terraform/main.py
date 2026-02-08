#!/usr/bin/env python3
import json
import sys
import os

# -----------------------------
# CONFIGURATION
# -----------------------------
INPUT_FILE = "tf_output.json"          # JSON file from: terraform output -json > tf_output.json
INVENTORY_FILE = "inventory.ini"       # Local output file
ANSIBLE_HOSTS_FILE = "/etc/ansible/hosts"  # Where to move it (needs sudo usually)

# -----------------------------
# HELPER FUNCTIONS
# -----------------------------
def load_terraform_json(file_path):
    """Load Terraform JSON output and extract the 'value' from each output"""
    try:
        with open(file_path, "r") as f:
            raw = json.load(f)
        
        # Transform to simple dict of lists/strings
        data = {}
        for key, info in raw.items():
            if "value" in info:
                data[key] = info["value"]
            else:
                print(f"Warning: Output '{key}' has no 'value' key — skipping")
        return data
    
    except FileNotFoundError:
        print(f"Error: Input file '{file_path}' not found.")
        print("Tip: Run → terraform output -json > tf_output.json")
        sys.exit(1)
    except json.JSONDecodeError as e:
        print(f"Error parsing JSON: {e}")
        sys.exit(1)


def generate_hosts(ips, users, role_prefix):
    """
    Create lines like '192.168.1.10 ansible_user=root custom_hostname=master1'
    """
    if not ips:
        return []
    
    # Safety: make sure lengths match
    if len(ips) != len(users):
        print(f"Warning: IP count ({len(ips)}) ≠ user count ({len(users)}) — using first user for all")
        users = [users[0]] * len(ips) if users else ["root"] * len(ips)
    
    hosts = []
    for i, (ip, user) in enumerate(zip(ips, users), start=1):
        hostname = f"{role_prefix}{i}" if len(ips) > 1 else role_prefix
        hosts.append(f"{ip.strip()} ansible_user={user.strip()} custom_hostname={hostname}")
    return hosts


def write_inventory(data, filename=INVENTORY_FILE):
    """Generate classic Ansible INI inventory with custom hostnames"""
    inventory = []

    # Group hierarchy
    inventory.append("[k8s_cluster:children]")
    inventory.append("masters")
    inventory.append("workers")
    inventory.append("haproxy")
    inventory.append("")

    # Masters
    masters = generate_hosts(data.get("master_ips", []), data.get("master_users", []), "kubernetes-master")
    if masters:
        inventory.append("[masters]")
        inventory.extend(masters)
        inventory.append("")

    # Workers
    workers = generate_hosts(data.get("worker_ips", []), data.get("worker_users", []), "kubernetes-worker")
    if workers:
        inventory.append("[workers]")
        inventory.extend(workers)
        inventory.append("")

    # HAProxy (usually 1 item)
    proxies = generate_hosts(data.get("haproxy_ip", []), data.get("haproxy_user", []), "kubernetes-haproxy")
    if proxies:
        inventory.append("[haproxy]")
        inventory.extend(proxies)
        inventory.append("")

    try:
        with open(filename, "w") as f:
            f.write("\n".join(inventory) + "\n")
        print(f"Ansible inventory successfully written to: {filename}")
    except PermissionError:
        print(f"Error writing to {filename}: permission denied")
        sys.exit(1)


def move_to_ansible_hosts(src=INVENTORY_FILE, dest=ANSIBLE_HOSTS_FILE):
    """Move file to /etc/ansible/hosts — usually requires sudo"""
    dest_dir = os.path.dirname(dest)
    if not os.path.exists(dest_dir):
        try:
            os.makedirs(dest_dir, exist_ok=True)
        except PermissionError:
            print(f"Cannot create directory {dest_dir} — permission denied")
            sys.exit(1)

    try:
        os.replace(src, dest)
        print(f"Inventory moved to: {dest}")
    except PermissionError:
        print(f"Cannot move to {dest} — permission denied.")
        print("Suggestion: run with sudo, or copy manually:")
        print(f"  sudo cp {src} {dest}")
        sys.exit(1)
    except FileNotFoundError:
        print(f"Source file {src} not found — nothing to move.")
        sys.exit(1)


# -----------------------------
# MAIN
# -----------------------------
if __name__ == "__main__":
    terraform_data = load_terraform_json(INPUT_FILE)
    
    # Debug: show what we parsed
    print("Parsed data:", terraform_data)
    
    write_inventory(terraform_data)
    
    # Uncomment if you want to auto-move (usually needs sudo python3 script.py)
    move_to_ansible_hosts()
