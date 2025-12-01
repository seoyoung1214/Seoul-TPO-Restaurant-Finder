<?php
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . "/header.php";

$pdo = getDB();

// 분석 레벨 (1, 2, 3)
$level = $_GET['level'] ?? '1';
if (!in_array($level, ['1', '2', '3'], true)) {
    $level = '1';
}

// 하나의 ROLLUP 쿼리로 구(district) / 시간대(time_of_day) / Occasion 별 집계
$sql = "
    SELECT
        d.district_name,
        ts.time_of_day,
        o.occasion_name,
        AVG(r.rating_score) AS avg_rating,
        COUNT(*)            AS review_count
    FROM reviews r
    JOIN restaurants res ON r.restaurant_id = res.restaurant_id
    JOIN districts  d    ON res.district_id = d.district_id
    JOIN time_slots ts   ON r.time_slot_id  = ts.time_slot_id
    JOIN occasions  o    ON r.occasion_id   = o.occasion_id
    GROUP BY d.district_name, ts.time_of_day, o.occasion_name WITH ROLLUP
    HAVING COUNT(*) > 0
";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$allRows = $stmt->fetchAll();

/**
 * ROLLUP 결과 정렬
 * - district_name → time_of_day → occasion_name 순으로 정렬
 * - NULL(소계/합계)은 빈 문자열로 치환해서 맨 위로 오지 않도록 정렬 기준 통일
 */
usort($allRows, function ($a, $b) {
    $dA = $a['district_name'] ?? '';
    $tA = $a['time_of_day'] ?? '';
    $oA = $a['occasion_name'] ?? '';

    $dB = $b['district_name'] ?? '';
    $tB = $b['time_of_day'] ?? '';
    $oB = $b['occasion_name'] ?? '';

    return [$dA, $tA, $oA] <=> [$dB, $tB, $oB];
});

// ROLLUP 결과에서 레벨별로 행 필터링
$rows = [];

foreach ($allRows as $row) {
    $d = $row['district_name'];
    $t = $row['time_of_day'];
    $o = $row['occasion_name'];

    $isGrandTotal   = ($d === null && $t === null && $o === null);
    $isDistrictOnly = ($d !== null && $t === null && $o === null); // 구 단위 합계
    $isDistrictTime = ($d !== null && $t !== null && $o === null); // 구 + 시간대 소계
    $isFullDetail   = ($d !== null && $t !== null && $o !== null); // 구 + 시간대 + Occasion

    if ($isGrandTotal) {
        continue;
    }

    if ($level === '1' && $isDistrictOnly) {
        // 레벨 1: 구별 평균 평점 (ROLLUP)
        $rows[] = $row;
    } elseif ($level === '2' && $isDistrictTime) {
        // 레벨 2: 구 + 시간대별 평균 평점 (SUB-ROLLUP)
        $rows[] = $row;
    } elseif ($level === '3' && $isFullDetail) {
        // 레벨 3: 구 + 시간대 + Occasion별 평균 평점 (DETAIL)
        $rows[] = $row;
    }
}

// 테이블 colspan 계산 (레벨에 따라 컬럼 개수 달라짐)
$colspan = 3; // District + 리뷰 수 + 평균 평점 (레벨 1 기준)
if ($level !== '1') {
    $colspan++; // Time 컬럼 추가
}
if ($level === '3') {
    $colspan++; // Occasion 컬럼 추가
}

