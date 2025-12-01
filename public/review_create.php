<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once '../config/db.php';
require_once __DIR__ . "/header.php";

$pdo = getDB();

// 0.1. 레스토랑 목록 조회
$restaurants = $pdo->query("SELECT restaurant_id, name FROM restaurants ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
// 0.2. 목적 목록 조회
$occasions = $pdo->query("SELECT occasion_id, occasion_name FROM occasions ORDER BY occasion_name")->fetchAll(PDO::FETCH_ASSOC);
// 0.3. 시간대 목록 조회
$time_slots = $pdo->query("SELECT time_slot_id, time_of_day FROM time_slots ORDER BY time_slot_id")->fetchAll(PDO::FETCH_ASSOC);


// 1. 로그인 상태 확인
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
    <!-- 공통 style.css 링크 추가 -->
    <link rel="stylesheet" href="../css/style.css">
    <style>
        /* 폼 전체를 중앙에 배치하고 컨테이너를 정의합니다. */
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
        
        h1 {
            color: #333;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
            margin-bottom: 25px;
            font-size: 1.8em;
        }

        .form-group { margin-bottom: 15px; }
        label { 
            display: block; 
            margin-top: 10px; 
            font-weight: bold; 
            margin-bottom: 5px;
            color: #555;
        }
        input:not([type="submit"]), select, textarea { 
            width: 100%; 
            max-width: 300px;
            padding: 10px; 
            border: 1px solid #ccc; 
            border-radius: 6px; 
            box-sizing: border-box;
            transition: border-color 0.3s;
        }
        input:focus, select:focus, textarea:focus {
            border-color: #007bff;
            outline: none;
        }
        textarea { 
            height: 100px; 
            resize: vertical; 
            max-width: 300px;
        }
        /* 버튼 스타일을 analysis_rank.php의 버튼과 유사하게 통일 */
        button[type="submit"] { 
            padding: 10px 15px; 
            background-color: #007bff; /* 주 색상으로 통일 */
            color: white; 
            border: none; 
            border-radius: 4px; 
            cursor: pointer; 
            font-size: 1em;
            transition: background-color 0.3s;
            margin-top: 10px;
        }
        button[type="submit"]:hover {
            background-color: #0056b3;
        }

        /* 메시지 스타일 */
        .message-success {
            padding: 10px;
            border-radius: 4px;
            background-color: #d4edda; /* 연한 녹색 배경 */
            color: #155724; /* 진한 녹색 텍스트 */
            border: 1px solid #c3e6cb;
            margin-bottom: 15px;
        }
        .message-error {
            padding: 10px;
            border-radius: 4px;
            background-color: #f8d7da; /* 연한 빨강 배경 */
            color: #721c24; /* 진한 빨강 텍스트 */
            border: 1px solid #f5c6cb;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="page-wrapper">
        <div class="form-container">
            <h1 style="margin-bottom: 20px;">✏️ 새 리뷰 작성</h1>
            <?php if (isset($message)): ?>
                <p class="<?php echo (strpos($message, '성공') !== false) ? 'message-success' : 'message-error'; ?>">
                    <?php echo $message; ?>
                </p>
            <?php endif; ?>

            <form method="POST" action="review_create.php">
                
                <div class="form-group">
                    <label for="restaurant_id">레스토랑:</label>
                    <select id="restaurant_id" name="restaurant_id" required>
                        <option value="">-- 레스토랑 선택 --</option>
                        <?php foreach ($restaurants as $res): ?>
                            <option value="<?php echo htmlspecialchars($res['restaurant_id']); ?>">
                                <?php echo htmlspecialchars($res['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="occasion_id">방문 목적 (Occasion):</label>
                    <select id="occasion_id" name="occasion_id" required>
                        <option value="">-- 목적 선택 --</option>
                        <?php foreach ($occasions as $occasion): ?>
                            <option value="<?php echo htmlspecialchars($occasion['occasion_id']); ?>">
                                <?php echo htmlspecialchars($occasion['occasion_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="time_slot_id">시간대 (Time Slot):</label>
                    <select id="time_slot_id" name="time_slot_id" required>
                        <option value="">-- 시간대 선택 --</option>
                        <?php foreach ($time_slots as $slot): ?>
                            <option value="<?php echo htmlspecialchars($slot['time_slot_id']); ?>">
                                <?php echo htmlspecialchars($slot['time_of_day']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="rating_score">평점 (1-5):</label>
                    <input type="number" id="rating_score" name="rating_score" min="1" max="5" required>
                </div>

                <div class="form-group">
                    <label for="spend_amount">지출 금액:</label>
                    <input type="number" id="spend_amount" name="spend_amount" required>
                </div>

                <div class="form-group">
                    <label for="visit_time">방문 시각 (visit_time):</label>
                    <input type="datetime-local" id="visit_time" name="visit_time" required>
                </div>

                <div class="form-group">
                    <label for="comment">리뷰 코멘트:</label>
                    <textarea id="comment" name="comment"></textarea>
                </div>

                <button type="submit">리뷰 등록</button>
            </form>
        </div>
    </div>
</body>
</html>