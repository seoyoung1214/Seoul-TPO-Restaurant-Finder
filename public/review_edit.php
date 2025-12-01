<?php
session_start();
require_once __DIR__ . '/../config/db.php';


if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$pdo = getDB();
$userId   = (int)$_SESSION['user_id'];
$reviewId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($reviewId <= 0) {
    exit('잘못된 접근입니다.');
}

$stmt = $pdo->prepare("
    SELECT 
        r.review_id,
        r.restaurant_id,
        r.rating_score,
        r.spend_amount,
        r.comment,
        res.name AS restaurant_name
    FROM reviews r
    JOIN restaurants res ON r.restaurant_id = res.restaurant_id
    WHERE r.review_id = ? AND r.user_id = ?
");
$stmt->execute([$reviewId, $userId]);
$review = $stmt->fetch();

if (!$review) {
    exit('리뷰를 찾을 수 없거나 수정 권한이 없습니다.');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rating  = (int)($_POST['rating_score'] ?? 0);
    $spend   = (int)($_POST['spend_amount'] ?? 0);
    $comment = trim($_POST['comment'] ?? '');

    if ($rating < 1 || $rating > 5) {
        $error = '평점은 1~5 사이로 입력해 주세요.';
    } elseif ($spend <= 0) {
        $error = '사용 금액을 1원 이상으로 입력해 주세요.';
    } elseif ($comment === '') {
        $error = '리뷰 내용을 입력해 주세요.';
    } else {
        $restaurantId = (int)$review['restaurant_id'];

        try {
            // 리뷰 수정 + 해당 레스토랑 통계 갱신을 하나의 트랜잭션으로 처리
            $pdo->beginTransaction();

            // 1) 리뷰 내용 업데이트
            $update = $pdo->prepare("
                UPDATE reviews
                SET rating_score = ?, spend_amount = ?, comment = ?
                WHERE review_id = ? AND user_id = ?
            ");
            $update->execute([$rating, $spend, $comment, $reviewId, $userId]);

            // 2) 이 레스토랑의 avg_rating, review_count 재계산
            $stats = $pdo->prepare("
                UPDATE restaurants r
                SET 
                    avg_rating = (
                        SELECT COALESCE(ROUND(AVG(rv.rating_score), 2), 0.0)
                        FROM reviews rv
                        WHERE rv.restaurant_id = r.restaurant_id
                    ),
                    review_count = (
                        SELECT COUNT(*)
                        FROM reviews rv
                        WHERE rv.restaurant_id = r.restaurant_id
                    )
                WHERE r.restaurant_id = ?
            ");
            $stats->execute([$restaurantId]);

            $pdo->commit();

            // 수정 후 리뷰 목록으로 리다이렉트
            header('Location: reviews.php');
            exit();
        } catch (PDOException $e) {
            $pdo->rollBack();
            $error = '리뷰 수정 중 오류가 발생했습니다. 잠시 후 다시 시도해 주세요.';
        }
    }
}

// 수정된 데이터가 POST 요청에 의해 반영되지 않았다면, DB에서 다시 데이터를 가져와 폼에 표시합니다.
if ($review) {
    $review['rating_score'] = $_POST['rating_score'] ?? $review['rating_score'];
    $review['spend_amount'] = $_POST['spend_amount'] ?? $review['spend_amount'];
    $review['comment'] = $_POST['comment'] ?? $review['comment'];
}


include 'header.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>리뷰 수정</title>
    <!-- 공통 style.css 링크 추가 --><link rel="stylesheet" href="../css/style.css">
    <style>
        /* 리뷰 수정 페이지 전용 스타일 (review_create와 유사) */
        .page-wrapper {
            display: flex;
            justify-content: center;
            padding: 50px 20px;
            min-height: 80vh;
        }
        .form-container {
            max-width: 600px;
            width: 100%;
            padding: 30px;
            border: 1px solid #ddd;
            border-radius: 10px;
            background-color: #ffffff;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }
        
        h2 {
            color: #333;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
            margin-bottom: 25px;
            font-size: 1.8em;
        }
        
        /* 폼 그룹 스타일 */
        .form-group { margin-bottom: 15px; }
        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
            color: #555;
        }
        /* input과 textarea의 max-width를 동일하게 설정하여 너비 일치 */
        .form-group input, .form-group textarea {
            width: 100%;
            max-width: 300px; /* 리뷰 생성 페이지의 입력 필드 너비와 동일하게 조정 */
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            box-sizing: border-box;
            transition: border-color 0.3s;
        }
        .form-group input:focus, .form-group textarea:focus {
            border-color: #007bff;
            outline: none;
        }
        textarea { 
            height: 100px; 
            resize: vertical; 
        }

        /* 버튼 그룹 */
        .button-group { margin-top: 20px; }
        .btn-submit, .btn-secondary-link {
            padding: 10px 15px; 
            border: none; 
            border-radius: 4px; 
            cursor: pointer; 
            font-size: 1em;
            text-decoration: none;
            display: inline-block;
            transition: background-color 0.2s;
        }

        .btn-submit { 
            background-color: #007bff;
            color: white; 
            margin-right: 10px;
        }
        .btn-submit:hover {
            background-color: #0056b3;
        }
        .btn-secondary-link {
            background-color: #6c757d;
            color: white;
        }
        .btn-secondary-link:hover {
            background-color: #5a6268;
        }

        /* 에러 메시지 스타일 */
        .message-error {
            padding: 10px;
            border-radius: 4px;
            background-color: #f8d7da; 
            color: #721c24; 
            border: 1px solid #f5c6cb;
            margin-top: 15px;
        }
        /* 레스토랑 이름 표시 */
        .restaurant-info {
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f0f8ff;
            border-left: 4px solid #007bff;
            font-size: 1.1em;
        }
    </style>
</head>
<body>
<div class="page-wrapper">
    <div class="form-container">
        <h2>리뷰 수정</h2>

        <div class="restaurant-info">
            레스토랑: <strong><?= htmlspecialchars($review['restaurant_name'], ENT_QUOTES, 'UTF-8') ?></strong>
        </div>

        <form method="post">
            <div class="form-group">
                <label>평점 (1~5):
                    <input type="number" name="rating_score" min="1" max="5"
                            value="<?= htmlspecialchars($review['rating_score'], ENT_QUOTES, 'UTF-8') ?>" required>
                </label>
            </div>
            <div class="form-group">
                <label>사용 금액(원):
                    <input type="number" name="spend_amount" min="1"
                            value="<?= htmlspecialchars($review['spend_amount'], ENT_QUOTES, 'UTF-8') ?>" required>
                </label>
            </div>
            <div class="form-group">
                <label>리뷰 내용:</label>
                <textarea name="comment" rows="5" required><?= htmlspecialchars($review['comment'], ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>
            
            <div class="button-group">
                <button type="submit" class="btn-submit">수정 완료</button>
                <a href="reviews.php" class="btn-secondary-link">목록으로</a>
            </div>
        </form>

        <?php if ($error): ?>
            <p class="message-error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>
    </div>
</div>

<?php include 'footer.php'; ?>
</body>
</html>