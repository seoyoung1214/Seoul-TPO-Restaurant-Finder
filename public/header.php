<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>TPO Restaurant Finder</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container-fluid">
        <a class="navbar-brand" href="/Seoul-TPO-Restaurant-Finder/public/index.php">TPO Finder</a>

        <div class="collapse navbar-collapse">
            <ul class="navbar-nav me-auto">

                <li class="nav-item">
                    <a class="nav-link" href="/Seoul-TPO-Restaurant-Finder/public/search.php">맛집 검색</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="/Seoul-TPO-Restaurant-Finder/public/reviews.php">리뷰 목록</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="/Seoul-TPO-Restaurant-Finder/public/review_create.php">리뷰 작성</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/Seoul-TPO-Restaurant-Finder/public/analysis_rank.php">가게 랭킹</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/Seoul-TPO-Restaurant-Finder/public/analysis_group.php">그룹핑 분석</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/Seoul-TPO-Restaurant-Finder/public/analysis_rollup.php">롤업 분석</a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link" href="/Seoul-TPO-Restaurant-Finder/public/analysis_window.php">윈도잉 분석</a>
                </li>
            </ul>

            <ul class="navbar-nav">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="nav-item">
                        <span class="nav-link text-light">
                            <?= $_SESSION['username'] ?> 님
                        </span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/Seoul-TPO-Restaurant-Finder/public/logout.php">Logout</a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/Seoul-TPO-Restaurant-Finder/public/login.php">Login</a>
                    </li>
                <?php endif; ?>
            </ul>

        </div>
    </div>
</nav>
