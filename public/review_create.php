<?php
// db.php 파일 include (PDO 연결 객체 $pdo를 사용한다고 가정)
require_once '../config/db.php';
session_start();

// 1. 로그인 상태 확인 (세션 활용)
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 2. 사용자 입력 데이터 및 세션 정보 추출
    $user_id = $_SESSION['user_id'];
    $restaurant_id = $_POST['restaurant_id'];
    $occasion_id = $_POST['occasion_id'];
    $time_slot_id = $_POST['time_slot_id'];
    $rating_score = $_POST['rating_score'];
    $spend_amount = $_POST['spend_amount'];
    $comment = $_POST['comment'];

    // =======================================================
    // 3. 트랜잭션 시작
    // =======================================================
    $pdo->beginTransaction();

    try {
        // A. reviews 테이블에 새 리뷰 INSERT
        $sql_insert_review = "INSERT INTO reviews (user_id, restaurant_id, occasion_id, time_slot_id, rating_score, spend_amount, comment) 
                              VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt_insert = $pdo->prepare($sql_insert_review);
        $stmt_insert->execute([$user_id, $restaurant_id, $occasion_id, $time_slot_id, $rating_score, $spend_amount, $comment]);
        
        // B. 레스토랑 통계 갱신 (평균 평점 및 리뷰 수 재계산)
        // 서브쿼리를 사용하여 현재 시점의 총합과 개수를 구함
        $sql_update_stats = "UPDATE restaurants SET 
                                avg_rating = (SELECT AVG(rating_score) FROM reviews WHERE restaurant_id = ?),
                                review_count = (SELECT COUNT(*) FROM reviews WHERE restaurant_id = ?)
                             WHERE restaurant_id = ?";
        $stmt_update = $pdo->prepare($sql_update_stats);
        $stmt_update->execute([$restaurant_id, $restaurant_id, $restaurant_id]);

        // 4. 트랜잭션 성공 시 커밋
        $pdo->commit();
        $message = "리뷰 등록 및 통계 갱신 성공!";

    } catch (Exception $e) {
        // 5. 오류 발생 시 롤백
        $pdo->rollBack();
        $message = "리뷰 등록 실패: " . $e->getMessage();
        // 실제 운영 환경에서는 오류 로깅만 하고 사용자에게는 일반적인 메시지 제공
    }
}
// HTML 폼 및 결과 메시지 출력 부분...
?>