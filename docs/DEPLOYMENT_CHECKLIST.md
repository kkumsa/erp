# 최종 배포 체크리스트

배포 전 확인·실행 항목입니다.

---

## 1. 문법·로직 검사 결과 (검수 완료)

| 항목 | 상태 | 비고 |
|------|------|------|
| PHP 문법 | ✅ | 주요 파일 린트 오류 없음 |
| Blade/뷰 | ✅ | list-table-wrapper, slide-over-panel, payment-matching 등 검수 완료 |
| List 페이지 공통 로직 | ✅ | closePanel, slideOverMode, selectRecord 등 30개 리소스 동일 패턴·정상 참조 |
| PaymentMatching | ✅ | getInvoices() status 값이 InvoiceStatus enum 값(issued, partially_paid, overdue)과 일치 |
| 슬라이드 패널 | ✅ | document 클릭은 init()에서 1회 등록, 테이블 밖/데이터 행 구분 로직 적용 |

---

## 2. 배포 전 실행 권장 명령

```bash
# 캐시·뷰 초기화
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# (선택) 라우트·설정 재생성
php artisan route:clear
php artisan config:cache

# (선택) PHP 문법 일괄 확인
find app -name "*.php" -exec php -l {} \; 2>&1 | grep -v "No syntax errors"
```

---

## 3. 환경·설정 확인

- [ ] `.env`에서 `APP_ENV=production`, `APP_DEBUG=false` 확인
- [ ] `APP_NAME` 등 브랜드 설정 확인 (스타트업 GRP)
- [ ] DB 마이그레이션 적용 여부 (`php artisan migrate --force`)
- [ ] 스토리지·캐시 디렉터리 쓰기 권한 (`storage`, `bootstrap/cache`)

---

## 4. 배포 후 브라우저 확인 권장

- [ ] 로그인/로그아웃 (한·영 전환 유지)
- [ ] 대시보드 (로그인 정보·진행 중인 프로젝트 위젯, 50:50 레이아웃)
- [ ] 목록 페이지: 가로 스크롤, 슬라이드 오버, **테이블 밖 클릭 시 슬라이드 아웃**
- [ ] 그룹 테이블(프로젝트 등): 그룹 헤더 클릭 시 슬라이드 아웃, 데이터 행 클릭 시 패널 유지
- [ ] 결제(청구/입금) 관리: 드래그앤드롭·모바일 탭 매칭
- [ ] 휴지통: 전체 필터, 복원/완전 삭제

---

## 5. 알려진 구조 (추가 정리 시 참고)

- **List* 페이지**: 30개 리소스에 `slideOverMode`, `selectedRecordId`, `selectRecord`, `closePanel`, `setSlideOverMode` 동일 패턴 존재. 추후 Trait로 통합 가능.
- **뷰**: 목록 테이블은 `<x-list-table-wrapper>`로 통일되어 있어, 슬라이드/스크롤 로직 변경 시 해당 컴포넌트만 수정하면 됨.

---

## 6. 정리된 코드 요약

- **목록 뷰**: 30개 리소스 모두 `<x-list-table-wrapper>` 사용. 슬라이드 바깥 클릭·가로 스크롤은 컴포넌트·전역 CSS 한 곳에서 관리.
- **슬라이드 패널**: `init()`에서 document 클릭 1회 등록. 테이블 밖 클릭 → 닫기, 테이블 내 데이터 행 클릭 → 유지, 그룹 헤더/헤더 클릭 → 닫기.
- **결제 매칭**: 인라인 스타일을 `.pm-*` 클래스로 정리. Enum·모델 참조 일치 확인됨.
- **전역 CSS**: AdminPanelProvider 내 `.fi-sidebar-group` 통합, 목록 가로 스크롤·위젯·페이지네이션 스타일 유지.

## 7. 참고 문서

- `docs/CODE_AUDIT_2026.md` — 코드 전수검사·정리 요약
- `docs/PROJECT_ANALYSIS_AND_QA.md` — 프로젝트 분석·QA
