<?php
include('db.php');
session_start();

// เช็คสิทธิ์ Admin
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'bigadmin')) {
    header("Location: member.php");
    exit();
}

$id = $_GET['id'];
$res = mysqli_query($conn, "SELECT firstname, lastname FROM users WHERE id = '$id'");
$customer = mysqli_fetch_assoc($res);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_pass = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
    $update = mysqli_query($conn, "UPDATE users SET password = '$new_pass' WHERE id = '$id'");
    
    if ($update) {
        echo "<script>alert('เปลี่ยนรหัสผ่านให้ลูกค้าสำเร็จ!'); window.location='admin.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการรหัสผ่านลูกค้า | Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        /* ใช้ CSS เดียวกับด้านบนเพื่อความต่อเนื่อง */
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Kanit', sans-serif; }
        body { background: #f4f7f6; height: 100vh; display: flex; justify-content: center; align-items: center; padding: 20px; }
        .box { background: white; padding: 40px; border-radius: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); width: 100%; max-width: 400px; text-align: center; border: 1px solid #eee; }
        h2 { margin-bottom: 5px; color: #333; }
        .customer-name { color: #667eea; font-weight: 500; margin-bottom: 25px; display: block; }
        .input-group { text-align: left; margin-bottom: 20px; }
        .input-group label { display: block; margin-bottom: 5px; font-size: 14px; color: #555; }
        .input-group input { width: 100%; padding: 12px 15px; border: 1px solid #ddd; border-radius: 10px; outline: none; }
        .btn-submit { width: 100%; padding: 12px; background: #2d3436; color: white; border: none; border-radius: 10px; font-size: 16px; cursor: pointer; transition: 0.3s; }
        .btn-submit:hover { background: #000; }
        .back-link { display: block; margin-top: 20px; color: #888; text-decoration: none; font-size: 14px; }
    </style>
</head>
<body>
    <div class="box">
        <h2>🛠 เปลี่ยนรหัสผ่านลูกค้า</h2>
        <span class="customer-name">คุณ <?php echo $customer['firstname'] . " " . $customer['lastname']; ?></span>
        
        <form method="POST">
            <div class="input-group">
                <label>กำหนดรหัสผ่านใหม่</label>
                <input type="password" name="new_password" placeholder="ระบุรหัสใหม่" required>
            </div>
            <button type="submit" class="btn-submit">บันทึกข้อมูล</button>
        </form>
        
        <a href="admin.php" class="back-link">← กลับหน้าจัดการ</a>
    </div>
</body>
</html>