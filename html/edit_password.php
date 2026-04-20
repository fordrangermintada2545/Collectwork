<?php
include('db.php');
session_start();

if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }

$id = $_GET['id']; // รับ ID ลูกค้าจาก URL
$user_result = mysqli_query($conn, "SELECT username FROM users WHERE id = '$id'");
$user_data = mysqli_fetch_assoc($user_result);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_pass = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
    
    $update_sql = "UPDATE users SET password = '$new_pass' WHERE id = '$id'";
    
    if (mysqli_query($conn, $update_sql)) {
        echo "<script>alert('เปลี่ยนรหัสผ่านให้คุณ " . $user_data['username'] . " สำเร็จ!'); window.location='admin.php';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>เปลี่ยนรหัสผ่าน</title>
    <style>
        body { font-family: 'Tahoma', sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; background: #f0f2f5; }
        .box { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); width: 350px; }
        input { width: 100%; padding: 10px; margin: 15px 0; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background: #28a745; color: white; border: none; border-radius: 5px; cursor: pointer; }
    </style>
</head>
<body>
    <div class="box">
        <h3>เปลี่ยนรหัสผ่านให้: <br><span style="color:#007bff;"><?php echo $user_data['username']; ?></span></h3>
        <form method="POST">
            <input type="password" name="new_password" placeholder="ระบุรหัสผ่านใหม่" required>
            <button type="submit">ยืนยันการเปลี่ยนรหัสผ่าน</button>
        </form>
        <br>
        <a href="admin.php">ยกเลิกและกลับหน้าหลัก</a>
    </div>
</body>
</html>