?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>ROLLUP / Drill-down 분석</title>
    <!-- 공통 style.css 링크 추가 -->
    <link rel="stylesheet" href="../css/style.css">
    <style>
        /* ROLLUP 전용 스타일 */
        .page-wrapper {
            padding: 40px 20px;
            max-width: 900px; /* 분석 페이지보다 조금 작게 설정 */
            margin: 0 auto;
        }
        
        h2 {
            font-size: 1.8em;
            color: #333;
            margin-bottom: 5px;
        }
        p {
            color: #666;
            margin-bottom: 25px;
        }
        
        /* 분석 레벨 선택 폼 */
        .level-form-container {
            padding: 20px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            background-color: #f9f9f9;
            margin-bottom: 30px;
        }
        .level-form-container label {
            font-weight: bold;
            color: #555;
            margin-right: 15px;
        }
        .level-form-container select {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            width: 300px;
            box-sizing: border-box;
            margin-right: 10px;
        }
        .level-form-container button {
            padding: 8px 15px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        .level-form-container button:hover {
            background-color: #0056b3;
        }

        /* 결과 테이블 스타일 (analysis_group.php와 유사하게) */
        .results-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
        }
        .results-table thead {
            background-color: #007bff;
            color: white;
        }
        .results-table th, .results-table td {
            border: 1px solid #ddd;
            padding: 12px 15px;
            text-align: left;
        }
        .results-table tbody tr:nth-child(even) {
            background-color: #f4f4f4;
        }
        .results-table tbody tr:hover {
            background-color: #e9ecef;
        }
        /* 숫자 필드 오른쪽 정렬 */
        .results-table td:nth-child(<?= $colspan-1 ?>), /* 리뷰 수 */
        .results-table td:nth-child(<?= $colspan ?>) { /* 평균 평점 */
            text-align: right;
            font-family: monospace, sans-serif;
        }
        .sub-total-row {
            font-weight: bold;
            background-color: #e3f2fd !important; /* 라이트 블루 */
            color: #0056b3;
        }
        .no-data-message {
            padding: 15px;
            background-color: #fff3cd;
            border: 1px solid #ffeeba;
            border-radius: 4px;
            color: #856404;
            margin-top: 20px;
        }
    </style>
</head>

<body>

<div class="page-wrapper">

<h2>🔽 ROLLUP / Drill-down 분석</h2>

<p>
    ROLLUP을 이용해
    <strong>구 단위 → 구 + 시간대 → 구 + 시간대 + 자리/목적(Occasion)</strong>으로
    점점 세분화되는 분석(Drill-down)을 제공합니다. 
</p>

<div class="level-form-container">
    <form method="get">
        <label>분석 레벨 선택:
            <select name="level">
                <option value="1" <?= $level === '1' ? 'selected' : '' ?>>
                    레벨 1: 구(District) 단위 소계 (최상위 요약)
                </option>
                <option value="2" <?= $level === '2' ? 'selected' : '' ?>>
                    레벨 2: 구 + 시간대(Time of Day) 단위 소계
                </option>
                <option value="3" <?= $level === '3' ? 'selected' : '' ?>>
                    레벨 3: 구 + 시간대 + Occasion 상세 분석
                </option>
            </select>
        </label>
        <button type="submit">보기</button>
    </form>
</div>

<?php if (!$rows): ?>
    <p class="no-data-message">선택된 레벨에 해당하는 데이터가 없습니다.</p>
<?php else: ?>
    <h3>결과: 레벨 <?= $level ?> 분석 (총 <?= count($rows) ?>건)</h3>
    
    <table class="results-table">
        <thead>
        <tr>
            <th>구 (District)</th>
            <?php if ($level !== '1'): ?>
                <th>시간대 (Time of Day)</th>
            <?php endif; ?>
            <?php if ($level === '3'): ?>
                <th>자리/목적 (Occasion)</th>
            <?php endif; ?>
            <th>리뷰 수</th>
            <th>평균 평점</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($rows as $row): ?>
            <?php
                // 소계/합계 행 스타일링 (ROLLUP 쿼리에서 NULL로 반환되는 경우)
                $isSubtotal = ($row['district_name'] !== null && ($row['time_of_day'] === null || $row['occasion_name'] === null));
                $rowClass = $isSubtotal ? 'sub-total-row' : '';
            ?>
            <tr class="<?= $rowClass ?>">
                <td><?= htmlspecialchars($row['district_name'] ?? '합계', ENT_QUOTES, 'UTF-8') ?></td>
                <?php if ($level !== '1'): ?>
                    <td><?= htmlspecialchars($row['time_of_day'] ?? '소계', ENT_QUOTES, 'UTF-8') ?></td>
                <?php endif; ?>
                <?php if ($level === '3'): ?>
                    <td><?= htmlspecialchars($row['occasion_name'] ?? '소계', ENT_QUOTES, 'UTF-8') ?></td>
                <?php endif; ?>
                <td style="font-weight: bold;"><?= (int)$row['review_count'] ?></td>
                <td style="color: #007bff; font-weight: bold;"><?= number_format($row['avg_rating'], 2) ?></td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
</table>

<?php include 'footer.php'; ?>

</div> <!-- page-wrapper 끝 -->

</body>
</html>