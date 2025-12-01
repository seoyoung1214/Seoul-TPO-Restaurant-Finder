<?php
session_start();
require_once __DIR__ . "/header.php";
require_once __DIR__ . '/../config/db.php';

$pdo = getDB();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = '아이디와 비밀번호를 모두 입력해 주세요.';
    } else {
        $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ?');
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = (int)$user['user_id'];
            $_SESSION['username'] = $user['username'];

            header('Location: index.php');
            exit;
        } else {
            $error = '아이디 또는 비밀번호가 올바르지 않습니다.';
        }
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>로그인</title>
    <!-- 공통 style.css 링크 추가 -->
    <link rel="stylesheet" href="../css/style.css">
    <style>
        /* 로그인 페이지 전용 스타일 */
        .page-wrapper {
            display: flex;
            justify-content: center;
            padding: 50px 20px;
            min-height: 80vh;
        }
        .form-container {
            max-width: 400px; /* 로그인 폼은 더 작게 설정 */
            width: 100%;
            padding: 30px;
            border: 1px solid #ddd;
            border-radius: 10px;
            background-color: #ffffff;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }
        
        h2 {
            color: #333;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
            margin-bottom: 25px;
            font-size: 1.8em;
        }
        
        /* 폼 그룹 스타일 */
        .form-group { margin-bottom: 15px; }
        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
            color: #555;
        }
        .form-group input[type="text"],
        .form-group input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            box-sizing: border-box;
            transition: border-color 0.3s;
        }
        .form-group input:focus {
            border-color: #007bff;
            outline: none;
        }

        /* 버튼 스타일 */
        .btn-submit { 
            padding: 10px 15px; 
            background-color: #007bff; 
            color: white; 
            border: none; 
            border-radius: 4px; 
            cursor: pointer; 
            font-size: 1em;
            width: 100%;
            margin-top: 10px;
            transition: background-color 0.2s;
        }
        .btn-submit:hover {
            background-color: #0056b3;
        }

        /* 에러 메시지 스타일 */
        .error-message {
            padding: 10px;
            border-radius: 4px;
            background-color: #f8d7da; /* 연한 빨강 배경 */
            color: #721c24; /* 진한 빨강 텍스트 */
            border: 1px solid #f5c6cb;
            margin-top: 15px;
            font-weight: normal;
        }
    </style>
</head>
<body>
<div class="page-wrapper">
    <div class="form-container">
        <h2>로그인</h2>

        <form method="post">
            <div class="form-group">
                <label>아이디(Username)
                    <input type="text" name="username" required>
                </label>
            </div>
            <div class="form-group">
                <label>비밀번호
                    <input type="password" name="password" required>
                </label>
            </div>
            <button type="submit" class="btn-submit">로그인</button>
        </form>

        <?php if ($error): ?>
            <p class="error-message"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>
        
        <p style="margin-top: 20px; text-align: center; font-size: 0.9em;">
             계정이 없으신가요? <a href="register.php" style="color: #007bff; text-decoration: none; font-weight: bold;">회원가입</a>
        </p>
    </div>
</div>

<?php include 'footer.php'; ?>
</body>
</html>