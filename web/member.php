<?php
include('db.php');
session_start();

// ตรวจสอบว่าล็อกอินหรือยัง
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
// ดึงข้อมูลชื่อจาก Database
$res = mysqli_query($conn, "SELECT firstname, lastname, role FROM users WHERE id = '$user_id'");
$user = mysqli_fetch_assoc($res);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>พื้นที่สมาชิก | MyWebsite</title>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Kanit', sans-serif; }
        body { background-color: #f4f7f6; color: #333; }
        
        /* Navigation Bar */
        nav {
            background: white;
            padding: 0 8%;
            height: 70px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            position: sticky; top: 0; z-index: 1000;
        }
        .logo { font-size: 22px; font-weight: 500; color: #667eea; text-decoration: none; }

        /* User Menu Dropdown */
        .user-menu { position: relative; cursor: pointer; display: flex; align-items: center; gap: 10px; padding: 5px 10px; border-radius: 10px; transition: 0.3s; }
        .user-menu:hover { background: #f0f2f5; }
        
        .user-name { font-weight: 500; color: #444; }
        .user-avatar { width: 35px; height: 35px; background: #667eea; color: white; border-radius: 50%; display: flex; justify-content: center; align-items: center; font-size: 14px; }

        .dropdown-content {
            display: none;
            position: absolute;
            top: 100%; right: 0;
            background-color: white;
            min-width: 180px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
            border-radius: 12px;
            margin-top: 10px;
            overflow: hidden;
            border: 1px solid #eee;
        }
        .dropdown-content a {
            color: #444; padding: 12px 16px; text-decoration: none; display: block; font-size: 14px; transition: 0.2s;
        }
        .dropdown-content a:hover { background-color: #f8f9fa; color: #667eea; }
        .dropdown-content .logout { color: #ff4757; border-top: 1px solid #eee; }
        
        /* Show dropdown on click (via JS) */
        .show { display: block; }

        /* Main Content */
        .main-content { max-width: 1000px; margin: 40px auto; padding: 0 20px; }
        .welcome-banner {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 20px; padding: 40px; color: white; margin-bottom: 30px;
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.2);
        }
        
        .grid-menu { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; }
        .card {
            background: white; padding: 30px; border-radius: 15px; text-decoration: none; color: #333;
            transition: 0.3s; border: 1px solid #eee; display: flex; flex-direction: column; align-items: flex-start;
        }
        .card:hover { transform: translateY(-5px); border-color: #667eea; box-shadow: 0 8px 20px rgba(0,0,0,0.05); }
        .card h3 { margin-bottom: 10px; color: #444; }
        .card p { font-size: 13px; color: #888; }
    </style>
</head>
<body>

<nav>
    <a href="member.php" class="logo">MyWebsite</a>
    
    <div class="user-menu" onclick="toggleDropdown()">
        <div class="user-avatar"><?php echo mb_substr($user['firstname'], 0, 1, 'UTF-8'); ?></div>
        <span class="user-name">คุณ <?php echo $user['firstname']; ?> ▼</span>
        
        <div id="myDropdown" class="dropdown-content">
            <a href="change_password.php">🔑 เปลี่ยนรหัสผ่าน</a>
            <?php if ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'bigadmin'): ?>
                <a href="admin.php">⚙️ หน้าจัดการ (Admin)</a>
            <?php endif; ?>
            <?php if ($_SESSION['role'] == 'bigadmin'): ?>
                <a href="bigadmin.php">👑 หน้า Big Admin</a>
            <?php endif; ?>
            <a href="logout.php" class="logout">🚪 ออกจากระบบ</a>
        </div>
    </div>
</nav>

<div class="main-content">
    <div class="welcome-banner">
        <h2>สวัสดีครับ, คุณ <?php echo $user['firstname'] . " " . $user['lastname']; ?></h2>
        <p>ยินดีต้อนรับสู่พื้นที่สมาชิก คุณสามารถเลือกดูข้อมูลต่างๆ ได้จากเมนูด้านล่าง</p>
    </div>

    <div class="grid-menu">
        <a href="guide.php" class="card">
            <h3>📘 คำแนะนำการใช้งาน</h3>
            <p>เรียนรู้วิธีการทำงานของระบบเบื้องต้น</p>
        </a>
        <a href="how-to.php" class="card">
            <h3>💡 วิธีใช้งานฟีเจอร์ต่างๆ</h3>
            <p>ขั้นตอนการใช้งานระบบอย่างละเอียด</p>
        </a>
        <a href="contact.php" class="card">
            <h3>📞 ติดต่อเจ้าหน้าที่</h3>
            <p>พบปัญหาการใช้งาน แจ้งแอดมินได้ตลอด 24 ชม.</p>
        </a>
        <a href="index.php" class="card">
            <h3>🏠 กลับหน้าหลัก</h3>
            <p>ไปยังหน้าแรกของเว็บไซต์</p>
        </a>
    </div>
</div>

<script>
/* ฟังก์ชัน เปิด-ปิด Dropdown */
function toggleDropdown() {
    document.getElementById("myDropdown").classList.toggle("show");
}

/* ปิด Dropdown ถ้าผู้ใช้คลิกข้างนอกเมนู */
window.onclick = function(event) {
  if (!event.target.matches('.user-menu') && !event.target.matches('.user-name') && !event.target.matches('.user-avatar')) {
    var dropdowns = document.getElementsByClassName("dropdown-content");
    for (var i = 0; i < dropdowns.length; i++) {
      var openDropdown = dropdowns[i];
      if (openDropdown.classList.contains('show')) {
        openDropdown.classList.remove('show');
      }
    }
  }
}
</script>

</body>
</html>