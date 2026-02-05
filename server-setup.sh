#!/bin/bash

#===============================================================================
# Rocky Linux 10.x 서버 초기 설정 스크립트
# 실행: sudo bash server-setup.sh
#===============================================================================

set -e  # 에러 발생 시 중단

# 색상 정의
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# 로그 함수
log_info() { echo -e "${BLUE}[INFO]${NC} $1"; }
log_success() { echo -e "${GREEN}[SUCCESS]${NC} $1"; }
log_warning() { echo -e "${YELLOW}[WARNING]${NC} $1"; }
log_error() { echo -e "${RED}[ERROR]${NC} $1"; }

#===============================================================================
# 0. Root 권한 확인
#===============================================================================
if [ "$EUID" -ne 0 ]; then
    log_error "이 스크립트는 root 권한으로 실행해야 합니다."
    log_info "실행: sudo bash server-setup.sh"
    exit 1
fi

log_info "=========================================="
log_info "Rocky Linux 서버 초기 설정 시작"
log_info "=========================================="


#===============================================================================
# 1. 필수 패키지 설치
#===============================================================================
log_info "[1/10] 필수 패키지 설치 중..."
dnf install -y \
    epel-release \
    git \
    vim-enhanced \
    wget \
    curl \
    net-tools \
    bind-utils \
    jq \
    policycoreutils-python-utils \
    plocate \
    setroubleshoot-server

log_success "필수 패키지 설치 완료"

#===============================================================================
# 2. 시스템 업데이트
#===============================================================================
log_info "[2/10] 시스템 패키지 업데이트 중..."
dnf update -y
log_success "시스템 업데이트 완료"


#===============================================================================
# 3. 사용자 생성 (kkumsa)
#===============================================================================
log_info "[3/10] 사용자 'kkumsa' 생성 중..."

USERNAME="kkumsa"
USER_PASSWORD="rhwoahr8474"

if id "$USERNAME" &>/dev/null; then
    log_warning "사용자 '$USERNAME'이(가) 이미 존재합니다."
else
    useradd -m -s /bin/bash "$USERNAME"
    echo "$USERNAME:$USER_PASSWORD" | chpasswd
    
    # sudo 권한 부여
    usermod -aG wheel "$USERNAME"
    
    log_success "사용자 '$USERNAME' 생성 완료 (sudo 권한 부여됨)"
fi

# SSH 키 디렉토리 생성
#mkdir -p /home/$USERNAME/.ssh
#chmod 700 /home/$USERNAME/.ssh
#chown -R $USERNAME:$USERNAME /home/$USERNAME/.ssh

#===============================================================================
# 4. SSH 보안 설정
#===============================================================================
log_info "[4/10] SSH 보안 설정 중..."

# SSH 설정 백업
cp /etc/ssh/sshd_config /etc/ssh/sshd_config.backup

# SSH 설정 변경
cat > /etc/ssh/sshd_config.d/99-custom.conf << 'EOF'
# SSH 보안 설정
Port 22
PermitRootLogin prohibit-password
PasswordAuthentication yes
PubkeyAuthentication yes
MaxAuthTries 5
ClientAliveInterval 300
ClientAliveCountMax 2
X11Forwarding no
AllowTcpForwarding no
EOF

# SSH 서비스 재시작
systemctl restart sshd
log_success "SSH 보안 설정 완료"

#===============================================================================
# 5. Firewalld 설정
#===============================================================================
log_info "[5/10] Firewalld 방화벽 설정 중..."

dnf install -y firewalld
systemctl enable --now firewalld

# 기본 포트 허용
firewall-cmd  --add-service=ssh
firewall-cmd  --add-service=http
firewall-cmd  --add-service=https

# 추가 포트 (필요 시 활성화)
# firewall-cmd --permanent --add-port=3306/tcp  # MySQL
# firewall-cmd --permanent --add-port=6379/tcp  # Redis
# firewall-cmd --permanent --add-port=8080/tcp  # 개발용

