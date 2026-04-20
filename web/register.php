<?php
include('db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fname = mysqli_real_escape_string($conn, $_POST['firstname']);
    $lname = mysqli_real_escape_string($conn, $_POST['lastname']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $pass  = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $check = mysqli_query($conn, "SELECT phone FROM users WHERE phone = '$phone'");
    if (mysqli_num_rows($check) > 0) {
        echo "<script>alert('เบอร์โทรศัพท์นี้ถูกใช้ไปแล้ว!');</script>";
    } else {
        $sql = "INSERT INTO users (firstname, lastname, phone, password) VALUES ('$fname', '$lname', '$phone', '$pass')";
        if (mysqli_query($conn, $sql)) {
            echo "<script>alert('สมัครสมาชิกสำเร็จ!'); window.location='login.php';</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สมัครสมาชิก | MyWebsite</title>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Kanit', sans-serif; }
        body { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            min-height: 100vh; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            padding: 20px; 
        }
        .reg-container { 
            background: rgba(255, 255, 255, 0.95); 
            padding: 40px; 
            border-radius: 20px; 
            box-shadow: 0 15px 35px rgba(0,0,0,0.2); 
            width: 100%; 
            max-width: 450px; 
        }
        h2 { text-align: center; margin-bottom: 10px; color: #333; font-weight: 500; }
        p.subtitle { text-align: center; color: #777; font-size: 14px; margin-bottom: 25px; }
        
        .input-group { margin-bottom: 15px; text-align: left; }
        .input-group label { display: block; margin-bottom: 5px; font-size: 14px; color: #555; }
        .input-group input { 
            width: 100%; 
            padding: 12px 15px; 
            border: 2px solid #eee; 
            border-radius: 12px; 
            outline: none; 
            transition: 0.3s; 
        }
        .input-group input:focus { border-color: #667eea; background: #fff; }
        
        .row { display: flex; gap: 15px; }
        .row .input-group { flex: 1; }
        
        .btn { 
            width: 100%; 
            padding: 14px; 
            background: #667eea; 
            color: white; 
            border: none; 
            border-radius: 12px; 
            font-size: 16px; 
            font-weight: 500; 
            cursor: pointer; 
            transition: 0.3s; 
            margin-top: 15px; 
        }
        .btn:hover { background: #5a67d8; transform: translateY(-2px); box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4); }
        
        .footer { text-align: center; margin-top: 25px; font-size: 14px; color: #777; }
        .footer a { color: #667eea; text-decoration: none; font-weight: 500; }
        .footer a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="reg-container">
        <h2>สร้างบัญชีใหม่</h2>
        <p class="subtitle">กรอกข้อมูลให้ครบถ้วนเพื่อเริ่มใช้งาน</p>
        <form method="POST">
            <div class="row">
                <div class="input-group">
                    <label>ชื่อจริง</label>
                    <input type="text" name="firstname" placeholder="ชื่อ" required>
                </div>
                <div class="input-group">
                    <label>นามสกุล</label>
                    <input type="text" name="lastname" placeholder="นามสกุล" required>
                </div>
            </div>
            <div class="input-group">
                <label>เบอร์โทรศัพท์</label>
                <input type="text" name="phone" placeholder="08XXXXXXXX" maxlength="10" required>
            </div>
            <div class="input-group">
                <label>กำหนดรหัสผ่าน</label>
                <input type="password" name="password" placeholder="รหัสผ่านของคุณ" required>
            </div>
            <button type="submit" class="btn">ยืนยันการสมัคร</button>
        </form>
        <div class="footer">
            เป็นสมาชิกอยู่แล้ว? <a href="login.php">เข้าสู่ระบบที่นี่</a>
        </div>
    </div>
</body>
</html>