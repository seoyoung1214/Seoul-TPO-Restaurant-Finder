<?php
// DB 연결 및 HTML 상단 코드
require_once '../config/db.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . "/header.php"; 

$pdo = getDB();

// =================================================================
// 1. 드롭다운 메뉴에 사용할 데이터 조회
// =================================================================
$districts = $pdo->query("SELECT district_id, district_name FROM districts ORDER BY district_name")->fetchAll(PDO::FETCH_ASSOC);
$occasions = $pdo->query("SELECT occasion_id, occasion_name FROM occasions ORDER BY occasion_name")->fetchAll(PDO::FETCH_ASSOC);
$time_slots = $pdo->query("SELECT time_slot_id, time_of_day FROM time_slots ORDER BY time_slot_id")->fetchAll(PDO::FETCH_ASSOC);


// 2. 사용자 입력 받기 및 동적 쿼리 조건 설정
$district_id = $_GET['district'] ?? 'all';
$occasion_id = $_GET['occasion'] ?? 'all';
$time_slot_id = $_GET['time_slot'] ?? 'all';
$limit = 10;

$results = [];
$where_clauses = [];
$bind_params = [];

// 2.1. 지역 조건
if ($district_id !== 'all') {
    $where_clauses[] = "res.district_id = ?";
    $bind_params[] = $district_id;
}

// 2.2. 방문 목적 조건
if ($occasion_id !== 'all') {
    $where_clauses[] = "r.occasion_id = ?";
    $bind_params[] = $occasion_id;
}

// 2.3. 시간대 조건
if ($time_slot_id !== 'all') {
    $where_clauses[] = "r.time_slot_id = ?";
    $bind_params[] = $time_slot_id;
}

// 2.4. 랭킹 제한
$bind_params[] = $limit;


if (isset($_GET['district'])) {
    $where_sql = count($where_clauses) > 0 ? "WHERE " . implode(' AND ', $where_clauses) : "";

    $sql = "
        WITH RankedRestaurants AS (
            SELECT
                r.restaurant_id,
                res.name,
                -- 'All' 선택 시 결과 집합에 해당 컬럼이 없으므로, 동적 출력 준비를 위해 NULL 처리
                d.district_name, 
                o.occasion_name,
                ts.time_of_day,
                AVG(r.rating_score) AS avg_rating,
                -- 모든 조건이 All일 경우 전체 레스토랑 랭킹이 됨
                RANK() OVER (ORDER BY AVG(r.rating_score) DESC) AS ranking
            FROM reviews r
            JOIN restaurants res ON r.restaurant_id = res.restaurant_id
            LEFT JOIN districts d ON res.district_id = d.district_id
            LEFT JOIN occasions o ON r.occasion_id = o.occasion_id
            LEFT JOIN time_slots ts ON r.time_slot_id = ts.time_slot_id
            
            {$where_sql}
            
            GROUP BY 
                r.restaurant_id, res.name, d.district_name, o.occasion_name, ts.time_of_day
        )
        SELECT *
        FROM RankedRestaurants
        WHERE ranking <= ?
        ORDER BY ranking ASC;
    ";
    
    // 3. Prepared Statement 실행
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($bind_params);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        $results = [];
    }
} 
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
        /* input[type="number"], */ select { padding: 8px; width: 200px; border: 1px solid #ccc; border-radius: 4px; }
        button { padding: 10px 15px; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
    </style>
</head>
<body>

<h1>🏆 TPO 조건별 레스토랑 랭킹 분석</h1>
<p>원하는 지역, 목적, 시간대를 선택하여 해당 TPO 조건에서 평점이 가장 높은 TOP 10 레스토랑 순위를 확인합니다. 'All'을 선택하면 해당 조건은 필터링에서 제외됩니다.</p>

<form method="GET" action="analysis_rank.php">
    
    <div class="form-group">
        <label for="district">지역 (Place):</label>
        <select id="district" name="district" required>
            <option value="all" <?php if ($district_id === 'all') echo 'selected'; ?>>[All] 전체 지역</option>
            <?php foreach ($districts as $district): ?>
                <option value="<?php echo htmlspecialchars($district['district_id']); ?>"
                    <?php if ($district_id == $district['district_id']) echo 'selected'; ?>>
                    <?php echo htmlspecialchars($district['district_name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-group">
        <label for="occasion">방문 목적 (Occasion):</label>
        <select id="occasion" name="occasion" required>
            <option value="all" <?php if ($occasion_id === 'all') echo 'selected'; ?>>[All] 전체 목적</option>
            <?php foreach ($occasions as $occasion): ?>
                <option value="<?php echo htmlspecialchars($occasion['occasion_id']); ?>"
                    <?php if ($occasion_id == $occasion['occasion_id']) echo 'selected'; ?>>
                    <?php echo htmlspecialchars($occasion['occasion_name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-group">
        <label for="time_slot">시간대 (Time Slot):</label>
        <select id="time_slot" name="time_slot" required>
            <option value="all" <?php if ($time_slot_id === 'all') echo 'selected'; ?>>[All] 전체 시간대</option>
            <?php foreach ($time_slots as $slot): ?>
                <option value="<?php echo htmlspecialchars($slot['time_slot_id']); ?>"
                    <?php if ($time_slot_id == $slot['time_slot_id']) echo 'selected'; ?>>
                    <?php echo htmlspecialchars($slot['time_of_day']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <button type="submit">랭킹 분석 실행</button>
    <?php include 'footer.php'; ?>
</form>

<?php if (isset($results) && !empty($results)): ?>
    <hr>
    <?php
        $header_district = ($district_id === 'all') ? '전체 지역' : htmlspecialchars($results[0]['district_name'] ?? '지역 선택 안됨');
        $header_occasion = ($occasion_id === 'all') ? '전체 목적' : htmlspecialchars($results[0]['occasion_name'] ?? '목적 선택 안됨');
        $header_time_slot = ($time_slot_id === 'all') ? '전체 시간대' : htmlspecialchars($results[0]['time_of_day'] ?? '시간대 선택 안됨');
    ?>
    <h2>[<?php echo $header_district; ?>]
    <?php echo $header_occasion; ?> 
    <?php echo $header_time_slot; ?> 
    TOP <?php echo count($results); ?> 랭킹</h2>

    <table>
        <thead>
            <tr>
                <th>순위</th>
                <th>레스토랑 이름</th>
                <th>평균 평점</th>
                <th>지역</th>
                <th>방문 목적</th>
                <th>시간대</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($results as $row): ?>
            <tr>
                <td><?php echo $row['ranking']; ?></td>
                <td><?php echo htmlspecialchars($row['name']); ?></td>
                <td><?php echo number_format($row['avg_rating'], 2); ?></td>
                
                <td><?php echo ($district_id === 'all') ? 'ALL' : htmlspecialchars($row['district_name']); ?></td>
                <td><?php echo ($occasion_id === 'all') ? 'ALL' : htmlspecialchars($row['occasion_name']); ?></td>
                <td><?php echo ($time_slot_id === 'all') ? 'ALL' : htmlspecialchars($row['time_of_day']); ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

<?php elseif (isset($_GET['district'])): ?>
    <hr>
    <p>요청하신 조건에 해당하는 리뷰 데이터가 없습니다.</p>
<?php endif; ?>

</body>
</html>