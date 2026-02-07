#!/usr/bin/env python3
import json
import sys
import os
# -----------------------------
# CONFIGURATION
# -----------------------------
INPUT_FILE = "tf_output.json"  # JSON file exported from Terraform
INVENTORY_FILE = "inventory.ini"      # Output Ansible inventory file
ANSIBLE_HOSTS_FILE = "/etc/ansible/hosts"  # Default Ansible inventory path
# -----------------------------
# HELPER FUNCTIONS
# -----------------------------
def load_json(file_path):
    """Load JSON data from file"""
    try:
        with open(file_path, "r") as f:
            return json.load(f)
    except FileNotFoundError:
        print(f"Error: File '{file_path}' not found.")
        sys.exit(1)
    except json.JSONDecodeError as e:
        print(f"Error parsing JSON file: {e}")
        sys.exit(1)

def generate_hosts(ips, users):
    """Generate inventory lines like 'IP ansible_user=USER'"""
    if not ips:
        return []
    if not users or len(users) != len(ips):
        users = [users[0] if users else "ubuntu"] * len(ips)
    return [f"{ip} ansible_user={user}" for ip, user in zip(ips, users)]

def write_inventory(data, filename=INVENTORY_FILE):
    """Write Ansible inventory file"""
    inventory = []

    # Masters
    masters = generate_hosts(data.get("master_ips", []), data.get("master_users", []))
    if masters:
        inventory.append("[masters]")
        inventory.extend(masters)
        inventory.append("")

    # Workers
    workers = generate_hosts(data.get("worker_ips", []), data.get("worker_users", []))
    if workers:
        inventory.append("[workers]")
        inventory.extend(workers)
        inventory.append("")

    # HA Proxy
    proxy = generate_hosts(data.get("haproxy_ip", []), data.get("haproxy_user", []))
    if proxy:
        inventory.append("[haproxy]")
        inventory.extend(proxy)
        inventory.append("")

    with open(filename, "w") as f:
        f.write("\n".join(inventory))

    print(f"Ansible inventory written to: {filename}")

def move_inventory(src=INVENTORY_FILE, dest=ANSIBLE_HOSTS_FILE):
    """Move inventory to /etc/ansible/hosts, creating directories if necessary"""
    dest_dir = os.path.dirname(dest)
    if not os.path.exists(dest_dir):
        try:
            os.makedirs(dest_dir, exist_ok=True)
        except PermissionError:
            print(f"Error: Cannot create directory '{dest_dir}', permission denied.")
            sys.exit(1)

    try:
        os.replace(src, dest)
        print(f"Inventory moved to: {dest}")
    except PermissionError:
        print(f"Error: Cannot write to '{dest}', permission denied. Run as root or use sudo.")
        sys.exit(1)


# -----------------------------S
# MAIN
# -----------------------------
if __name__ == "__main__":
    terraform_data = load_json(INPUT_FILE)
    write_inventory(terraform_data)
    move_inventory()