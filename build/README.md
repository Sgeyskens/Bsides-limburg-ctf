---

# Challenge Source Code Repository Guide

This directory contains the **source code for all CTF challenges** included in the platform.  
Each challenge is stored in its own folder and is automatically built and pushed to the GitLab Container Registry whenever a `git push` is executed.

The structure is designed to support reproducible builds, automated deployments, and consistent challenge packaging across the entire CTF environment.

---

## 1. Purpose

This directory serves as the central location for:

- Storing challenge source code  
- Organizing challenges by category or type  
- Building challenge containers automatically  
- Publishing challenge images to the GitLab registry  
- Integrating challenges into the CTFd deployment pipeline  

Every challenge follows a standardized layout to ensure compatibility with the automated build system.

---

## 2. Repository Structure

Each challenge resides in its own subdirectory:

```
build/
├── Clientauth-A3/
│   ├── Dockerfile
│   ├── challenge.yml
│   └── ...
├── web-challenge-A3/
│   ├── Dockerfile
│   ├── challenge.yml
│   └── ...
└── ...
```

Typical components include:

- **Dockerfile** — Defines how the challenge container is built  
- **Source code** — Application logic, binaries, scripts, or artifacts  
- **challenge.yml** — Metadata consumed by the CTFd deployment pipeline  


---

## 3. Automated Build & Push Pipeline

When a commit is pushed to the repository:

1. GitLab CI/CD detects changes in the `challenges/` directory  
2. Each modified challenge is built into a container image  
3. Images are tagged using the challenge name and commit hash  
4. Images are pushed to the GitLab Container Registry  
5. The CTFd deployment pipeline pulls the latest images during deployment  

This ensures that:

- Every challenge build is reproducible  
- No manual container building is required  
- CTFd always uses the latest version of each challenge  

---

## 4. Adding a New Challenge

To add a new challenge:

1. Create a new folder under `build/`  
2. Add a `Dockerfile` and source code  
3. Include a `challenge.yml` file with metadata such as:
   - Challenge name  
   - Category  
   - Description  
   - Flag location  
   - Container image name  
4. Add a maintainer README if needed  
5. Commit and push your changes  

The CI pipeline will automatically build and publish the new challenge image.

---

## 5. challenge.yml Format

A typical metadata file looks like:

```yaml
name: Camp Crystal Lake Access Badge
category: Web
value: 100
description: |
  🪓 **Camp Crystal Lake — Staff Terminal**
  
  The night shift terminal claims you're just a guest.
  Rangers insist only authorized staff can open the Equipment Locker.

  Your task:
  - Gain access to the staff terminal.
  - Reach the locker screen and recover the flag.

  **URL:** http://192.168.159.130:8085/

  Hint: The “security” feels… suspiciously client-side.
flags:
  - ctf{client_side_auth_is_not_security}
state: visible

```

This file is consumed by the CTFd deployment automation to register the challenge.

---

## 6. Notes

- Ensure each challenge builds successfully before pushing.  
- Avoid committing large binaries; use Docker build steps instead.  
- Keep challenge flags out of source code when possible; inject them via environment variables or secrets.  
- The CI pipeline handles image tagging and publishing automatically.  
- All challenge images must be compatible with the Kubernetes environment used by CTFd.
