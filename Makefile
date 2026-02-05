# ERP Docker 관리 Makefile
# 사용법: make [명령어]

.PHONY: help build up down restart logs shell db-shell fresh migrate seed cache-clear key-generate npm-install npm-build

# 기본 명령어
help:
	@echo "사용 가능한 명령어:"
	@echo "  make build         - Docker 이미지 빌드"
	@echo "  make up            - 컨테이너 시작"
	@echo "  make down          - 컨테이너 중지"
	@echo "  make restart       - 컨테이너 재시작"
	@echo "  make logs          - 로그 보기"
	@echo "  make shell         - App 컨테이너 접속"
	@echo "  make db-shell      - MySQL 접속"
	@echo "  make fresh         - DB 초기화 + 마이그레이션 + 시딩"
	@echo "  make migrate       - 마이그레이션 실행"
	@echo "  make seed          - 시더 실행"
	@echo "  make cache-clear   - 캐시 클리어"
	@echo "  make key-generate  - 앱 키 생성"
	@echo "  make setup         - 초기 설정 (최초 1회)"
	@echo "  make prod-up       - 프로덕션 환경 시작"
	@echo "  make prod-down     - 프로덕션 환경 중지"

# Docker 빌드
build:
	docker compose build

# 컨테이너 시작 (백그라운드)
up:
	docker compose up -d

# 컨테이너 중지
down:
	docker compose down

# 컨테이너 재시작
restart:
	docker compose restart

# 로그 보기
logs:
	docker compose logs -f

# App 컨테이너 쉘 접속
shell:
	docker compose exec app sh

# MySQL 접속
db-shell:
	docker compose exec db mysql -u erp -psecret erp

# DB 초기화 + 마이그레이션 + 시딩
fresh:
	docker compose exec app php artisan migrate:fresh --seed

# 마이그레이션
migrate:
	docker compose exec app php artisan migrate

# 시더
seed:
	docker compose exec app php artisan db:seed

# 캐시 클리어
cache-clear:
	docker compose exec app php artisan cache:clear
	docker compose exec app php artisan config:clear
	docker compose exec app php artisan route:clear
	docker compose exec app php artisan view:clear

# 앱 키 생성
key-generate:
	docker compose exec app php artisan key:generate

# 초기 설정 (최초 1회)
setup:
	cp .env.docker .env
	docker compose build
	docker compose up -d
	@echo "컨테이너 시작 대기 (10초)..."
	sleep 10
	docker compose exec app php artisan key:generate
	docker compose exec app php artisan storage:link
	docker compose exec app php artisan migrate --seed
	@echo ""
	@echo "=========================================="
	@echo "✅ 설정 완료!"
	@echo "=========================================="
	@echo "URL: http://localhost"
	@echo "관리자 이메일: admin@techwave.kr"
	@echo "비밀번호: password"
	@echo "=========================================="

# Composer 설치
composer-install:
	docker compose exec app composer install

# npm 설치
npm-install:
	docker compose run --rm node npm install

# npm 빌드
npm-build:
	docker compose run --rm node npm run build

# 프로덕션 빌드
prod-build:
	docker compose -f docker-compose.yml -f docker-compose.prod.yml build

# 프로덕션 시작
prod-up:
	docker compose -f docker-compose.yml -f docker-compose.prod.yml up -d

# 프로덕션 중지
prod-down:
	docker compose -f docker-compose.yml -f docker-compose.prod.yml down

# 프로덕션 로그
prod-logs:
	docker compose -f docker-compose.yml -f docker-compose.prod.yml logs -f
