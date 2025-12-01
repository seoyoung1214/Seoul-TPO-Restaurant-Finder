# 🍽️ Seoul TPO Restaurant Finder: Data-Driven Recommendation System

## 프로젝트 개요

### 1. 시스템 필요성 (Why This System is Needed)

오늘날 서울에는 수많은 맛집 정보가 존재하지만, 정작 **'함께하는 사람', '시간', '목적지'** 등 개인화된 **TPO (Time, Place, Occasion)** 요소를 동시에 고려하여 최적의 식당을 찾는 것은 매우 비효율적입니다.

* **비효율적인 의사결정:** 모임의 **출발지가 다를 때** 최적의 중간 지점을 찾거나, **데이트/비즈니스 미팅** 등 목적에 맞는 분위기를 보장하는 식당을 찾는 과정에서 많은 시간과 에너지 낭비가 발생합니다.
* **단순 검색의 한계:** 기존의 검색 시스템은 '평점'이나 '음식 종류' 등 단일 필터만 제공할 뿐, 복합적인 상황 판단(예: "금요일 저녁, 강남역 근처, 캐주얼한 친구 모임에 적합한 가성비 좋은 한식집")을 지원하지 못합니다.

### 2. 해결 방안 및 시스템 가치

본 **Seoul TPO Restaurant Finder** 시스템은 이러한 비효율성을 해소하고, 데이터 기반의 스마트한 의사결정을 지원합니다.

* **TPO 기반 맞춤 추천:** 사용자가 입력한 **복합적인 TPO 데이터** (지역, 시간대, 모임 목적)를 기반으로 레스토랑 리뷰 데이터를 분석하여, **현재 상황에 가장 부합하는** 음식점을 추천합니다.
* **고급 데이터 분석 시뮬레이션:** 단순한 조회 기능을 넘어, **Rollup, 랭킹, 윈도잉, 복합 그룹핑**과 같은 고급 분석 기법을 웹사이트에 구현함으로써, 사용자가 **직접** 데이터 트렌드를 파악하고 최적의 선택을 내릴 수 있도록 돕습니다.
* **데이터 무결성 및 신뢰성:** 리뷰 작성 시 **트랜잭션**을 사용하여 평점 및 리뷰 수 통계를 실시간으로 정확하게 반영함으로써, 시스템이 제공하는 모든 정보의 **신뢰성**을 보장합니다.

**요약:** 이 시스템은 단순한 맛집 리스트업이 아닌, **복잡한 사회적 상황**에 직면한 사용자에게 **가장 합리적이고 효율적인 식사 장소 결정권**을 제공하는 **데이터 기반의 의사결정 지원 플랫폼**입니다.

---

## 개발 환경 및 기술 스택

| 구분 | 기술 스택 | 특징 및 사용 목적 |
| :--- | :--- | :--- |
| **플랫폼** | XAMPP | 설치 및 실행 환경 통일 |
| **웹 서버** | Apache | XAMPP 기본 제공 |
| **데이터베이스** | **MariaDB** | MySQL 호환, **`teamXX`** 계정 사용 (할당된 DB 계정명) |
| **백엔드** | **PHP 7/8** | 서버 로직 및 DB 연동 담당. **PreparedStatements** 필수 사용. |
| **프론트엔드** | HTML, CSS, JS | UI/UX 구현 (Bootstrap 기반 권장) |

    teamXX/
    ├── config/
    │   ├── config.php        # DB 접속 정보(teamXX/teamXX) 설정 파일
    │   └── db.php            # PDO 기반 DB 연결 및 핸들링 로직
    │
    ├── sql/
    │   ├── dbcreate.sql      # 테이블 및 스키마 생성 스크립트 (PK/FK/Index 포함)
    │   ├── dbinsert.sql      # 대규모 초기 데이터 삽입 스크립트 (1000+ rows)
    │   ├── dbdrop.sql        # 테이블 삭제 스크립트
    │   └── dbdump.sql        # 최종 mysqldump 결과 파일
    │
    ├── public/
    │   ├── index.php         # 메인 페이지 및 기본 접근 페이지
    │   ├── login.php         # 사용자 로그인 처리
    │   ├── logout.php        # 사용자 로그아웃 처리
    │   ├── register.php      # 사용자 회원가입 처리
    │   ├── search.php        # TPO 기반 복합 검색 및 SELECT 페이지
    │   ├── reviews.php       # 전체 리뷰 목록 조회 페이지
    │   │
    │   ├── review_create.php # 리뷰 INSERT (트랜잭션 적용)
    │   ├── review_edit.php   # 리뷰 UPDATE 처리
    │   ├── review_delete.php # 리뷰 DELETE 처리
    │   │
    │   ├── analysis_group.php  # SQL 복합 그룹핑(GROUP BY) 분석 결과 표시
    │   ├── analysis_rollup.php # SQL ROLLUP / Drill-down 분석 결과 표시
    │   ├── analysis_rank.php   # SQL Ranking(RANK/DENSE_RANK) 분석 결과 표시
    │   ├── analysis_window.php # SQL Windowing Function 분석 결과 표시
    │   │
    │   ├── header.php        # 모든 페이지에 공통으로 포함되는 상단 메뉴 구조
    │   └── footer.php        # 모든 페이지에 공통으로 포함되는 하단 정보
    │
    └── assets/
        └── style.css         # (선택) UI/UX 개선을 위한 전역 CSS 스타일

---

## Database Schema
<img width="1747" height="762" alt="image" src="https://github.com/user-attachments/assets/5769c1e5-0dc0-4d1e-8a66-f738e3b9342b" />

## Web Application Architecture
<img width="739" height="521" alt="image" src="https://github.com/user-attachments/assets/bef09fe9-d482-42fd-97b7-3874eaec457b" />

---

## 설치 및 실행 방법

제출된 코드만으로 다른 테스트 서버(XAMPP 설치됨)에 쉽게 설치 및 실행 가능해야 합니다.

### 1. 환경 설정

1.  **XAMPP 실행:** Apache와 MariaDB 서비스를 시작합니다.
2.  **프로젝트 폴더 이동:** 프로젝트 폴더(`teamXX`)를 XAMPP 설치 경로의 `htdocs` 폴더 아래에 복사합니다.
3.  **DB 접속 정보 설정:** 할당된 DB 계정 정보로 `config/config.php` 및 `config/db.php` 파일을 수정합니다.

### 2. 데이터베이스 초기화

MariaDB 콘솔 또는 PHPMyAdmin을 사용하여 `sql/` 폴더의 스크립트를 실행합니다.

```bash
# 1. DB 콘솔 접속
mysql -u teamXX -p

# 2. 데이터베이스 사용
USE teamXX;

# 3. 테이블 생성 및 데이터 삽입 (권장 순서)
SOURCE sql/dbdrop.sql;    # 기존 테이블 삭제
SOURCE sql/dbcreate.sql;  # 새 테이블 생성 (PK/FK/Index 포함)
SOURCE sql/dbinsert.sql;  # 초기 데이터 삽입 (최소 1000+ rows)

# 4. 최종 DB 덤프 파일 생성
# mysqldump -u teamXX -p teamXX > sql/dbdump.sql

---
## Database Schema
<img width="1747" height="762" alt="image" src="https://github.com/user-attachments/assets/5769c1e5-0dc0-4d1e-8a66-f738e3b9342b" />
---
## Web Application Architecture
<img width="739" height="521" alt="image" src="https://github.com/user-attachments/assets/bef09fe9-d482-42fd-97b7-3874eaec457b" />

