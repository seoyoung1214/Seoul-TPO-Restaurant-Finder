<?php
require_once "../config/db.php";
session_start();
require_once __DIR__ . '/header.php';
$conn = getDB();

// --- 필터 값 수신 ---
$district     = $_GET['district']     ?? '';
$cuisine      = $_GET['cuisine']      ?? '';
$time_slot    = $_GET['time_slot']    ?? '';
$occasion     = $_GET['occasion']     ?? '';
$max_price    = $_GET['max_price']    ?? 400000;
$rating_check = $_GET['rating_check'] ?? '';
$weekend_only = $_GET['weekend_only'] ?? '';
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>TPO 맛집 검색</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
<div class="container py-4">

<h2 class="mb-4"> TPO 기반 맛집 검색</h2>

<!-- 검색 폼 -->
<form class="card p-4 mb-4" method="GET">
    <div class="row mb-3">

        <!-- 시간대 -->
        <div class="col-md-3">
            <label class="form-label">Time: 시간대</label>
            <select name="time_slot" class="form-select">
                <option value="">전체</option>
                <?php
                $sql = "SELECT time_slot_id, time_of_day FROM time_slots ORDER BY time_slot_id";
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $selected = ($time_slot == $row['time_slot_id']) ? "selected" : "";
                    echo "<option value='{$row['time_slot_id']}' $selected>{$row['time_of_day']}</option>";
                }
                ?>
            </select>
        </div>


        <!-- 구 선택 -->
        <div class="col-md-3">
            <label class="form-label">Place: 지역(구)</label>
            <select name="district" class="form-select">
                <option value="">전체</option>
                <?php
                $sql = "SELECT district_id, district_name FROM districts ORDER BY district_name";
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $selected = ($district == $row['district_id']) ? "selected" : "";
                    echo "<option value='{$row['district_id']}' $selected>{$row['district_name']}</option>";
                }
                ?>
            </select>
        </div>
        

        <!-- 자리/목적 -->
        <div class="col-md-3">
            <label class="form-label">Occasion: 자리/목적</label><br>
            <?php
            $sql = "SELECT occasion_id, occasion_name FROM occasions ORDER BY occasion_name";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $checked = ($occasion == $row['occasion_id']) ? "checked" : "";
                echo "
                    <label class='me-2'>
                        <input type='radio' name='occasion' value='{$row['occasion_id']}' $checked>
                        {$row['occasion_name']}
                    </label>
                ";
            }
            ?>
            <label class="ms-2"><input type="radio" name="occasion" value="" <?= ($occasion==''?'checked':'') ?>> 전체</label>
        </div>

        <!-- 음식 종류 -->
        <div class="col-md-3">
            <label class="form-label">음식 종류</label><br>
            <?php
            $sql = "SELECT cuisine_id, cuisine_name FROM cuisines ORDER BY cuisine_name";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $checked = ($cuisine == $row['cuisine_id']) ? "checked" : "";
                echo "
                    <label class='me-2'>
                        <input type='radio' name='cuisine' value='{$row['cuisine_id']}' $checked>
                        {$row['cuisine_name']}
                    </label>
                ";
            }
            ?>
            <label class="ms-2"><input type="radio" name="cuisine" value="" <?= ($cuisine==''?'checked':'') ?>> 전체</label>
        </div>

    </div>

   <div class="mb-3">
    <label class="form-label">예산 상한</label>

    <!-- 현재 금액 표시 -->
    <div class="d-flex align-items-center mb-2">
        <span class="me-2">선택 금액:</span>
        <span id="price_value" class="fw-bold text-primary">
            <?= number_format($max_price) ?>원
        </span>
    </div>

    <!-- 슬라이더 -->
    <input type="range" 
           class="form-range" 
           name="max_price"
           id="max_price"
           min="10000" 
           max="400000" 
           step="5000"
           value="<?= $max_price ?>">
</div>

<script>
    // 실시간 가격 업데이트
    document.getElementById("max_price").addEventListener("input", function() {
        let val = Number(this.value).toLocaleString() + "원";
        document.getElementById("price_value").textContent = val;
    });
</script>


    <!-- 평점 -->
    <div class="mb-3 form-check">
        <input class="form-check-input" type="checkbox" name="rating_check" value="1"
            <?= ($rating_check == "1") ? "checked" : "" ?>>
        <label class="form-check-label">평점 4.0 이상만 보기</label>
    </div>

    <!-- 주말 -->
    <div class="mb-4 form-check">
        <input class="form-check-input" type="checkbox" name="weekend_only" value="1"
            <?= ($weekend_only == "1") ? "checked" : "" ?>>
        <label class="form-check-label">주말에 영업하는 가게만 보기</label>
    </div>

    <button type="submit" class="btn btn-primary">검색</button>
    <a href="search.php" class="btn btn-secondary">초기화</a>
</form>


<!-- 검색 결과 -->
<div class="card p-4">
<h4 class="mb-3">
    검색 결과 
    <?php if (isset($rows)) echo "<span class='text-primary'>(" . count($rows) . "개)</span>"; ?>
</h4>

<?php
$query = "
    SELECT 
        r.restaurant_id,
        r.name,
        r.price,
        r.avg_rating,
        d.district_name
    FROM restaurants r
    JOIN districts d ON r.district_id = d.district_id

    LEFT JOIN restaurant_cuisines rc ON r.restaurant_id = rc.restaurant_id
    LEFT JOIN cuisines c ON rc.cuisine_id = c.cuisine_id
    
    LEFT JOIN reviews rv ON r.restaurant_id = rv.restaurant_id
    LEFT JOIN time_slots ts ON rv.time_slot_id = ts.time_slot_id
    LEFT JOIN occasions oc ON rv.occasion_id = oc.occasion_id

    WHERE 1 = 1
";

$params = [];

// 지역
if ($district) {
    $query .= " AND r.district_id = :district ";
    $params[':district'] = $district;
}

// 음식 종류
if ($cuisine) {
    $query .= " AND c.cuisine_id = :cuisine ";
    $params[':cuisine'] = $cuisine;
}

// 시간대
if ($time_slot) {
    $query .= " AND ts.time_slot_id = :time_slot ";
    $params[':time_slot'] = $time_slot;
}

// 목적
if ($occasion) {
    $query .= " AND oc.occasion_id = :occasion ";
    $params[':occasion'] = $occasion;
}

// 평점
if ($rating_check) {
    $query .= " AND r.avg_rating >= 4.0 ";
}

// 주말 필터
if ($weekend_only) {
    $query .= " AND (rv.review_id IS NOT NULL AND DAYOFWEEK(rv.visit_time) IN (1,7)) ";
}

// 가격
$query .= " AND r.price <= :max_price ";
$params[':max_price'] = $max_price;

// 중복 방지
$query .= " GROUP BY r.restaurant_id ";

$stmt = $conn->prepare($query);
$stmt->execute($params);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (count($rows) == 0) {
    echo "<p>검색 결과 없음</p>";
} else {
    echo "<ul class='list-group'>";
    foreach ($rows as $row) {
        echo "
            <li class='list-group-item'>
                <strong>{$row['name']}</strong>
                ({$row['district_name']})<br>
                가격대: " . number_format($row['price']) . "원 /
                평점: {$row['avg_rating']}
            </li>
        ";
    }
    echo "</ul>";
}
?>
</div>
    <?php include 'footer.php'; ?>
</div>
</body>
</html>
