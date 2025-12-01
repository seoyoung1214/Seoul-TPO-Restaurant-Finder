<?php
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . "/header.php";

$pdo = getDB();

$districts = $pdo->query("
    SELECT district_id, district_name
    FROM districts
    ORDER BY district_name
")->fetchAll();

$occasions = $pdo->query("
    SELECT occasion_id, occasion_name
    FROM occasions
    ORDER BY occasion_name
")->fetchAll();

$timeSlots = $pdo->query("
    SELECT time_slot_id, time_of_day
    FROM time_slots
    ORDER BY time_slot_id
")->fetchAll();

/*
 * GET 파라미터 수집
 */
$startDate   = $_GET['start_date']   ?? '';
$endDate     = $_GET['end_date']     ?? '';
$districtId  = $_GET['district_id']  ?? '';
$timeSlotId  = $_GET['time_slot_id'] ?? '';
$occasionId  = $_GET['occasion_id']  ?? '';
$minRating   = $_GET['min_rating']   ?? '';   // 예: 평점 4.0 이상 필터
$weekendOnly = isset($_GET['weekend_only']); // 체크박스 예시


/*
 * 동적 WHERE 절 구성 (PreparedStatement)
 */
$where  = [];
$params = [];

// 기간 필터 (방문일 기준, 컬럼명은 실제 스키마에 맞게 조정)
if ($startDate !== '') {
    $where[] = 'DATE(r.visit_time) >= :start_date';
    $params[':start_date'] = $startDate;
}
if ($endDate !== '') {
    $where[] = 'DATE(r.visit_time) <= :end_date';
    $params[':end_date'] = $endDate;
}

// 구 필터
if ($districtId !== '') {
    $where[] = 'd.district_id = :district_id';
    $params[':district_id'] = (int)$districtId;
}

// 시간대 필터
if ($timeSlotId !== '') {
    $where[] = 'ts.time_slot_id = :time_slot_id';
    $params[':time_slot_id'] = (int)$timeSlotId;
}

// Occasion 필터
if ($occasionId !== '') {
    $where[] = 'o.occasion_id = :occasion_id';
    $params[':occasion_id'] = (int)$occasionId;
}

// 최소 평점 필터 (예: 4.0 이상)
if ($minRating !== '') {
    $where[] = 'r.rating_score >= :min_rating';
    $params[':min_rating'] = (float)$minRating;
}

// 주말만 보기 체크박스 예시 (visit_datetime 기준)
if ($weekendOnly) {
    // DAYOFWEEK: 1=일요일 ~ 7=토요일 (MySQL 기준)
    $where[] = 'DAYOFWEEK(r.visit_time) IN (1,7)';
}

/*
 * 메인 집계 쿼리
 * - GROUP BY: 구(district) + 시간대(time_of_day) + Occasion
 * - 집계: COUNT, AVG, MIN, MAX
 * - 레스토랑 단위가 아니라 “해당 TPO 조건의 리뷰 집합”을 기준으로 집계
 */

$sql = "
    SELECT
        d.district_name,
        ts.time_of_day,
        o.occasion_name,
        COUNT(*)                 AS review_count,
        AVG(r.rating_score)      AS avg_rating,
        AVG(r.spend_amount)      AS avg_spend,
        MIN(r.spend_amount)      AS min_spend,
        MAX(r.spend_amount)      AS max_spend
    FROM reviews r
    JOIN restaurants res ON r.restaurant_id = res.restaurant_id
    JOIN districts  d    ON res.district_id = d.district_id
    LEFT JOIN time_slots ts    ON r.time_slot_id  = ts.time_slot_id
    LEFT JOIN occasions  o     ON r.occasion_id   = o.occasion_id
";


if ($where) {
    $sql .= ' WHERE ' . implode(' AND ', $where);
}

$sql .= "
    GROUP BY d.district_name, ts.time_of_day, o.occasion_name
    ORDER BY d.district_name, ts.time_of_day, o.occasion_name
";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>TPO 복합 그룹 분석</title>
    <!-- 공통 style.css 링크 추가 -->
    <link rel="stylesheet" href="../css/style.css">
    <style>
        /* analysis_group.php 전용 스타일 */
        .page-wrapper {
            padding: 40px 20px;
            max-width: 1200px;
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

        /* 필터 폼 스타일 */
        .filter-form-container {
            padding: 20px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            background-color: #f9f9f9;
            margin-bottom: 30px;
        }
        
        legend {
            font-size: 1.2em;
            font-weight: bold;
            color: #007bff;
            padding: 0 10px;
            border-bottom: 1px solid #007bff;
            margin-bottom: 15px;
            width: auto;
        }

        .input-group {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 15px;
        }
        .input-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
            width: auto; /* 라벨 너비 자동 */
        }
        .input-group input[type="date"],
        .input-group input[type="number"],
        .input-group select {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            width: 180px; /* 고정 너비 지정 */
            box-sizing: border-box;
        }
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* 버튼 및 링크 스타일 */
        .button-group button {
            padding: 10px 15px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1em;
            transition: background-color 0.2s;
        }
        .button-group a {
            padding: 10px 15px;
            background-color: #6c757d;
            color: white;
            border-radius: 4px;
            text-decoration: none;
            font-size: 1em;
            margin-left: 10px;
            transition: background-color 0.2s;
        }
        .button-group button:hover {
            background-color: #0056b3;
        }
        .button-group a:hover {
            background-color: #5a6268;
        }


        /* 결과 테이블 스타일 */
        .results-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden; /* 그림자 잘림 방지 */
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
        .results-table td:nth-child(4),
        .results-table td:nth-child(5),
        .results-table td:nth-child(6),
        .results-table td:nth-child(7),
        .results-table td:nth-child(8),
        .results-table td:nth-child(9) {
            text-align: right;
            font-family: monospace, sans-serif; /* 숫자 가독성 향상 */
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

<h2>📊 TPO 기반 복합 그룹핑 분석</h2>
<p>
    선택한 기간, 구, 시간대, Occasion 조건에 따라
    <strong>평균 평점 / 평균 소비 금액 / 최소·최대 소비 금액</strong>을 집계합니다.
</p>

<div class="filter-form-container">
    <form method="get">
        <fieldset>
            <legend>필터 조건</legend>

            <div class="input-group">
                <label>시작일:
                    <input type="date" name="start_date" value="<?= htmlspecialchars($startDate) ?>">
                </label>

                <label>종료일:
                    <input type="date" name="end_date" value="<?= htmlspecialchars($endDate) ?>">
                </label>
            </div>

            <div class="input-group">
                <label>구 선택:
                    <select name="district_id">
                        <option value="">전체</option>
                        <?php foreach ($districts as $d): ?>
                            <option value="<?= $d['district_id'] ?>"
                                <?= $districtId == $d['district_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($d['district_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>

                <label>시간대:
                    <select name="time_slot_id">
                        <option value="">전체</option>
                        <?php foreach ($timeSlots as $t): ?>
                            <option value="<?= $t['time_slot_id'] ?>"
                                <?= $timeSlotId == $t['time_slot_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($t['time_of_day']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>

                <label>자리/목적(Occasion):
                    <select name="occasion_id">
                        <option value="">전체</option>
                        <?php foreach ($occasions as $o): ?>
                            <option value="<?= $o['occasion_id'] ?>"
                                <?= $occasionId == $o['occasion_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($o['occasion_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>
            </div>

            <div class="input-group">
                <label>최소 평점:
                    <input type="number" name="min_rating" step="0.1" min="1" max="5" placeholder="예: 4.0"
                                value="<?= htmlspecialchars($minRating) ?>" style="width: 120px;">
                </label>

                <div class="checkbox-group">
                    <label style="font-weight: normal;">
                        <input type="checkbox" name="weekend_only" value="1"
                            <?= $weekendOnly ? 'checked' : '' ?>>
                        주말 방문만 보기
                    </label>
                </div>
            </div>

            <div class="button-group" style="margin-top: 20px;">
                <button type="submit">분석 실행</button>
                <a href="analysis_group.php">필터 초기화</a>
            </div>
        </fieldset>
    </form>
</div> <!-- filter-form-container 끝 -->

<?php if (!$rows): ?>
    <p class="no-data-message">조건에 해당하는 데이터가 없습니다.</p>
<?php else: ?>
    <h3>✅ 분석 결과 (총 <?= count($rows) ?>건)</h3>
    <table class="results-table">
        <thead>
        <tr>
            <th>구 (Place)</th>
            <th>시간대 (Time)</th>
            <th>자리/목적 (Occasion)</th>
            <th>리뷰 수</th>
            <th>평균 평점</th>
            <th>평균 소비 금액</th>
            <th>최소 소비 금액</th>
            <th>최대 소비 금액</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($rows as $row): ?>
            <tr>
                <td><?= htmlspecialchars($row['district_name']) ?></td>
                <td><?= htmlspecialchars($row['time_of_day']) ?></td>
                <td><?= htmlspecialchars($row['occasion_name']) ?></td>
                <td><?= (int)$row['review_count'] ?></td>
                <td style="font-weight: bold; color: #007bff;"><?= number_format($row['avg_rating'], 2) ?></td>
                <td><?= number_format($row['avg_spend']) ?>원</td>
                <td><?= number_format($row['min_spend']) ?>원</td>
                <td><?= number_format($row['max_spend']) ?>원</td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php include 'footer.php'; ?>

</div> <!-- page-wrapper 끝 -->

</body>
</html>