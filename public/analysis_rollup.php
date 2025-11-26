<?php
// public/analysis_rollup.php
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . "/header.php";

// PDO 핸들 가져오기 (db.php의 getDB() 사용)
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
        COUNT(*)           AS review_count
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
        // 전체 합계는 이번 분석에서는 사용하지 않음
        continue;
    }

    if ($level === '1' && $isDistrictOnly) {
        // 레벨 1: 구별 평균 평점
        $rows[] = $row;
    } elseif ($level === '2' && $isDistrictTime) {
        // 레벨 2: 구 + 시간대별 평균 평점
        $rows[] = $row;
    } elseif ($level === '3' && $isFullDetail) {
        // 레벨 3: 구 + 시간대 + Occasion별 평균 평점
        $rows[] = $row;
    }
}

// 테이블 colspan 계산 (레벨에 따라 컬럼 개수 달라짐)
$colspan = 3; // District + 리뷰 수 + 평균 평점
if ($level !== '1') {
    $colspan++; // Time 컬럼 추가
}
if ($level === '3') {
    $colspan++; // Occasion 컬럼 추가
}

include 'header.php';
?>

<h2>ROLLUP / Drill-down 분석</h2>

<p>
    ROLLUP을 이용해
    <strong>구 단위 → 구 + 시간대 → 구 + 시간대 + 자리/목적(Occasion)</strong>으로
    점점 세분화되는 분석(Drill-down)을 제공합니다.
</p>

<form method="get">
    <label>분석 레벨 선택:
        <select name="level">
            <option value="1" <?= $level === '1' ? 'selected' : '' ?>>
                레벨 1: 구(district) 단위 평균 평점
            </option>
            <option value="2" <?= $level === '2' ? 'selected' : '' ?>>
                레벨 2: 구 + 시간대(time_of_day) 평균 평점
            </option>
            <option value="3" <?= $level === '3' ? 'selected' : '' ?>>
                레벨 3: 구 + 시간대 + Occasion 평균 평점
            </option>
        </select>
    </label>
    <button type="submit">보기</button>
</form>

<table border="1" cellpadding="4" cellspacing="0">
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
    <?php if (!$rows): ?>
        <tr>
            <td colspan="<?= $colspan ?>">데이터가 없습니다.</td>
        </tr>
    <?php else: ?>
        <?php foreach ($rows as $row): ?>
            <tr>
                <td><?= htmlspecialchars($row['district_name'] ?? '합계', ENT_QUOTES, 'UTF-8') ?></td>
                <?php if ($level !== '1'): ?>
                    <td><?= htmlspecialchars($row['time_of_day'] ?? '소계', ENT_QUOTES, 'UTF-8') ?></td>
                <?php endif; ?>
                <?php if ($level === '3'): ?>
                    <td><?= htmlspecialchars($row['occasion_name'] ?? '소계', ENT_QUOTES, 'UTF-8') ?></td>
                <?php endif; ?>
                <td><?= (int)$row['review_count'] ?></td>
                <td><?= number_format($row['avg_rating'], 2) ?></td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
</table>

<?php include 'footer.php'; ?>