firewall-cmd --runtime-to-permanent

log_success "Firewalld 설정 완료"

#===============================================================================
# 6. Fail2ban 설치 및 설정
#===============================================================================
log_info "[6/10] Fail2ban 설치 및 설정 중..."

dnf install -y fail2ban

# Fail2ban 설정
# 아래 부분은  /etc/fail2ban/jail.d/00-override.conf 에 추가하는 것이 좋습니다.
cat > /etc/fail2ban/jail.local << 'EOF'
[DEFAULT]
# 차단 시간 (1시간)
bantime = 3600
# 검사 시간 범위 (10분)
findtime = 600
# 최대 실패 횟수
maxretry = 5

# 화이트리스트 (차단 제외 IP)
ignoreip = 127.0.0.1/8 ::1 211.54.71.242 61.74.102.182

# 아래 부분은  /etc/fail2ban/jail.d/01-sshd.conf 에 추가하는 것이 좋습니다.
[sshd]
enabled = true
port = ssh
filter = sshd
logpath = /var/log/secure
maxretry = 3
bantime = 3600

# 아래 부분은  /etc/fail2ban/jail.d/02-nginx-auth.conf 에 추가하는 것이 좋습니다.
[nginx-http-auth]
enabled = true
port = http,https
filter = nginx-http-auth
logpath = /var/log/nginx/error.log
maxretry = 5

[nginx-limit-req]
enabled = true
port = http,https
filter = nginx-limit-req
logpath = /var/log/nginx/error.log
maxretry = 10

[nginx-404]
enabled = true
port = http,https
filter = nginx-404
logpath = /var/log/nginx/access.log
# 10분 내 5번 404 요청 시 차단
maxretry = 5
findtime = 600
# 24시간 차단
bantime = 86400

[nginx-badbots]
enabled = true
port = http,https
filter = nginx-badbots
logpath = /var/log/nginx/access.log
maxretry = 1
bantime = 86400

[nginx-noscript]
enabled = true
port = http,https
filter = nginx-noscript
logpath = /var/log/nginx/access.log
maxretry = 3
bantime = 86400

[nginx-noproxy]
enabled = true
port = http,https
filter = nginx-noproxy
logpath = /var/log/nginx/access.log
maxretry = 1
bantime = 86400
EOF

# Fail2ban 필터: 404 Not Found 차단
cat > /etc/fail2ban/filter.d/nginx-404.conf << 'EOF'
[Definition]
# 404 에러를 반복적으로 발생시키는 IP 차단
failregex = ^<HOST> .* "(GET|POST|HEAD|PUT|DELETE|PATCH|OPTIONS).*" 404 .*$
            ^<HOST> .* "(GET|POST|HEAD|PUT|DELETE|PATCH|OPTIONS).*" 400 .*$
            ^<HOST> .* "(GET|POST|HEAD|PUT|DELETE|PATCH|OPTIONS).*" 403 .*$

ignoreregex = \.(?:css|js|png|jpg|jpeg|gif|ico|woff|woff2|ttf|svg|eot)
              /robots\.txt
              /favicon\.ico
              /apple-touch-icon
              /sitemap\.xml
EOF

# Fail2ban 필터: 악성 봇 차단
cat > /etc/fail2ban/filter.d/nginx-badbots.conf << 'EOF'
[Definition]
# 악성 봇 및 스캐너 차단
failregex = ^<HOST> .* ".*(?:sqlmap|nikto|nmap|masscan|zgrab|python-requests|curl\/|wget\/|Go-http-client|libwww-perl|Scrapy|MJ12bot|AhrefsBot|SemrushBot|DotBot).*" .*$
            ^<HOST> .* ".*(?:wp-login|wp-admin|xmlrpc|wlwmanifest|wp-includes).*" .*$
            ^<HOST> .* ".*(?:\.env|\.git|\.svn|\.htaccess|\.htpasswd|config\.php|phpinfo|phpmyadmin|adminer).*" .*$
            ^<HOST> .* ".*(?:/admin|/manager|/administrator|/wp-json|/api/v1/pods).*" (?:400|403|404) .*$

