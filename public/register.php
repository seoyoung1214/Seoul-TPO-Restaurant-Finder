<?php
session_start();
require_once __DIR__ . '/../config/db.php';

$pdo = getDB();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $password2 = $_POST['password2'] ?? '';
    $gender = $_POST['gender'] ?? null;
    $birth_year = (int)($_POST['birth_year'] ?? 0);


    
    // === Validation ===
    if ($username === '' || $password === '' || $password2 === '') {
        $error = '모든 필드를 입력해 주세요.';
    } elseif ($password !== $password2) {
        $error = '비밀번호가 일치하지 않습니다.';
    } elseif (strlen($username) < 3) {
        $error = '아이디는 3글자 이상이어야 합니다.';
    } elseif ($birth_year < 1900 || $birth_year > 2024) {
        $error = '출생 연도를 올바르게 입력해 주세요.';
    } else {
        // 아이디 중복 확인
        $check = $pdo->prepare("SELECT user_id FROM users WHERE username = ?");
        $check->execute([$username]);
        if ($check->fetch()) {
            $error = '이미 존재하는 아이디입니다.';
        } else {
            // 비밀번호 해시
            $hash = password_hash($password, PASSWORD_DEFAULT);

            // Insert user
            $stmt = $pdo->prepare("
                INSERT INTO users (username, password, gender, birth_year)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([$username, $hash, $gender ?: null, $birth_year]);

            $success = '회원가입이 완료되었습니다! 로그인해 주세요.';
        }
    }
}

include 'header.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>회원가입</title>
    <!-- 공통 style.css 링크 추가 -->
    <link rel="stylesheet" href="../css/style.css">
    <style>
        /* 회원가입 페이지 전용 스타일 (로그인 페이지와 유사) */
        .page-wrapper {
            display: flex;
            justify-content: center;
            padding: 50px 20px;
            min-height: 80vh;
        }
        .form-container {
            max-width: 450px; /* 로그인보다 약간 크게 설정 */
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
        .form-group input, .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            box-sizing: border-box;
            transition: border-color 0.3s;
        }
        .form-group input:focus, .form-group select:focus {
            border-color: #007bff;
            outline: none;
        }

        /* 버튼 스타일 */
        .btn-submit { 
            padding: 10px 15px; 
            background-color: #007bff; /* 가입 버튼은 초록색 계열로 */
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
            background-color: #1e7e34;
        }

        /* 메시지 스타일 */
        .message-success {
            padding: 10px;
            border-radius: 4px;
            background-color: #d4edda; 
            color: #155724; 
            border: 1px solid #c3e6cb;
            margin-bottom: 15px;
            font-weight: bold;
        }
        .message-error {
            padding: 10px;
            border-radius: 4px;
            background-color: #f8d7da; 
            color: #721c24; 
            border: 1px solid #f5c6cb;
            margin-bottom: 15px;
            font-weight: normal;
        }
        .login-link-group {
            margin-top: 20px;
            text-align: center; 
            font-size: 0.9em;
        }
        .login-link-group a {
            color: #007bff; 
            text-decoration: none; 
            font-weight: bold;
        }
    </style>
</head>
<body>
<div class="page-wrapper">
    <div class="form-container">
        <h2>회원가입</h2>

        <?php if ($error): ?>
            <p class="message-error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <?php if ($success): ?>
            <p class="message-success"><?= htmlspecialchars($success) ?></p>
        <?php endif; ?>

        <form method="post">
            <div class="form-group">
                <label>아이디 (Username)
                    <input type="text" name="username" required>
                </label>
            </div>

            <div class="form-group">
                <label>비밀번호
                    <input type="password" name="password" required>
                </label>
            </div>

            <div class="form-group">
                <label>비밀번호 확인
                    <input type="password" name="password2" required>
                </label>
            </div>

            <div class="form-group">
                <label>성별
                    <select name="gender">
                        <option value="">선택 안함</option>
                        <option value="M">남성</option>
                        <option value="F">여성</option>
                        <option value="O">기타</option>
                    </select>
                </label>
            </div>

            <div class="form-group">
                <label>출생 연도
                    <input type="number" name="birth_year" min="1900" max="2024" required>
                </label>
            </div>

            <button type="submit" class="btn-submit">회원가입</button>
        </form>

        <p class="login-link-group">
            이미 계정이 있으신가요?
            <a href="login.php">로그인</a>
        </p>
    </div>
</div>

<?php include 'footer.php'; ?>
</body>
</html>