<?php
// public/index.php
session_start();
require_once __DIR__ . "/header.php";
require_once __DIR__ . '/../config/db.php';

?>

<h1>Seoul TPO Restaurant Finder</h1>

<?php if (isset($_SESSION['username'])): ?>
    <p>
        <strong><?= htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8') ?></strong> 님, 환영합니다 👋
    </p>
    <p>
        <a href="logout.php">로그아웃</a>
    </p>
<?php else: ?>
    <p>
        <a href="login.php">로그인</a> /
        <a href="register.php">회원가입</a>
        후 TPO 맞춤 맛집을 확인해 보세요.
    </p>
<?php endif; ?>

<hr>

<h2>메뉴</h2>
<ul>
    <li><a href="search.php">🔍 TPO 기반 맛집 검색</a></li>
    <li><a href="reviews.php">⭐ 리뷰 목록</a></li>
    <li><a href="review_create.php">✏️ 리뷰 작성</a></li>
    <li><a href="analysis_group.php">📊 TPO 복합 그룹 분석</a></li>
    <li><a href="analysis_rollup.php">🔽 구/시간/목적별 ROLLUP 분석</a></li>
    <li><a href="analysis_rank.php">🏆 TPO 조건별 레스토랑 랭킹 분석</a></li>
    <li><a href="analysis_window.php">☑️ 레스토랑 평점 추이 분석 (윈도잉)</a></li>
</ul>

<?php include 'footer.php'; ?>
