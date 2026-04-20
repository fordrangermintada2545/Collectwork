<?php
include('db.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $phone_input = mysqli_real_escape_string($conn, $_POST['phone']); // รับค่า 0812345678
    $password = $_POST['password'];

    // 1. จัดการเบอร์โทรให้เป็นรูปแบบ +66
    // ltrim($phone_input, '0') จะตัดเลข 0 ที่อยู่หน้าสุดออก
    $username = "+66" . ltrim($phone_input, '0'); 

    // 2. ค้นหาในคอลัมน์ username (ที่เราเพิ่งสร้าง SQL ไปใหม่)
    $sql = "SELECT * FROM users WHERE username = '$username'";
    $result = mysqli_query($conn, $sql);
    // ตัวอย่างโค้ดที่ควรเพิ่มใน login.php ตอนล็อกอินสำเร็จ
    $ip = $_SERVER['REMOTE_ADDR'];
    $user_id = $row['id'];
    mysqli_query($conn, "UPDATE users SET last_ip = '$ip', last_login_at = NOW() WHERE id = '$user_id'");
    
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        
        // 3. ตรวจสอบรหัสผ่าน (ที่ใช้ password_hash ตอนสมัคร)
        if (password_verify($password, $row['password'])) {
            // ล็อกอินสำเร็จ
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['real_name'] = $row['real_name'];

            echo "<script>alert('เข้าสู่ระบบสำเร็จ'); window.location='member.php';</script>";
        } else {
            echo "<script>alert('รหัสผ่านไม่ถูกต้อง'); history.back();</script>";
        }
    } else {
        echo "<script>alert('ไม่พบเบอร์โทรศัพท์นี้ในระบบ'); history.back();</script>";
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