ignoreregex =
EOF

# Fail2ban 필터: 스크립트 취약점 스캔 차단
cat > /etc/fail2ban/filter.d/nginx-noscript.conf << 'EOF'
[Definition]
# 취약점 스캐닝 및 의심스러운 요청 차단
failregex = ^<HOST> .* ".*(?:\.asp|\.aspx|\.jsp|\.cgi|\.pl|\.exe|\.dll).*" .*$
            ^<HOST> .* ".*(?:/cgi-bin/|/scripts/|/shell|/cmd|/command).*" .*$
            ^<HOST> .* ".*(?:eval\(|base64_decode|<script|alert\(|document\.cookie).*" .*$
            ^<HOST> .* ".*(?:UNION|SELECT|INSERT|UPDATE|DELETE|DROP|--).*(FROM|INTO|WHERE).*" .*$
            ^<HOST> .* ".*(?:\.\./|\.\.\\\\|%2e%2e|%252e).*" .*$

ignoreregex =
EOF

# Fail2ban 필터: 프록시 시도 차단
cat > /etc/fail2ban/filter.d/nginx-noproxy.conf << 'EOF'
[Definition]
# 서버를 프록시로 사용하려는 시도 차단
failregex = ^<HOST> .* "(?:GET|POST|CONNECT) https?://(?!localhost|127\.0\.0\.1).*" .*$
            ^<HOST> .* "CONNECT .+:\d+ HTTP.*" .*$

ignoreregex =
EOF


systemctl enable --now fail2ban
log_success "Fail2ban 설치 및 설정 완료"


#===============================================================================
# 7. Nginx 설치
#===============================================================================
log_info "[7/10] Nginx 설치 중..."

dnf install -y nginx

# Nginx 기본 설정 최적화 (server 블록 내에서만 유효한 설정)
cat > /etc/nginx/conf.d/security.conf << 'EOF'
# 보안 설정 (server 블록에서 include하여 사용)
# 사용법: server { include /etc/nginx/conf.d/security-headers.conf; }
EOF

# 보안 헤더 설정 파일 (server 블록에서 include용)
cat > /etc/nginx/conf.d/security-headers.conf << 'EOF'
# 보안 헤더
add_header X-Frame-Options "SAMEORIGIN" always;
add_header X-Content-Type-Options "nosniff" always;
add_header X-XSS-Protection "1; mode=block" always;
add_header Referrer-Policy "strict-origin-when-cross-origin" always;
EOF

# nginx.conf에서 server_tokens off 설정 (http 블록)
sed -i '/http {/a\    server_tokens off;' /etc/nginx/nginx.conf 2>/dev/null || true

# client_max_body_size 설정 추가
sed -i '/http {/a\    client_max_body_size 100M;' /etc/nginx/nginx.conf 2>/dev/null || true

# Nginx 시작
systemctl enable --now nginx

log_success "Nginx 설치 완료"

#===============================================================================
# 8. Certbot (Let's Encrypt) 설치
#===============================================================================
log_info "[8/10] Certbot 설치 중..."

dnf install -y certbot python3-certbot-nginx

log_success "Certbot 설치 완료"
log_info "SSL 인증서 발급: sudo certbot --nginx -d yourdomain.com"

# #===============================================================================
# # 10. PHP 8.3 + 확장 설치 (Laravel용)
# #===============================================================================
# log_info "[10/12] PHP 8.3 및 확장 설치 중..."

# # Remi 저장소 추가
# dnf install -y https://rpms.remirepo.net/enterprise/remi-release-$(rpm -E %rhel).rpm || true
# dnf module reset php -y
# dnf module enable php:remi-8.3 -y

