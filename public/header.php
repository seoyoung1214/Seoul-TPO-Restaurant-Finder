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
        <a class="navbar-brand" href="/team05/index.php">TPO Finder</a>

        <div class="collapse navbar-collapse">
            <ul class="navbar-nav me-auto">

                <li class="nav-item">
                    <a class="nav-link" href="/team05/search.php">Search</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="/team05/analysis_group.php">Analysis</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="/team05/my_reviews.php">My Reviews</a>
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
                        <a class="nav-link" href="/team05/logout.php">Logout</a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/team05/login.php">Login</a>
                    </li>
                <?php endif; ?>
            </ul>

        </div>
    </div>
</nav>
