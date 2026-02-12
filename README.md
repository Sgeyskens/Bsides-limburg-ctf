
# BSides Limburg CTF

A lightweight repository created for the **BSides Limburg Capture The Flag (CTF)** event.  
This project hold the full deployment to setup the CTFd platform e.g. Ansible, Terraform, Helmchart and deployment.


## 🚀 Getting Started

1. **Clone the repository**
   ```bash
   git clone https://github.com/Sgeyskens/Bsides-limburg-ctf
   cd Bsides-limburg-ctf
   ```
2. **Deploy terraform**
    reference infrastructure/terraform/README.md

3. **Deploy Proxy**
    reference infrastructure/ansible/kubeadm-proxy/README.md

4. **Deploy kubeadm cluster**
    reference infrastructure/ansible/kubeadm/README.md

5. **Deploy kubeadm Metallb**
    reference infrastructure/ansible/kubeadm-metallb/README.md   

6. **Deploy kubeadm ingress**
    reference infrastructure/ansible/kubeadm-ingress/README.md 

7. **Deploy monitoring**
    reference infrastructure/ansible/monitoring/README.md

8. **Deploy ctfd**
    reference infrastructure/ansible/ctfd/README.md