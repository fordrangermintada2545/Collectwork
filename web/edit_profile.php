<?php
include('db.php');
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_name = mysqli_real_escape_string($conn, $_POST['username']);
    $update = mysqli_query($conn, "UPDATE users SET username = '$new_name' WHERE id = '$user_id'");
    if ($update) {
        $_SESSION['username'] = $new_name; // อัปเดตชื่อใน session ด้วย
        echo "<script>alert('แก้ไขชื่อสำเร็จ!'); window.location='member.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>แก้ไขชื่อ</title>
    <style>
        body { font-family: 'Tahoma', sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; background: #f0f2f5; }
        .box { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); width: 320px; }
        input { width: 100%; padding: 10px; margin: 15px 0; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer; }
    </style>
</head>
<body>
    <div class="box">
        <h3>แก้ไขชื่อที่แสดง</h3>
        <form method="POST">
            <input type="text" name="username" value="<?php echo $_SESSION['username']; ?>" required>
            <button type="submit">บันทึกชื่อใหม่</button>
        </form>
        <br><a href="member.php">ย้อนกลับ</a>
    </div>
</body>
</html>