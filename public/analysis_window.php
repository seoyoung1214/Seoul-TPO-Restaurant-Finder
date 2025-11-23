<?php
// ... DB 연결 및 HTML 상단 코드 ...
require_once '../config/db.php';

// 1. 사용자 입력 받기
$restaurant_id = $_GET['restaurant_id'] ?? null;
// 윈도우 크기 (5개 리뷰)
$window_size = $_GET['window_size'] ?? 5; 

$results = [];

if ($restaurant_id) {
    // 2. 동적 쿼리 (AVG() OVER 윈도우 함수 사용)
    $sql = "
        SELECT
            r.review_id,
            r.rating_score,
            r.created_at,
            -- N개(window_size) 리뷰에 대한 이동 평균 평점 계산
            -- 현재 행과 이전 N-1개 행을 포함하여 평균을 계산
            AVG(r.rating_score) OVER (
                PARTITION BY r.restaurant_id
                ORDER BY r.created_at
                ROWS BETWEEN :preceding_rows PRECEDING AND CURRENT ROW
            ) AS moving_avg
        FROM reviews r
        WHERE r.restaurant_id = :restaurant_id
        ORDER BY r.created_at ASC;
    ";
    
    // 3. Prepared Statement 실행 및 바인딩
    $stmt = $pdo->prepare($sql);
    
    // 윈도우 크기 계산: ROWS BETWEEN 4 PRECEDING AND CURRENT ROW = 총 5개
    $preceding_rows = $window_size - 1;
    
    // PDO::PARAM_INT를 사용하여 정수 바인딩
    $stmt->bindParam(':preceding_rows', $preceding_rows, PDO::PARAM_INT);
    $stmt->bindParam(':restaurant_id', $restaurant_id, PDO::PARAM_INT);
    
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
// ... HTML 폼 및 결과 테이블 출력 부분 ...
?>