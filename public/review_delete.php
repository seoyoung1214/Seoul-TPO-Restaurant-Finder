<?php
// public/review_delete.php
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

// 1) 먼저, 이 리뷰가 로그인한 사용자의 것인지 + 어떤 레스토랑 것인지 확인
$stmt = $pdo->prepare("
    SELECT restaurant_id
    FROM reviews
    WHERE review_id = ? AND user_id = ?
");
$stmt->execute([$reviewId, $userId]);
$row = $stmt->fetch();

if (!$row) {
    exit('리뷰를 찾을 수 없거나 삭제 권한이 없습니다.');
}

$restaurantId = (int)$row['restaurant_id'];

try {
    // 2) 리뷰 삭제 + 해당 레스토랑 통계 재계산을 하나의 트랜잭션으로 처리
    $pdo->beginTransaction();

    // (a) 리뷰 삭제
    $del = $pdo->prepare("
        DELETE FROM reviews
        WHERE review_id = ? AND user_id = ?
    ");
    $del->execute([$reviewId, $userId]);

    // (b) 삭제된 리뷰가 속해 있던 레스토랑의 avg_rating, review_count 갱신
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
    // 개발 중에는 에러 메시지를 보고 싶으면 아래 주석을 잠깐 풀어도 됨
    // echo '에러: ' . $e->getMessage();
    exit('리뷰 삭제 중 오류가 발생했습니다. 잠시 후 다시 시도해 주세요.');
}
