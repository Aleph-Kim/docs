# Docs

Claude로 생성한 완결형 단일 HTML 시각화 문서를 저장·분류·열람하는 개인용 아카이브.

- PHP 8.3 / Laravel 13 / MariaDB 11.8
- 단순 MVC (Controller + Eloquent Model), 스타터 킷·`users` 테이블 없음
- 관리자 로그인은 환경변수(`ADMIN_ID` / `ADMIN_PASSWORD`) + 세션 플래그 기반

## 로컬 설치

```bash
# 1. 저장소 클론 후 의존성 설치
composer install
npm install

# 2. 환경 파일
cp .env.example .env
php artisan key:generate

# 3. .env 에서 DB_* 와 ADMIN_ID, ADMIN_PASSWORD 설정 (DB_HOST 는 도커 기준 host.docker.internal)

# 4. 호스트 MariaDB 에 DB 생성
#    CREATE DATABASE docs CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# 5. 마이그레이션 + 카테고리 시드
php artisan migrate --seed

# 6. 프론트엔드 빌드
npm run build

# 7-a. 도커로 실행 (참고 프로젝트와 동일한 구조)
docker network create local_net   # 최초 1회
docker compose up -d               # http://localhost

# 7-b. 도커 없이 실행
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

| Secret                                          | 설명                                                                         |
|-------------------------------------------------|----------------------------------------------------------------------------|
| `DOCKER_USERNAME` / `DOCKER_PASSWORD`           | DockerHub 로그인                                                              |
| `PROJECT_NAME`                                  | 이미지·컨테이너 이름                                                                |
| `SERVER_IP` / `SERVER_USER` / `SSH_PRIVATE_KEY` | 배포 서버 SSH 접속                                                               |
| `ENV_PATH`                                      | 서버의 `.env` 절대경로 (컨테이너 `/var/www/html/.env` 로 마운트)                          |
| `LOG_DIR_PATH`                                  | 서버의 로그 디렉터리 (컨테이너 `storage/logs` 로 마운트)                                    |
| `UPLOADS_DIR_PATH`                              | 서버의 업로드 디렉터리 (컨테이너 `storage/app/public` 로 마운트, 시각화 HTML 보관, 컨테이너 재생성에도 유지) |
| `NETWORK_NAME`                                  | 리버스 프록시와 공유하는 도커 네트워크                                                      |

첫 배포 전 `UPLOADS_DIR_PATH` 디렉터리를 만들고 컨테이너의 `www-data`(uid:gid `33:33`) 소유로 지정해야 한다.
없으면 Docker 가 `root` 소유로 생성해 등록/수정 시 쓰기 권한 오류가 난다.

```bash
sudo mkdir -p "$UPLOADS_DIR_PATH/visuals"
sudo chown -R 33:33 "$UPLOADS_DIR_PATH"
sudo chmod -R 755 "$UPLOADS_DIR_PATH"
```

배포 컨테이너는 프로덕션 `Dockerfile`(멀티스테이지: Node 프론트엔드 빌드 + `php:8.3-apache`)로 빌드되며,
DB 는 컨테이너에 포함하지 않고 `.env` 의 `DB_HOST` 로 외부 MariaDB 에 접속한다.

## HTML 렌더링 방식

업로드된 HTML 은 완결형 문서이므로 DB 가 아닌 파일로 저장하고 웹서버가 정적으로 서빙한다.

- 업로드 시 `public` 디스크(`storage/app/public/visuals/Y/m/d/`)에 저장, 메타데이터는 `files` 테이블(다형성)
- 애플리케이션은 파일 URL(`Storage::disk('public')->url(...)`)만 반환하고 실물은 Apache 가 서빙 (PHP 미경유)
- 상세 페이지는 이 URL 을 `<iframe sandbox="allow-scripts allow-popups">` 로 로드
- `allow-same-origin` 은 넣지 않는다 → 인라인 스크립트·외부 CDN 은 동작하지만 `localStorage` 계열은 차단
- iframe 높이는 고정 + 전체화면 토글 버튼으로 처리
- 툴바의 "새 탭에서 열기"는 파일 URL 을 최상위 문서로 직접 여는 것이라 iframe sandbox 가 적용되지 않는다(같은 도메인 origin 으로 로드됨, 업로드자가 신뢰 가능하다는 전제)

