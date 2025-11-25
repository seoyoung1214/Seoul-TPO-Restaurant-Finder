<?php
require_once "../config/db.php";
session_start();
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>리뷰 목록</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container py-4">

    <h2 class="mb-4">📄 전체 리뷰 목록</h2>

    <?php
    //   리뷰 JOIN 조회

    $sql = "
        SELECT 
            r.review_id,
            r.rating_score,
            r.spend_amount,
            r.comment,
            r.created_at,
            r.visit_time,

            u.username,
            rs.restaurant_id,
            rs.name AS restaurant_name,
            d.district_name,

            oc.occasion_name,
            ts.time_of_day

        FROM reviews r
        JOIN users u             ON r.user_id = u.user_id
        JOIN restaurants rs      ON r.restaurant_id = rs.restaurant_id
        JOIN districts d         ON rs.district_id = d.district_id
        JOIN occasions oc        ON r.occasion_id = oc.occasion_id
        JOIN time_slots ts       ON r.time_slot_id = ts.time_slot_id
        ORDER BY r.visit_time DESC
    ";

    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($rows) == 0) {
        echo "<p class='alert alert-warning'>아직 등록된 리뷰가 없습니다.</p>";
    } else {
        echo "<div class='list-group'>";
        
        foreach ($rows as $row) {

            // 본인 리뷰일 경우 수정/삭제 표시
            $editDelete = "";
            if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $row['user_id']) {
                $editDelete = "
                    <div class='mt-2'>
                        <a href='review_edit.php?review_id={$row['review_id']}' class='btn btn-sm btn-outline-primary'>수정</a>
                        <a href='review_delete.php?review_id={$row['review_id']}' class='btn btn-sm btn-outline-danger'
                           onclick='return confirm(\"리뷰를 삭제할까요?\")'>삭제</a>
                    </div>
                ";
            }

            echo "
            <div class='list-group-item mb-3'>
                <h5 class='mb-1'>🍽 {$row['restaurant_name']} <small class='text-muted'>({$row['district_name']})</small></h5>

                <p class='mb-1'>
                    ⭐ 평점: <strong>{$row['rating_score']}</strong><br>
                    💸 사용 금액: " . ($row['spend_amount'] ? number_format($row['spend_amount'])." 원" : "정보 없음") . "<br>
                    🎯 목적: {$row['occasion_name']}<br>
                    🕒 시간대: {$row['time_of_day']}<br>
                </p>

                <p class='mb-1'>💬 {$row['comment']}</p>

                <small class='text-muted'>
                    작성자: {$row['username']} |
                    방문일: " . date("Y-m-d H:i", strtotime($row['visit_time'])) . "
                </small>

                $editDelete
            </div>
            ";
        }

        echo "</div>";
    }
    ?>

</div>

</body>
</html>
