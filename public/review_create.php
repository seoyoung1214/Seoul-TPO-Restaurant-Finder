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
// [임시 조치] 로그인 확인을 우회하고 테스트 user_id를 설정합니다.
// =======================================================
// user_id가 1번인 'kim_minjun' 사용자를 임시로 설정합니다.
$_SESSION['user_id'] = 1;


// =======================================================
// 0. 드롭다운 메뉴에 사용할 데이터 조회 (추가된 로직)
// =======================================================

// 0.1. 레스토랑 목록 조회
$restaurants = $pdo->query("SELECT restaurant_id, name FROM restaurants ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
// 0.2. 목적 목록 조회
$occasions = $pdo->query("SELECT occasion_id, occasion_name FROM occasions ORDER BY occasion_name")->fetchAll(PDO::FETCH_ASSOC);
// 0.3. 시간대 목록 조회
$time_slots = $pdo->query("SELECT time_slot_id, time_of_day FROM time_slots ORDER BY time_slot_id")->fetchAll(PDO::FETCH_ASSOC);


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
    if ($pdo === null) {
        die("디버그 체크: \$pdo 변수가 null입니다. db.php 파일을 확인하세요.");
    }
    if (!($pdo instanceof PDO)) {
        die("디버그 체크: \$pdo는 PDO 객체가 아닙니다. db.php 파일 확인 필수.");
    }
    
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
    }
}
// HTML 폼 및 결과 메시지 출력 부분...
?>

<!DOCTYPE html>
<html>
<head>
    <title>리뷰 작성</title>
    <style>
        label { display: block; margin-top: 10px; font-weight: bold; }
        input, select, textarea { margin-bottom: 10px; padding: 8px; width: 300px; border: 1px solid #ccc; border-radius: 4px; }
        button { padding: 10px 15px; background-color: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; }
    </style>
</head>
<body>
    <h1>새 리뷰 작성</h1>
    <?php if (isset($message)): ?>
        <p style="color: <?php echo (strpos($message, '성공') !== false) ? 'green' : 'red'; ?>"><?php echo $message; ?></p>
    <?php endif; ?>

    <form method="POST" action="review_create.php">
        
        <label for="restaurant_id">레스토랑:</label>
        <select id="restaurant_id" name="restaurant_id" required>
            <option value="">-- 레스토랑 선택 --</option>
            <?php foreach ($restaurants as $res): ?>
                <option value="<?php echo htmlspecialchars($res['restaurant_id']); ?>">
                    <?php echo htmlspecialchars($res['name']); ?>
                </option>
            <?php endforeach; ?>
        </select><br>

        <label for="occasion_id">방문 목적 (Occasion):</label>
        <select id="occasion_id" name="occasion_id" required>
            <option value="">-- 목적 선택 --</option>
            <?php foreach ($occasions as $occasion): ?>
                <option value="<?php echo htmlspecialchars($occasion['occasion_id']); ?>">
                    <?php echo htmlspecialchars($occasion['occasion_name']); ?>
                </option>
            <?php endforeach; ?>
        </select><br>

        <label for="time_slot_id">시간대 (Time Slot):</label>
        <select id="time_slot_id" name="time_slot_id" required>
            <option value="">-- 시간대 선택 --</option>
            <?php foreach ($time_slots as $slot): ?>
                <option value="<?php echo htmlspecialchars($slot['time_slot_id']); ?>">
                    <?php echo htmlspecialchars($slot['time_of_day']); ?>
                </option>
            <?php endforeach; ?>
        </select><br>

        <label for="rating_score">평점 (1-5):</label>
        <input type="number" id="rating_score" name="rating_score" min="1" max="5" required><br>

        <label for="spend_amount">지출 금액:</label>
        <input type="number" id="spend_amount" name="spend_amount" required><br>

        <label for="visit_time">방문 시각 (visit_time):</label>
        <input type="datetime-local" id="visit_time" name="visit_time" required><br>

        <label for="comment">리뷰 코멘트:</label>
        <textarea id="comment" name="comment"></textarea><br>

        <button type="submit">리뷰 등록</button>
    </form>
</body>
</html>