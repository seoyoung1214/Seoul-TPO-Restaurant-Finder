<?php
// ... DB 연결 및 HTML 상단 코드 ...
require_once '../config/db.php';

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