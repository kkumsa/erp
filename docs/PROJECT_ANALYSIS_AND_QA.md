# 스타트업 ERP — 프로젝트 분석 및 QA 보고서

> IT 비평가·QA 관점의 전체 분석, 보완·개선 지점, 패키징 제안

---

## 1. 프로젝트 전체 분석

### 1.1 기술 스택 및 아키텍처

| 구분 | 내용 |
|------|------|
| **프레임워크** | Laravel 11, Filament 3 (Admin Panel) |
| **인증/권한** | Laravel Auth, Spatie Laravel Permission (역할·권한) |
| **감사/로깅** | Spatie Activity Log |
| **비동기** | Laravel Queue (database driver 기본) |
| **API** | Laravel Sanctum (설정됨, 실제 엔드포인트 최소) |
| **다국어** | ko/en, 세션·쿠키 기반 locale 전환 |
| **인프라** | Docker / Docker Compose (dev·prod), Nginx, PHP-FPM, MySQL |

### 1.2 모듈 구성

- **인사(HR)**  
  부서, 직원, 근태(Attendance), 휴가(Leave/LeaveType), 승인 플로우 연동
- **CRM**  
  고객(Customer), 담당자(Contact), 리드(Lead), 영업기회(Opportunity), 계약(Contract)
- **재무/회계**  
  계정과목(Account), 청구서(Invoice/InvoiceItem), 비용(Expense/ExpenseCategory), 결제(Payment), 입금 매칭(BankDeposit), 결제 매칭 페이지
- **프로젝트**  
  프로젝트(Project), 마일스톤(Milestone), 태스크(Task), 타임시트(Timesheet)
- **구매**  
  공급업체(Supplier), 발주(PurchaseOrder/PurchaseOrderItem), 승인 플로우
- **재고**  
  상품·카테고리(Product/ProductCategory), 창고(Warehouse), 재고(Stock), 입출고(StockMovement)
- **시스템**  
  사용자(User), 역할/권한, 승인 플로우 정의(ApprovalFlow/Step), 알림 설정, 활동 로그, 휴지통(Trash)

### 1.3 데이터·권한 설계 요약

- **FinanceDepartmentScope**  
  청구서·비용·결제·입금 등은 “작성자/PM 부서” 또는 “등록자/신청자 부서” 기준으로 필터링. Super Admin·Accountant는 전체 접근.
- **HasResourcePermissions**  
  리소스별 `permissionPrefix`(예: `customer`, `invoice`)로 `*.view/create/update/delete`(및 일부 `approve`) 적용. Filament 메뉴·CRUD 노출이 권한과 일치.
- **Approval**  
  비용·휴가·발주·계약 등에 승인 플로우(다단계) 적용 가능.
- **Soft Delete**  
  User, Department, Employee, Customer, Invoice 등 주요 엔티티에 적용.

### 1.4 강점

- 모듈별 역할이 명확하고, Filament 리소스·RelationManager·위젯 구조가 일관됨.
- Enums로 상태·유형이 통일되어 유지보수와 다국어(라벨)에 유리함.
- Activity Log로 주요 모델 변경 이력 추적 가능.
- 다국어(ko/en), 로그인 후/로그인 화면 언어 전환 지원.
- Docker·Makefile·배포 스크립트로 로컬/서버 환경 구성 가능.
- 샘플 시더(SampleDataSeeder 등)로 데모·테스트 데이터가 풍부함.

---

## 2. 보완이 필요한 부분 & 개선 지점

### 2.1 테스트 (Critical)

- **현상**  
  `phpunit.xml`에 `tests/Unit`, `tests/Feature`가 정의되어 있으나 **`tests/` 디렉토리 자체가 없음.**
- **권장**  
  - `tests/Unit`: Enums, 핵심 계산 로직(예: Invoice 합계, 재고 차감), Scope 동작(FinanceDepartmentScope 등).
  - `tests/Feature`: 로그인, 권한별 메뉴/리소스 접근, 결제 매칭(입금→청구서), 주요 CRUD·상태 변경.
  - 최소한: 로그인, 권한별 1~2 리소스 접근, Invoice 생성·결제 연동, BankDeposit 매칭 1건.

### 2.2 권한·보안 세분화 (High)

- **현상**  
  - 리소스 접근은 `HasResourcePermissions`(permission 이름)로만 제어. **Policy 클래스 없음.**
  - “본인 부서만”, “본인 작성만” 같은 **레코드 단위 정책**이 Scope에만 의존하고, Filament 폼/액션 단에서는 명시적 authorize 부족.
- **권장**  
  - 주요 리소스(Invoice, Expense, Employee, Leave 등)에 `Policy` 추가 후 `$this->authorize()` 또는 Filament `can()`과 연동.
  - “수정/삭제는 작성자 또는 부서장만” 등 비즈니스 규칙을 Policy로 두고, Scope는 “목록 필터”로만 사용.

