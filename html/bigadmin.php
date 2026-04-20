<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('db.php');
session_start();

// ตรวจสอบสิทธิ์สูงสุด: ต้องเป็น bigadmin เท่านั้น
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'bigadmin') {
    echo "<script>alert('เฉพาะ Big Admin เท่านั้นที่เข้าหน้านี้ได้!'); window.location='member.php';</script>";
    exit();
}

// ระบบสร้าง Admin ใหม่ (ฝั่ง Big Admin สร้างให้เอง)
if (isset($_POST['create_admin'])) {
    $fname = mysqli_real_escape_string($conn, $_POST['firstname']);
    $lname = mysqli_real_escape_string($conn, $_POST['lastname']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $pass  = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role  = 'admin'; 

    $sql = "INSERT INTO users (firstname, lastname, phone, password, role) VALUES ('$fname', '$lname', '$phone', '$pass', '$role')";
    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('สร้างบัญชีผู้ดูแลระบบ (Admin) สำเร็จ!');</script>";
    }
}

// ดึงข้อมูลทุกคนในระบบ
$result = mysqli_query($conn, "SELECT * FROM users ORDER BY FIELD(role, 'bigadmin', 'admin', 'user'), id ASC");
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Control Panel | Big Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Kanit', sans-serif; }
        body { background-color: #1a1a2e; color: #e1e1e1; padding-bottom: 50px; }

        /* Header */
        header {
            background: #16213e;
            padding: 20px 8%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #e94560;
        }
        .logo-area { font-size: 24px; font-weight: 500; color: #e94560; }
        .admin-status { font-size: 14px; background: #e94560; color: white; padding: 5px 15px; border-radius: 50px; }

        .container { max-width: 1200px; margin: 40px auto; padding: 0 20px; }

        /* Section Boxes */
        .section {
            background: #16213e;
            padding: 30px;
            border-radius: 20px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        }
        h2 { margin-bottom: 20px; color: #fff; border-left: 4px solid #e94560; padding-left: 15px; }

        /* Create Admin Form */
        .form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; }
        input {
            background: #0f3460;
            border: 1px solid #16213e;
            padding: 12px;
            color: white;
            border-radius: 10px;
            outline: none;
        }
        input:focus { border-color: #e94560; }
        .btn-create {
            background: #e94560;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 500;
            transition: 0.3s;
        }
        .btn-create:hover { background: #ff4d6d; transform: scale(1.02); }

        /* User Table */
        .table-scroll { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { text-align: left; padding: 15px; background: #0f3460; color: #e94560; border-bottom: 2px solid #16213e; }
        td { padding: 15px; border-bottom: 1px solid #1a1a2e; font-size: 14px; }
        
        /* Badges */
        .badge { padding: 5px 12px; border-radius: 6px; font-size: 11px; font-weight: 500; }
        .bg-big { background: #e94560; color: white; }
        .bg-adm { background: #00d2d3; color: #1a1a2e; }
        .bg-usr { background: #576574; color: white; }

        .nav-link { color: #888; text-decoration: none; font-size: 14px; margin-right: 20px; }
        .nav-link:hover { color: #fff; }
        
    </style>
</head>
<body>

<header>
    <div class="logo-area">BIG ADMIN PANEL</div>
    <div>
        <a href="member.php" class="nav-link">หน้าสมาชิก</a>
        <span class="admin-status">SUPER USER</span>
    </div>
</header>

<div class="container">
    
    <div class="section">
        <h2>เพิ่มผู้ดูแลระบบ (Create Admin)</h2>
        <form method="POST" class="form-grid">
            <input type="text" name="firstname" placeholder="ชื่อจริง" required>
            <input type="text" name="lastname" placeholder="นามสกุล" required>
            <input type="text" name="phone" placeholder="เบอร์โทรศัพท์" required>
            <input type="password" name="password" placeholder="ตั้งรหัสผ่าน" required>
            <button type="submit" name="create_admin" class="btn-create">สร้างบัญชี Admin</button>
        </form>
    </div>

    <div class="section">
        <h2>จัดการสิทธิ์สมาชิกทั้งหมด</h2>
        <div class="table-scroll">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>ชื่อ-นามสกุล</th>
                        <th>เบอร์โทรศัพท์</th>
                        <th>ระดับสิทธิ์</th>
                        <th>วันที่เข้าร่วม</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td>#<?php echo $row['id']; ?></td>
                        <td><?php echo $row['firstname'] . " " . $row['lastname']; ?></td>
                        <td><?php echo $row['phone']; ?></td>
                        
                        <td>
                            <?php 
                                $r = $row['role'];
                                $c = ($r == 'bigadmin') ? 'bg-big' : (($r == 'admin') ? 'bg-adm' : 'bg-usr');
                                echo "<span class='badge $c'>" . strtoupper($r) . "</span>";
                            ?>
                        </td>
                        <td><?php echo date('d/m/Y', strtotime($row['created_at'])); ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div style="text-align: center;">
        <a href="logout.php" style="color: #e94560; text-decoration: none; font-size: 14px;">ออกจากระบบอย่างปลอดภัย</a>
    </div>

</div>

</body>
</html>