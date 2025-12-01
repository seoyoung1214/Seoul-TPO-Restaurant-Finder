<?php
require_once '../config/db.php';

session_start();
require_once __DIR__ . "/header.php";

$pdo = getDB();

// =================================================================
// 1. 드롭다운 메뉴에 사용할 레스토랑 목록 데이터 조회
// =================================================================
$restaurants = $pdo->query("SELECT restaurant_id, name FROM restaurants ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);


// 1. 사용자 입력 받기
$restaurant_id = $_GET['restaurant_id'] ?? null;
$window_size = $_GET['window_size'] ?? 5; // 기본값 5

$results = [];
$restaurant_name = '특정 레스토랑';

if ($restaurant_id) {
    // 윈도우 크기 계산 및 정수형 보장
    $window_size = (int)$window_size;
    $preceding_rows = $window_size - 1; 

    // 2. 동적 쿼리 (AVG() OVER 윈도우 함수 사용)
    $sql = "
        SELECT
            r.review_id,
            r.rating_score,
            r.visit_time,
            AVG(r.rating_score) OVER (
                PARTITION BY r.restaurant_id
                ORDER BY r.visit_time
                ROWS BETWEEN {$preceding_rows} PRECEDING AND CURRENT ROW
            ) AS moving_avg,
            res.name AS restaurant_name
        FROM reviews r
        JOIN restaurants res ON r.restaurant_id = res.restaurant_id
        WHERE r.restaurant_id = :restaurant_id
        ORDER BY r.visit_time ASC;
    ";
    
    // 3. Prepared Statement 실행 및 바인딩
    try {
        $stmt = $pdo->prepare($sql);
        
        $stmt->bindParam(':restaurant_id', $restaurant_id, PDO::PARAM_INT);
        
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($results)) {
            $restaurant_name = $results[0]['restaurant_name'];
        }

    } catch (PDOException $e) {
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
        input[type="number"], select { padding: 8px; width: 200px; border: 1px solid #ccc; border-radius: 4px; }
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
        <label for="restaurant_id">레스토랑 이름:</label>
        <select id="restaurant_id" name="restaurant_id" required>
            <option value="">-- 레스토랑 선택 --</option>
            <?php foreach ($restaurants as $res): ?>
                <option value="<?php echo htmlspecialchars($res['restaurant_id']); ?>"
                    <?php if ($restaurant_id == $res['restaurant_id']) echo 'selected'; ?>>
                    <?php echo htmlspecialchars($res['name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
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
                <th>**방문 시각 (visit_time)**</th> <th>개별 평점</th>
                <th>**<?php echo $window_size; ?>개 이동 평균 (Moving Avg)**</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($results as $row): ?>
            <tr>
                <td><?php echo $row['review_id']; ?></td>
                <td><?php echo htmlspecialchars($row['visit_time']); ?></td> <td><?php echo $row['rating_score']; ?></td>
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
<?php include 'footer.php'; ?>
</body>
</html>