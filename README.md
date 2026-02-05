# 스타트업 ERP 시스템

Laravel 11 + Filament 3 기반의 스타트업용 종합 ERP 시스템입니다.

## 주요 기능

### 인사관리 (HR)
- 직원 관리 (입사, 퇴사, 정보 관리)
- 부서 관리
- 근태 관리 (출퇴근 기록)
- 휴가 관리 (신청, 승인)

### CRM
- 고객/거래처 관리
- 담당자 관리
- 리드 관리 및 전환
- 영업 기회 관리
- 계약 관리

### 재무/회계
- 청구서 관리 (발행, 결제)
- 비용 관리 (신청, 승인)
- 결제 내역 관리
- 계정과목 관리

### 프로젝트 관리
- 프로젝트 관리
- 마일스톤 관리
- 태스크 관리
- 타임시트 (시간 기록)

### 구매관리
- 공급업체 관리
- 발주서 관리

### 재고관리
- 상품/품목 관리
- 창고 관리
- 재고 현황
- 입출고 관리

### 부가 기능
- 대시보드 (통계, 차트)
- 역할 기반 권한 관리
- 활동 로그/감사 추적
- 다국어 지원 (한국어/영어)
- REST API

## 시스템 요구사항

- PHP 8.2+
- MySQL 8.0+ / MariaDB 10.6+
- Composer 2.0+
- Node.js 18+ (프론트엔드 빌드용)

## 설치 방법

### 1. 프로젝트 클론

```bash
git clone <repository-url> erp
cd erp
```

### 2. 의존성 설치

```bash
composer install
npm install
```

### 3. 환경 설정

```bash
cp .env.example .env
php artisan key:generate
```

`.env` 파일에서 데이터베이스 설정을 수정합니다:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=erp
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 4. 데이터베이스 마이그레이션 및 시딩

```bash
php artisan migrate
php artisan db:seed
```

### 5. 관리자 계정 생성

```bash
php artisan make:filament-user
```

### 6. 스토리지 링크 생성

```bash
php artisan storage:link
```

### 7. 개발 서버 실행

```bash
php artisan serve
```

브라우저에서 `http://localhost:8000/admin` 으로 접속합니다.

## 기본 역할 및 권한

시스템에는 다음 역할이 기본으로 설정됩니다:

| 역할 | 설명 |
|------|------|
| Super Admin | 모든 권한 |
| Admin | 시스템 설정 제외 모든 권한 |
| Manager | 부서별 관리 권한 |
| Accountant | 재무/회계 모듈 전용 |
| HR Manager | 인사관리 모듈 전용 |
| Employee | 기본 조회 및 본인 정보 수정 |

## 디렉토리 구조

```
app/
├── Filament/
│   ├── Resources/     # Filament CRUD 리소스
│   ├── Pages/         # 커스텀 페이지
│   └── Widgets/       # 대시보드 위젯
├── Models/            # Eloquent 모델
├── Policies/          # 권한 정책
├── Services/          # 비즈니스 로직
└── Notifications/     # 알림 클래스

database/
├── migrations/        # 마이그레이션
└── seeders/          # 시더

lang/
├── ko/               # 한국어
└── en/               # 영어
```

## API 사용

API 인증은 Laravel Sanctum을 사용합니다.

### 토큰 발급

```bash
POST /api/login
{
    "email": "user@example.com",
    "password": "password"
}
```

### API 엔드포인트

- `GET /api/customers` - 고객 목록
- `GET /api/projects` - 프로젝트 목록
- `GET /api/invoices` - 청구서 목록

## 라이선스

MIT License

## 기술 지원

문의사항이 있으시면 이슈를 등록해주세요.