### 2.3 API 설계 (High)

- **현상**  
  `routes/api.php`는 `auth:sanctum` 하에 `GET /user` 정도만 존재. README의 “REST API” 설명과 괴리.
- **권장**  
  - 외부 연동·모바일 앱을 염두에 둔다면: 고객·청구서·프로젝트·결제 등에 대한 **읽기/쓰기 API** 설계 및 버전 prefix(예: `/api/v1/`).
  - API 전용 Rate limiting, 요청 검증(Form Request), 응답 포맷 통일(Resource/JsonResource).
  - API용 권한(예: `api.invoices.read`)을 Spatie Permission에 추가하고, API 라우트에 적용.

### 2.4 입력 검증·일관성 (Medium)

- **현상**  
  - Form Request 클래스가 없고, Filament 리소스의 `form()` 스키마에 검증이 녹아 있음.
  - 복잡한 규칙(예: 청구서 합계·세액, 재고 차감 조건)이 모델 `boot`/Observer에만 있을 수 있어, **재사용·테스트·에러 메시지 통일**이 어렵다.
- **권장**  
  - 중요 도메인(Invoice, Payment, Expense, StockMovement 등)은 **Form Request 또는 전용 DTO/Validator**로 규칙을 모으고, Filament와 API에서 공통 사용.
  - 금액·날짜·상태 전이 등은 모델 접근자/메서드보다 “서비스 레이어” 또는 “액션 클래스”에서 처리하면 테스트와 감사에 유리.

### 2.5 성능·N+1 (Medium)

- **현상**  
  리소스/RelationManager에서 `with()`/`load()` 사용은 일부 있으나, **테이블 목록·위젯·결제 매칭 페이지** 등에서 연관 로딩이 누락될 가능성 있음.
- **권장**  
  - Invoice 목록: `customer`, `payments` 등 자주 쓰는 관계 `with()` 고정.
  - 대시보드 위젯(StatsOverview, LatestInvoices, ProjectProgress 등): 쿼리 수·인덱스 확인 후 필요 시 eager load, 캐시(단기) 도입.
  - `PaymentMatching` 등 Livewire 페이지: `getInvoices()`/`getDeposits()` 쿼리에서 필요한 관계 한 번에 로드.

### 2.6 예외 처리·로깅 (Medium)

- **현상**  
  `bootstrap/app.php`의 `withExceptions`가 비어 있음. 404·403·500에 대한 공통 메시지·로깅·알림 정책이 명시되지 않음.
- **권장**  
  - 프로덕션용 렌더링(사용자 친화적 메시지), 로그 레벨·채널, 중요 오류 시 알림(Slack 등) 설정.
  - 결제 매칭·재고 차감 등 **비즈니스 예외**는 전용 Exception + Handler에서 메시지·HTTP 상태 코드 통일.

### 2.7 인프라·운영 (Medium)

- **현상**  
  - `trustProxies(at: '*')`로 모든 프록시를 신뢰. 방화벽/리버스 프록시 뒤라도 범위를 제한하는 편이 안전.
  - Queue worker, Scheduler(cron), 마이그레이션 실행이 **Docker/배포 스크립트에서 명시적으로 문서화·자동화**되어 있지 않을 수 있음.
- **권장**  
  - `trustProxies`는 필요한 IP/서브넷으로 제한.
  - `docker-compose.prod` 또는 배포 가이드에 `queue:work`, `schedule:run`, `migrate` 실행 방법과 헬스체크(`/up`) 활용 명시.

### 2.8 문서·일관성 (Low)

- **현상**  
  - README에는 “스타트업 ERP”인데, `AdminPanelProvider`의 `brandName`이 **“스타트업 GRP”**로 되어 있음 (오타).
  - API 사용법, 환경 변수 전체 목록, 백업/복구, 업그레이드 가이드가 없음.
- **권장**  
  - 브랜드명을 “스타트업 ERP”로 통일.
  - `docs/`에 설치·설정·API·운영·기여 가이드 추가. `.env.example`에 모든 키와 설명 주석.

### 2.9 기타 QA 포인트

- **삭제 정책**  
  Soft delete된 레코드를 목록/통계에서 제외하는지, 휴지통 복구 시 연관 데이터 무결성 검토.
- **다국어**  
  Enum 라벨·Filament 라벨이 `lang/ko|en`에 누락 없이 매핑되어 있는지 점검.
- **접근성**  
  Filament 기본 a11y 유지; 커스텀 위젯·결제 매칭 등 키보드/스크린리더 동작 확인.
- **모바일**  
  언어 전환은 인라인 버튼으로 개선된 상태; 테이블·폼이 작은 화면에서 스크롤·탭 사용이 자연스러운지 확인.

