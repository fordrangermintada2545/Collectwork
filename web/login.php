<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('db.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 1. รับค่า phone และ password จากฟอร์ม
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $pass = $_POST['password'];

    // 2. ตรวจสอบข้อมูลโดยใช้คอลัมน์ phone (ลบบรรทัดที่ดึงจาก email ออก)
    $result = mysqli_query($conn, "SELECT * FROM users WHERE phone = '$phone'");
    
    if (!$result) {
        die("Query Error: " . mysqli_error($conn));
    }

    $user = mysqli_fetch_assoc($result);

    // 3. ตรวจสอบรหัสผ่าน
    if ($user && password_verify($pass, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        // ตรวจสอบว่าใน Database ใช้ชื่อคอลัมน์ไหน (firstname หรือ username)
        $_SESSION['username'] = isset($user['firstname']) ? $user['firstname'] : (isset($user['username']) ? $user['username'] : 'User');
        $_SESSION['role'] = $user['role'];
        
        // ส่งไปหน้าตามระดับสิทธิ์
        if ($user['role'] == 'bigadmin') {
            header("Location: bigadmin.php");
        } elseif ($user['role'] == 'admin') {
            header("Location: admin.php");
        } else {
            header("Location: member.php");
        }
        exit();
    } else {
        // แจ้งเตือนถ้าข้อมูลผิด (เปลี่ยนข้อความให้ตรงกับที่ใช้ล็อกอิน)
        echo "<script>alert('เบอร์โทรศัพท์หรือรหัสผ่านผิดพลาด!'); window.location='login.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบ | MyWebsite</title>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        /* CSS ของคุณสวยอยู่แล้ว ใช้ตัวเดิมได้เลยครับ */
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Kanit', sans-serif; }
        body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); height: 100vh; display: flex; justify-content: center; align-items: center; }
        .login-container { background: rgba(255, 255, 255, 0.95); padding: 40px; border-radius: 20px; box-shadow: 0 15px 35px rgba(0,0,0,0.2); width: 100%; max-width: 400px; text-align: center; }
        .login-container h2 { margin-bottom: 10px; color: #333; font-weight: 500; }
        .login-container p { color: #777; font-size: 14px; margin-bottom: 30px; }
        .input-group { margin-bottom: 20px; text-align: left; }
        .input-group label { display: block; margin-bottom: 5px; color: #555; font-size: 14px; }
        .input-group input { width: 100%; padding: 12px 15px; border: 2px solid #eee; border-radius: 10px; outline: none; transition: 0.3s; }
        .input-group input:focus { border-color: #667eea; }
        .login-btn { width: 100%; padding: 12px; background: #667eea; color: white; border: none; border-radius: 10px; font-size: 16px; font-weight: 500; cursor: pointer; transition: 0.3s; margin-top: 10px; }
        .login-btn:hover { background: #5a67d8; transform: translateY(-2px); box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4); }
        .footer-links { margin-top: 25px; font-size: 14px; color: #777; }
        .footer-links a { color: #667eea; text-decoration: none; font-weight: 500; }
        .footer-links a:hover { text-decoration: underline; }
    </style>
</head>
<body>

<div class="login-container">
    <h2>ยินดีต้อนรับ</h2>
    <p>กรุณาเข้าสู่ระบบเพื่อใช้งาน</p>

    <form method="POST">
        <div class="input-group">
            <label>เบอร์มือถือ</label>
            <input type="text" name="phone" placeholder="0123456789" required>
        </div>

        <div class="input-group">
            <label>รหัสผ่าน</label>
            <input type="password" name="password" placeholder="••••••••" required>
        </div>

        <button type="submit" class="login-btn">เข้าสู่ระบบ</button>
    </form>

    <div class="footer-links">
        ยังไม่มีบัญชี? <a href="register.php">สมัครสมาชิกใหม่</a>
    </div>
</div>

</body>
</html>