<?php
// ... DB 연결 및 HTML 상단 코드 ...
require_once '../config/db.php';

// header.php가 없으므로 세션을 직접 시작합니다.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// $pdo 객체 할당
$pdo = getDB();

// 1. 사용자 입력 받기 (수정)
$district_id = $_GET['district'] ?? null;
$occasion_id = $_GET['occasion'] ?? null;
$time_slot_id = $_GET['time_slot'] ?? null;
$limit = 10;

$results = [];

// 조건 검사: T, P, O 세 가지가 모두 있을 때 쿼리 실행
if ($district_id && $occasion_id && $time_slot_id) { 
    $sql = "
        WITH RankedRestaurants AS (
            SELECT
                r.restaurant_id,
                res.name,
                d.district_name,
                o.occasion_name,
                ts.time_of_day, -- Time Slot 이름 추가
                AVG(r.rating_score) AS avg_rating,
                -- PARTITION BY 없이 전체 순위를 매김
                RANK() OVER (ORDER BY AVG(r.rating_score) DESC) AS ranking
            FROM reviews r
            JOIN restaurants res ON r.restaurant_id = res.restaurant_id
            JOIN districts d ON res.district_id = d.district_id
            JOIN occasions o ON r.occasion_id = o.occasion_id
            JOIN time_slots ts ON r.time_slot_id = ts.time_slot_id -- Time Slots 조인 추가!
            
            WHERE 
                res.district_id = ? 
                AND r.occasion_id = ?
                AND r.time_slot_id = ?
            
            GROUP BY 
                r.restaurant_id, res.name, d.district_name, o.occasion_name, ts.time_of_day
        )
        SELECT *
        FROM RankedRestaurants
        WHERE ranking <= ?
        ORDER BY ranking ASC;
    ";
    
    // 3. Prepared Statement 실행 (바인딩 변수 순서 유의)
    $stmt = $pdo->prepare($sql);
    
    // 바인딩할 변수 배열 (순서: district, occasion, time_slot, limit)
    $stmt->execute([$district_id, $occasion_id, $time_slot_id, $limit]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

} 
// ... HTML 폼 및 결과 테이블 출력 부분 ...
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>TPO 랭킹 분석</title>
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

<h1>🏆 TPO 조건별 레스토랑 랭킹 분석</h1>
<p>원하는 지역, 목적, 시간대를 선택하여 해당 TPO 조건에서 평점이 가장 높은 TOP 10 레스토랑 순위를 확인합니다.</p>

<form method="GET" action="analysis_rank.php">
    
    <div class="form-group">
        <label for="district">지역 ID (Place):</label>
        <input type="number" id="district" name="district" required placeholder="지역 ID (예: 1)" 
               value="<?php echo htmlspecialchars($district_id ?? ''); ?>">
    </div>

    <div class="form-group">
        <label for="occasion">방문 목적 ID (Occasion):</label>
        <input type="number" id="occasion" name="occasion" required placeholder="목적 ID (예: 2)" 
               value="<?php echo htmlspecialchars($occasion_id ?? ''); ?>">
    </div>

    <div class="form-group">
        <label for="time_slot">시간대 ID (Time Slot):</label>
        <input type="number" id="time_slot" name="time_slot" required placeholder="시간대 ID (예: 3)" 
               value="<?php echo htmlspecialchars($time_slot_id ?? ''); ?>">
    </div>

    <button type="submit">랭킹 분석 실행</button>
</form>

<?php if (isset($results) && !empty($results)): ?>
    <hr>
    <h2>[<?php echo htmlspecialchars($results[0]['district_name'] ?? '선택 지역'); ?>]
    <?php echo htmlspecialchars($results[0]['occasion_name'] ?? '선택 목적'); ?> 
    <?php echo htmlspecialchars($results[0]['time_of_day'] ?? '선택 시간대'); ?> 
    TOP <?php echo count($results); ?> 랭킹</h2>

    <table>
        <thead>
            <tr>
                <th>순위</th>
                <th>레스토랑 이름</th>
                <th>평균 평점</th>
                <th>지역</th>
                <th>분석 목적</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($results as $row): ?>
            <tr>
                <td><?php echo $row['ranking']; ?></td>
                <td><?php echo htmlspecialchars($row['name']); ?></td>
                <td><?php echo number_format($row['avg_rating'], 2); ?></td>
                <td><?php echo htmlspecialchars($row['district_name']); ?></td>
                <td><?php echo htmlspecialchars($row['occasion_name']); ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

<?php elseif (isset($_GET['district'])): ?>
    <hr>
    <p>요청하신 TPO 조건에 해당하는 리뷰 데이터가 없습니다.</p>
<?php endif; ?>

</body>
</html>