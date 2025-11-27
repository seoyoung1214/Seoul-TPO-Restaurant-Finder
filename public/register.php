<?php
// public/register.php
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

<h2>회원가입</h2>

<?php if ($error): ?>
    <p style="color:red;"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<?php if ($success): ?>
    <p style="color:green;"><?= htmlspecialchars($success) ?></p>
<?php endif; ?>

<form method="post">
    <div>
        <label>아이디 (Username)
            <input type="text" name="username" required>
        </label>
    </div>

    <div>
        <label>비밀번호
            <input type="password" name="password" required>
        </label>
    </div>

    <div>
        <label>비밀번호 확인
            <input type="password" name="password2" required>
        </label>
    </div>

    <div>
        <label>성별
            <select name="gender">
                <option value="">선택 안함</option>
                <option value="M">남성</option>
                <option value="F">여성</option>
                <option value="O">기타</option>
            </select>
        </label>
    </div>

    <div>
        <label>출생 연도
            <input type="number" name="birth_year" min="1900" max="2024" required>
        </label>
    </div>

    <button type="submit">회원가입</button>
</form>

<p>
    이미 계정이 있으신가요?
    <a href="login.php">로그인</a>
</p>

<?php include 'footer.php'; ?>
