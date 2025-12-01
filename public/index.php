<?php
session_start();
require_once __DIR__ . "/header.php";
require_once __DIR__ . '/../config/db.php';

// 데이터베이스 연결이 필요한 경우 (예: 나중에 통계 표시 등)
$pdo = getDB();

?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>Seoul TPO Restaurant Finder - 홈</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        /* 메인 페이지 전용 스타일 */
        .main-content {
            padding: 40px 20px;
            max-width: 900px;
            margin: 0 auto; /* 중앙 정렬 */
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin-top: 30px;
        }
        h1 {
            font-size: 2.5em;
            color: #007bff;
            margin-bottom: 5px;
        }
        .welcome-message {
            padding: 15px;
            margin-bottom: 25px;
            border-left: 5px solid #007bff;
            background-color: #f4f7ff;
            border-radius: 4px;
        }
        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); /* 반응형 2~3열 */
            gap: 20px;
            list-style: none;
            padding: 0;
            margin-top: 20px;
        }
        .menu-item a {
            display: block;
            text-decoration: none;
            padding: 20px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            background-color: #f8f8f8;
            color: #333;
            font-size: 1.1em;
            transition: all 0.2s;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.03);
        }
        .menu-item a:hover {
            background-color: #e6f0ff;
            border-color: #007bff;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            color: #007bff;
        }
        .menu-item span {
            font-size: 1.5em;
            margin-right: 10px;
        }
        .logout-link {
            display: inline-block;
            margin-top: 10px;
            padding: 5px 10px;
            background-color: #f0f0f0;
            border-radius: 4px;
            text-decoration: none;
            color: #333;
        }
    </style>
</head>

<body>

<div class="main-content">

    <h1>Seoul TPO Restaurant Finder</h1>

    <div class="welcome-message">
        <?php if (isset($_SESSION['username'])): ?>
            <p>
                <strong><?= htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8') ?></strong> 님, 환영합니다 👋
            </p>
            <p>
                <a href="logout.php" class="logout-link">로그아웃</a>
            </p>
        <?php else: ?>
            <p style="font-size: 1.1em;">
                <a href="login.php" style="font-weight: bold;">로그인</a> /
                <a href="register.php">회원가입</a>
                후 TPO 맞춤 맛집을 확인해 보세요.
            </p>
        <?php endif; ?>
    </div>

    <hr style="margin-top: 30px; margin-bottom: 30px;">

    <h2>메뉴</h2>
    
    <ul class="menu-grid">
        <li class="menu-item"><a href="search.php"><span role="img" aria-label="Search">🔍</span> TPO 기반 맛집 검색</a></li>
        <li class="menu-item"><a href="reviews.php"><span role="img" aria-label="Review List">⭐</span> 리뷰 목록</a></li>
        <li class="menu-item"><a href="review_create.php"><span role="img" aria-label="Write Review">✏️</span> 리뷰 작성</a></li>
        <li class="menu-item"><a href="analysis_group.php"><span role="img" aria-label="Group Analysis">📊</span> TPO 복합 그룹 분석</a></li>
        <li class="menu-item"><a href="analysis_rollup.php"><span role="img" aria-label="Rollup Analysis">🔽</span> 구/시간/목적별 ROLLUP 분석</a></li>
        <li class="menu-item"><a href="analysis_rank.php"><span role="img" aria-label="Ranking Analysis">🏆</span> TPO 조건별 레스토랑 랭킹 분석</a></li>
        <li class="menu-item"><a href="analysis_window.php"><span role="img" aria-label="Windowing Analysis">☑️</span> 레스토랑 평점 추이 분석 (윈도잉)</a></li>
    </ul>

    <?php include 'footer.php'; ?>

</div>

</body>
</html>