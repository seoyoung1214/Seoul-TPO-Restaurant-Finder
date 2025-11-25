<?php
// db.php 파일 include (PDO 연결 객체 $pdo를 사용한다고 가정)
require_once '../config/db.php';

// header.php가 없으므로 세션을 직접 시작합니다.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// $pdo 객체 할당
$pdo = getDB();

// 1. 사용자 입력 받기
// ?? '' (Null coalescing operator)를 사용하여 $_GET 값이 없을 때 null 대신 빈 문자열로 초기화하여 Warning을 방지합니다.
$restaurant_id = $_GET['restaurant_id'] ?? null;
$window_size = $_GET['window_size'] ?? 5; // 기본값 5

$results = [];
$restaurant_name = '특정 레스토랑'; // 출력용 기본값 설정

if ($restaurant_id) {
    // 윈도우 크기 계산 및 정수형 보장
    // 🚨 SQL 구문 오류 해결: $window_size를 정수로 강제 변환하여 쿼리 문자열에 직접 삽입합니다.
    $window_size = (int)$window_size;
    $preceding_rows = $window_size - 1; 

    // 2. 동적 쿼리 (AVG() OVER 윈도우 함수 사용)
    $sql = "
        SELECT
            r.review_id,
            r.rating_score,
            r.created_at,
            -- N개(window_size) 리뷰에 대한 이동 평균 평점 계산
            AVG(r.rating_score) OVER (
                PARTITION BY r.restaurant_id
                ORDER BY r.created_at
                ROWS BETWEEN {$preceding_rows} PRECEDING AND CURRENT ROW
            ) AS moving_avg,
            res.name AS restaurant_name
        FROM reviews r
        JOIN restaurants res ON r.restaurant_id = res.restaurant_id
        WHERE r.restaurant_id = :restaurant_id
        ORDER BY r.created_at ASC;
    ";
    
    // 3. Prepared Statement 실행 및 바인딩
    try {
        $stmt = $pdo->prepare($sql);
        
        // :restaurant_id만 정수(PDO::PARAM_INT)로 바인딩합니다.
        $stmt->bindParam(':restaurant_id', $restaurant_id, PDO::PARAM_INT);
        
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($results)) {
            // 출력용 레스토랑 이름 가져오기
            $restaurant_name = $results[0]['restaurant_name'];
        }

    } catch (PDOException $e) {
        // SQL 쿼리 실행 실패 시 (예: DB 연결이 끊긴 경우)
        // 실제 프로젝트에서는 사용자에게 오류를 보여주지 않습니다.
        $results = [];
        $error_message = "쿼리 실행 중 오류 발생: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>윈도잉 분석: 평점 이동 평균</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; font-weight: bold; margin-bottom: 5px; }
        input[type="number"] { padding: 8px; width: 200px; border: 1px solid #ccc; border-radius: 4px; }
        button { padding: 10px 15px; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
    </style>
</head>
<body>

<h1>📈 레스토랑 평점 추이 분석 (윈도잉)</h1>

<?php if (isset($error_message)): ?>
    <p style="color: red;">🚨 <?php echo $error_message; ?></p>
<?php endif; ?>

<form method="GET" action="analysis_window.php">
    
    <div class="form-group">
        <label for="restaurant_id">레스토랑 ID:</label>
        <input type="number" id="restaurant_id" name="restaurant_id" required placeholder="분석할 레스토랑 ID (예: 1)" 
               value="<?php echo htmlspecialchars($restaurant_id ?? ''); ?>">
    </div>

    <div class="form-group">
        <label for="window_size">윈도우 크기 (N):</label>
        <input type="number" id="window_size" name="window_size" required min="2" max="10" placeholder="평균을 낼 리뷰 수 (예: 5)" 
               value="<?php echo htmlspecialchars($window_size ?? '5'); ?>">
    </div>

    <button type="submit">이동 평균 분석 실행</button>
</form>

<?php if (!empty($results)): ?>
    <hr>
    <h2>[<?php echo htmlspecialchars($restaurant_name); ?> (ID: <?php echo htmlspecialchars($restaurant_id); ?>)] <?php echo $window_size; ?>개 리뷰 이동 평균</h2>

    <table>
        <thead>
            <tr>
                <th>리뷰 ID</th>
                <th>작성 시각</th>
                <th>개별 평점</th>
                <th>**<?php echo $window_size; ?>개 이동 평균 (Moving Avg)**</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($results as $row): ?>
            <tr>
                <td><?php echo $row['review_id']; ?></td>
                <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                <td><?php echo $row['rating_score']; ?></td>
                <td>
                    <strong><?php echo number_format($row['moving_avg'], 2); ?></strong>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    
<?php elseif (isset($_GET['restaurant_id'])): ?>
    <hr>
    <p>요청하신 레스토랑 ID (<?php echo htmlspecialchars($restaurant_id); ?>) 에 대한 리뷰 데이터가 충분하지 않거나 존재하지 않습니다. 다른 ID를 시도해 보세요.</p>
<?php endif; ?>

</body>
</html>