---

## 3. 패키징 제안

### 3.1 “즉시 배포용” 패키징 (추천)

- **목적**  
  다른 팀/고객이 서버에 올려서 바로 사용할 수 있게 함.
- **구성**  
  - **Docker 이미지 1개**  
    PHP-FPM + Nginx(또는 Caddy) + Laravel 앱을 한 이미지로 빌드하거나, `docker-compose.prod.yml`에서 app + web + db + redis 한 번에 기동.
  - **환경 변수**  
    `.env.production.example` 제공 (APP_KEY, DB, REDIS, MAIL, QUEUE_CONNECTION 등).
  - **초기 실행 스크립트**  
    `migrate --force`, `db:seed --class=RolePermissionSeeder`(필요 시), `storage:link`, `config/route/view cache`, `queue:work` 백그라운드.
- **결과물**  
  “ERP Docker 이미지 + 문서”로 배포 패키지. SaaS가 아닌 온프레미스/단일 테넌트에 적합.

### 3.2 “스타터 키트” 패키징

- **목적**  
  Filament 기반 ERP를 커스터마이징해서 쓰고 싶은 개발자용.
- **구성**  
  - 현재 레포를 **GitHub 템플릿 저장소**로 두고, “Use this template”로 복제.
  - `docs/CUSTOMIZATION.md`: 모듈 추가/제거, 테마·메뉴 변경, 권한 추가, 새 리소스 패턴.
  - 선택: “코어”만 Composer 패키지(`startup/erp-core`)로 분리하고, 앱은 그 패키지를 의존하는 구조. (초기에는 과할 수 있음.)
- **결과물**  
  “Filament ERP Starter” 같은 이름으로 공개하고, 문서로 확장 가이드 제공.

### 3.3 “SaaS·멀티테넌트” 패키징 (중장기)

- **목적**  
  여러 고객사가 같은 인스턴스에서 “회사별로 분리된” ERP 사용.
- **구성**  
  - 테넌트 식별: `tenant_id` 컬럼 추가 또는 DB/schema per tenant.
  - 로그인·세션·Scope를 테넌트 단위로 격리.
  - 청구·플랜(사용자 수, 스토리지 등) 및 결제(Stripe 등) 연동.
- **결과물**  
  현재 단일 테넌트 구조를 점진적으로 테넌트 aware로 리팩터링한 뒤, “ERP SaaS” 상품으로 패키징.

### 3.4 마켓플레이스·오픈소스 배포

- **Filament 플러그인/테마**  
  Filament 공식 또는 커뮤니티 목록에 “ERP 모듈 번들” 또는 “대시보드 위젯 세트”로 등록.
- **오픈소스**  
  MIT 등 라이선스 명시, CHANGELOG·버전 태그, 기여 가이드(CODE_OF_CONDUCT, PR 템플릿) 추가 후 GitHub/GitLab 공개.
- **결과물**  
  “Laravel Filament ERP” 검색 시 나오는 참조 구현으로 사용되고, 이슈·PR로 품질 개선.

### 3.5 패키징 체크리스트 (공통)

- [ ] `CHANGELOG.md` (버전별 변경 사항)
- [ ] 라이선스 파일 (MIT 등)
- [ ] `.env.example` / `.env.production.example` 완비
- [ ] “최소 요구 사항”(PHP, DB, 확장) 문서
- [ ] 한 번에 실행 가능한 설치/배포 명령어 (Docker 또는 스크립트)
- [ ] 기본 관리자 계정 생성 방법 (`make:filament-user` 또는 시더)
- [ ] 보안 권장 사항 (HTTPS, APP_DEBUG=false, 로그·백업)

---

## 4. 요약

| 구분 | 상태 | 비고 |
|------|------|------|
| 기능 완성도 | 양호 | 인사·CRM·재무·프로젝트·구매·재고·승인·알림·다국어 등 핵심 도메인 커버 |
| 권한·감사 | 양호 | Spatie Permission + Activity Log + 부서 Scope |
| 테스트 | 미비 | tests/ 없음 → Unit/Feature 추가 권장 |
| API | 미비 | /user 외 실질 API 없음 → 확장 시 설계·문서화 필요 |
| 보안 세분화 | 개선 여지 | Policy 도입, trustProxies 범위 축소 |
| 성능 | 점검 권장 | N+1·위젯 쿼리·캐시 검토 |
| 문서·일관성 | 보완 권장 | 브랜드명 통일, env·API·운영 문서화 |
| 패키징 | 제안됨 | Docker 배포·스타터 키트·SaaS·오픈소스 옵션 정리 |

이 문서를 기준으로 우선순위(테스트 → API/Policy → 문서·패키징)를 정해 단계적으로 보완하면, 운영·배포·재사용성이 크게 올라갑니다.
