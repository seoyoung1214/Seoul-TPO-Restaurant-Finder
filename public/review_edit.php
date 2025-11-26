<?php
// public/review_edit.php
// 접속 : http://localhost:8080/team12/public/review_edit.php?id=1024
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . "/header.php";


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

// 본인 리뷰인지 확인하며 로드 (레스토랑 id도 같이 가져오기)
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

            header('Location: reviews.php');
            exit();
        } catch (PDOException $e) {
            $pdo->rollBack();
            $error = '리뷰 수정 중 오류가 발생했습니다. 잠시 후 다시 시도해 주세요.';
            // 개발 중에는 에러 확인용으로 아래를 잠깐 열어볼 수 있음 (배포 시엔 주석!)
            // $error .= ' (' . $e->getMessage() . ')';
        }
    }
}

include 'header.php';
?>

<h2>리뷰 수정</h2>

<p>레스토랑: <strong><?= htmlspecialchars($review['restaurant_name'], ENT_QUOTES, 'UTF-8') ?></strong></p>

<form method="post">
    <div>
        <label>평점 (1~5):
            <input type="number" name="rating_score" min="1" max="5"
                   value="<?= htmlspecialchars($review['rating_score'], ENT_QUOTES, 'UTF-8') ?>" required>
        </label>
    </div>
    <div>
        <label>사용 금액(원):
            <input type="number" name="spend_amount" min="1"
                   value="<?= htmlspecialchars($review['spend_amount'], ENT_QUOTES, 'UTF-8') ?>" required>
        </label>
    </div>
    <div>
        <label>리뷰 내용:</label><br>
        <textarea name="comment" rows="5" cols="50" required><?= htmlspecialchars($review['comment'], ENT_QUOTES, 'UTF-8') ?></textarea>
    </div>
    <button type="submit">수정 완료</button>
    <a href="reviews.php">목록으로</a>
</form>

<?php if ($error): ?>
    <p style="color:red;"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
<?php endif; ?>

<?php include 'footer.php'; ?>
