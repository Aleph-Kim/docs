# Docs

Claude로 생성한 완결형 단일 HTML 시각화 문서를 저장·분류·열람하는 개인용 아카이브.

- PHP 8.3 / Laravel 13 / MariaDB 11.8
- 단순 MVC (Controller + Eloquent Model), 스타터 킷·`users` 테이블 없음
- 관리자 로그인은 환경변수(`ADMIN_ID` / bcrypt 해시) + 세션 플래그 기반

## 로컬 설치

```bash
# 1. 저장소 클론 후 의존성 설치
composer install
npm install

# 2. 환경 파일
cp .env.example .env
php artisan key:generate

# 3. .env 에서 DB_* 와 ADMIN_ID 설정 (DB_HOST 는 도커 기준 host.docker.internal)

# 4. 관리자 비밀번호 해시 생성 → .env 의 ADMIN_PASSWORD_HASH 에 따옴표 없이 붙여넣기
php artisan tinker --execute="echo bcrypt('원하는비밀번호');"

# 5. 호스트 MariaDB 에 DB 생성
#    CREATE DATABASE docs CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# 6. 마이그레이션 + 카테고리 시드
php artisan migrate --seed

# 7. 프론트엔드 빌드
npm run build

# 8-a. 도커로 실행 (참고 프로젝트와 동일한 구조)
docker network create local_net   # 최초 1회
docker compose up -d               # http://localhost

# 8-b. 도커 없이 실행
php artisan serve
```

관리자 화면: `/admin/login`

## 테스트

```bash
composer test   # SQLite 인메모리 DB 사용
```

## 배포 (GitHub Actions)

`main` 브랜치 push 시 `.github/workflows/cicd.yml` 이 실행된다: **test → build(DockerHub push) → deploy(SSH)**.

필요한 저장소 Secrets:


| Secret                                          | 설명                                                |
| ----------------------------------------------- | ------------------------------------------------- |
| `DOCKER_USERNAME` / `DOCKER_PASSWORD`           | DockerHub 로그인                                     |
| `PROJECT_NAME`                                  | 이미지·컨테이너 이름                                       |
| `SERVER_IP` / `SERVER_USER` / `SSH_PRIVATE_KEY` | 배포 서버 SSH 접속                                      |
| `ENV_PATH`                                      | 서버의 `.env` 절대경로 (컨테이너 `/var/www/html/.env` 로 마운트) |
| `LOG_DIR_PATH`                                  | 서버의 로그 디렉터리 (컨테이너 `storage/logs` 로 마운트)           |
| `NETWORK_NAME`                                  | 리버스 프록시와 공유하는 도커 네트워크                             |


배포 컨테이너는 프로덕션 `Dockerfile`(멀티스테이지: Node 프론트엔드 빌드 + `php:8.3-apache`)로 빌드되며,
DB 는 컨테이너에 포함하지 않고 `.env` 의 `DB_HOST` 로 외부 MariaDB 에 접속한다.

## HTML 렌더링 방식

저장된 HTML 은 완결형 문서이므로 Blade 에 직접 출력하지 않는다.

- `GET /visuals/{slug}/raw` 가 원문만 `text/html` 로 반환 (`X-Frame-Options: SAMEORIGIN`)
- 상세 페이지는 이 라우트를 `<iframe sandbox="allow-scripts allow-popups">` 로 로드
- `allow-same-origin` 은 넣지 않는다 → 인라인 스크립트·외부 CDN 은 동작하지만 `localStorage` 계열은 차단
- iframe 높이는 고정 + 전체화면 토글 버튼으로 처리

