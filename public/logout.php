<?php
// 세션 시작
session_start();
$_SESSION = [];

// 세션 쿠키 삭제 (보안 강화를 위해 추가)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(), 
        '', 
        time() - 42000,
        $params["path"], 
        $params["domain"],
        $params["secure"], 
        $params["httponly"]
    );
}

// 세션 완전 종료
session_destroy();

// 로그아웃 후 메인 페이지로 이동
header("Location: /Seoul-TPO-Restaurant-Finder/public/index.php");
exit;
?>