# # PHP 및 확장 설치
# dnf install -y \
#     php \
#     php-fpm \
#     php-cli \
#     php-common \
#     php-mysqlnd \
#     php-pdo \
#     php-gd \
#     php-mbstring \
#     php-xml \
#     php-curl \
#     php-zip \
#     php-bcmath \
#     php-json \
#     php-opcache \
#     php-intl \
#     php-redis \
#     php-sodium

# # PHP-FPM 설정
# sed -i 's/user = apache/user = nginx/' /etc/php-fpm.d/www.conf
# sed -i 's/group = apache/group = nginx/' /etc/php-fpm.d/www.conf
# sed -i 's/;listen.owner = nobody/listen.owner = nginx/' /etc/php-fpm.d/www.conf
# sed -i 's/;listen.group = nobody/listen.group = nginx/' /etc/php-fpm.d/www.conf

# systemctl start php-fpm
# systemctl enable php-fpm

# log_success "PHP 8.3 설치 완료"
# php -v

# #===============================================================================
# # 11. Composer 설치
# #===============================================================================
# log_info "[11/12] Composer 설치 중..."

# curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# log_success "Composer 설치 완료"
# composer --version

# #===============================================================================
# # 12. Node.js (LTS) 설치
# #===============================================================================
# log_info "[12/12] Node.js LTS 설치 중..."

# # NodeSource 저장소 추가 (Node.js 20 LTS)
# curl -fsSL https://rpm.nodesource.com/setup_20.x | bash -
# dnf install -y nodejs

# log_success "Node.js 설치 완료"
# node --version
# npm --version

#===============================================================================
# 9. SELinux 설정 (Nginx 허용)
#===============================================================================
log_info "[9/10] SELinux 설정 확인 중..."
getenforce
RET_CODE=$?
if [ $RET_CODE -eq 0 ]; then
    log_success "SELinux 활성화 되어 있습니다."
    log_info "SELinux 설정 중..."

    setsebool -P httpd_can_network_connect 1
    setsebool -P httpd_execmem 1
    setsebool -P httpd_can_network_connect_db 1
else
    log_error "SELinux 비활성화 되어 있습니다."
fi

log_success "SELinux 설정 완료"

#===============================================================================
# 10. 웹 디렉토리 생성
#===============================================================================
log_info "[10/10] 웹 디렉토리 생성 중..."

mkdir -p /var/www
chown -R $USERNAME:nginx /var/www
chmod -R 775 /var/www

log_success "웹 디렉토리 생성 완료"

#===============================================================================
# 시스템 정보 출력
#===============================================================================
echo ""
log_info "=========================================="
log_success "🎉 서버 초기 설정 완료!"
log_info "=========================================="
echo ""
echo -e "${GREEN}설치된 패키지:${NC}"
echo "  - Nginx: $(nginx -v 2>&1 | cut -d'/' -f2)"
echo "  - Certbot: $(certbot --version 2>/dev/null | cut -d' ' -f2)"
echo "  - Fail2ban: $(fail2ban-client --version 2>/dev/null | head -n1)"
echo ""
echo -e "${GREEN}생성된 사용자:${NC}"
echo "  - 사용자명: $USERNAME"
echo "  - 비밀번호: $USER_PASSWORD"
echo "  - sudo 권한: 활성화"
echo ""
echo -e "${YELLOW}⚠️  중요 알림:${NC}"
echo "  1. 보안을 위해 비밀번호를 변경하세요: passwd $USERNAME"
echo "  2. SSH 키 인증을 설정하세요"
echo "  3. SSL 인증서 발급: sudo certbot --nginx -d yourdomain.com"
echo ""
echo -e "${BLUE}방화벽 상태:${NC}"
firewall-cmd --list-all
echo ""
echo -e "${BLUE}서비스 상태:${NC}"
echo "  - Nginx: $(systemctl is-active nginx)"
echo "  - Fail2ban: $(systemctl is-active fail2ban)"
echo "  - Firewalld: $(systemctl is-active firewalld)"
echo ""
log_info "서버 재부팅을 권장합니다: sudo reboot"
