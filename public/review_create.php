<?php
// db.php 파일 include (PDO 연결 객체 $pdo를 사용한다고 가정)
require_once '../config/db.php';

// =======================================================
// [임시 조치] header.php의 역할을 대신하여 세션을 시작합니다.
// =======================================================
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// db.php 파일에 정의된 getDB() 함수를 호출하여 PDO 객체를 $pdo 변수에 할당
$pdo = getDB();

// =======================================================
// ⭐ [임시 조치] 로그인 확인을 우회하고 테스트 user_id를 설정합니다. ⭐
// =======================================================
// user_id가 1번인 'kim_minjun' 사용자를 임시로 설정합니다.
$_SESSION['user_id'] = 1;

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
    $visit_time = $_POST['visit_time'];

    // =======================================================
    // 3. 트랜잭션 시작
    // =======================================================
    // 라인 38 이전에 추가
    if ($pdo === null) {
        die("디버그 체크: \$pdo 변수가 null입니다. db.php 파일을 확인하세요.");
    }
    if (!($pdo instanceof PDO)) {
        die("디버그 체크: \$pdo는 PDO 객체가 아닙니다. db.php 파일 확인 필수.");
    }
    // 이 코드를 통과하면 $pdo->beginTransaction()이 실행됨
    
    $pdo->beginTransaction();

    try {
        // A. reviews 테이블에 새 리뷰 INSERT
        $sql_insert_review = "INSERT INTO reviews (user_id, restaurant_id, occasion_id, time_slot_id, rating_score, spend_amount, comment, visit_time) 
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt_insert = $pdo->prepare($sql_insert_review);
        $stmt_insert->execute([
            $user_id, 
            $restaurant_id, 
            $occasion_id, 
            $time_slot_id, 
            $rating_score, 
            $spend_amount, 
            $comment, 
            $visit_time
        ]);

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

<!DOCTYPE html>
<html>
<head>
    <title>리뷰 작성</title>
</head>
<body>
    <h1>새 리뷰 작성</h1>
    <?php if (isset($message)): ?>
        <p style="color: <?php echo (strpos($message, '성공') !== false) ? 'green' : 'red'; ?>"><?php echo $message; ?></p>
    <?php endif; ?>

    <form method="POST" action="review_create.php">
        <label>레스토랑 ID:</label>
        <input type="number" name="restaurant_id" required value="1"><br>

        <label>방문 목적 ID (occasion_id):</label>
        <input type="number" name="occasion_id" required value="1"><br>

        <label>시간대 ID (time_slot_id):</label>
        <input type="number" name="time_slot_id" required value="1"><br>

        <label>평점 (1-5):</label>
        <input type="number" name="rating_score" min="1" max="5" required><br>

        <label>지출 금액:</label>
        <input type="number" name="spend_amount" required><br>

        <label>방문 시각 (visit_time):</label>
        <input type="datetime-local" name="visit_time" required><br>

        <label>리뷰 코멘트:</label>
        <textarea name="comment"></textarea><br>

        <button type="submit">리뷰 등록</button>
    </form>
</body>
</html>