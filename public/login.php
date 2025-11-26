<?php
// public/login.php
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
        // username 으로 사용자 조회
        $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ?');
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        // password 컬럼에는 password_hash()로 해시된 값이 들어 있다고 가정
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = (int)$user['user_id'];
            $_SESSION['username'] = $user['username'];

            // 로그인 성공 후 메인으로 이동
            header('Location: index.php');
            exit;
        } else {
            $error = '아이디 또는 비밀번호가 올바르지 않습니다.';
        }
    }
}

include 'header.php';
?>

<h2>로그인</h2>

<form method="post">
    <div>
        <label>아이디(Username)
            <input type="text" name="username" required>
        </label>
    </div>
    <div>
        <label>비밀번호
            <input type="password" name="password" required>
        </label>
    </div>
    <button type="submit">로그인</button>
</form>

<?php if ($error): ?>
    <p style="color:red;"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
<?php endif; ?>

<?php include 'footer.php'; ?>
