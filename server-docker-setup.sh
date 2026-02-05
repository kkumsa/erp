dnf -y update kernel-core && reboot

dnf -y install kernel-modules-extra

dnf -y remove docker-ce && dnf -y config-manager --add-repo https://download.docker.com/linux/rhel/docker-ce.repo && dnf -y install docker-ce docker-ce-cli containerd.io docker-buildx-plugin docker-compose-plugin 

systemctl daemon-reload

systemctl enable --now docker

