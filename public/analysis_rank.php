<?php
// ... DB 연결 및 HTML 상단 코드 ...
require_once '../config/db.php';
session_start();
require_once __DIR__ . "/header.php";


// $pdo 객체 할당
$pdo = getDB();

// =================================================================
// 1. 드롭다운 메뉴에 사용할 데이터 조회 (추가된 로직)
// =================================================================

// 1.1. 지역 목록 조회
$districts = $pdo->query("SELECT district_id, district_name FROM districts ORDER BY district_name")->fetchAll(PDO::FETCH_ASSOC);
// 1.2. 목적 목록 조회
$occasions = $pdo->query("SELECT occasion_id, occasion_name FROM occasions ORDER BY occasion_name")->fetchAll(PDO::FETCH_ASSOC);
// 1.3. 시간대 목록 조회
$time_slots = $pdo->query("SELECT time_slot_id, time_of_day FROM time_slots ORDER BY time_slot_id")->fetchAll(PDO::FETCH_ASSOC);


// 2. 사용자 입력 받기 (수정)
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
                ts.time_of_day,
                AVG(r.rating_score) AS avg_rating,
                RANK() OVER (ORDER BY AVG(r.rating_score) DESC) AS ranking
            FROM reviews r
            JOIN restaurants res ON r.restaurant_id = res.restaurant_id
            JOIN districts d ON res.district_id = d.district_id
            JOIN occasions o ON r.occasion_id = o.occasion_id
            JOIN time_slots ts ON r.time_slot_id = ts.time_slot_id
            
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
        /* input[type="number"], */ select { padding: 8px; width: 200px; border: 1px solid #ccc; border-radius: 4px; }
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
        <label for="district">지역 (Place):</label>
        <select id="district" name="district" required>
            <option value="">-- 지역 선택 --</option>
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
            <option value="">-- 목적 선택 --</option>
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
            <option value="">-- 시간대 선택 --</option>
            <?php foreach ($time_slots as $slot): ?>
                <option value="<?php echo htmlspecialchars($slot['time_slot_id']); ?>"
                    <?php if ($time_slot_id == $slot['time_slot_id']) echo 'selected'; ?>>
                    <?php echo htmlspecialchars($slot['time_of_day']); ?>
                </option>
            <?php endforeach; ?>
        </select>
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