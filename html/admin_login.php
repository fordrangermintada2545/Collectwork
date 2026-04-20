<?php
include('db.php');
session_start();

// แก้ไขจุดนี้: ถ้าล็อกอินค้างไว้
if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'bigadmin') {
        header("Location: admin.php");
        exit();
    } else {
        header("Location: member.php");
        exit();
    }
}
// ... โค้ดที่เหลือเหมือนเดิม ...

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    // ตรวจสอบจากตาราง users โดยเน้นที่สถานะ Admin เท่านั้น
    $sql = "SELECT * FROM users WHERE username = '$username' AND (role = 'admin' OR role = 'bigadmin')";
    $result = mysqli_query($conn, $sql);

    // แก้ไขบรรทัดที่ประมาณ 22
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        // ค้นหาส่วนที่ล็อกอินสำเร็จใน admin_login.php แล้วเปลี่ยนเป็นโค้ดนี้:
        if (password_verify($password, $row['password'])) {
            // บันทึก Session ตามปกติ
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['real_name'] = $row['real_name'];
            $_SESSION['role'] = $row['role'];

            // อัปเดต IP และเวลา
            $ip = $_SERVER['REMOTE_ADDR'];
            mysqli_query($conn, "UPDATE users SET last_ip = '$ip', last_login_at = NOW() WHERE id = '{$row['id']}'");

            // --- ส่วนแยกหน้า (เปลี่ยนตรงนี้) ---
            if ($_SESSION['role'] == 'bigadmin') {
                header("Location: manage_staff.php"); // ถ้าเป็น Big ให้ไปหน้าจัดการพนักงาน/ระบบใหญ่
            } else {
                header("Location: admin.php"); // ถ้าเป็น Admin ปกติ ให้ไปหน้าดูแลลูกค้า
            }
            exit();
        }
    } else {
        // ลองเพิ่ม Username เข้าไปในข้อความ Error เพื่อเช็คว่าเราพิมพ์ถูกไหม
        $error = "ไม่พบชื่อผู้ใช้งาน '$username' ที่มีสิทธิ์ Admin หรือ Bigadmin ในระบบ";
}
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>Admin Login | KonradSystem</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;600&display=swap" rel="stylesheet">
</head>
<body class="bg-slate-900 flex items-center justify-center min-h-screen font-['Kanit']">

    <div class="bg-slate-800 p-8 rounded-[2rem] shadow-2xl w-full max-w-md border border-slate-700">
        <div class="text-center mb-8">
            <div class="bg-indigo-600 w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg shadow-indigo-500/50">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-white">Staff Only</h1>
            <p class="text-slate-400 text-sm">เข้าสู่ระบบจัดการสำหรับเจ้าหน้าที่</p>
        </div>

        <?php if (isset($error)): ?>
            <div class="bg-rose-500/10 border border-rose-500/50 text-rose-500 p-3 rounded-xl text-sm mb-6 text-center">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST" class="space-y-4">
            <div>
                <label class="text-slate-300 text-sm ml-1">Username</label>
                <input type="text" name="username" placeholder="Username" required 
                    class="w-full bg-slate-700 border border-slate-600 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all mt-1">
            </div>
            <div>
                <label class="text-slate-300 text-sm ml-1">Password</label>
                <input type="password" name="password" placeholder="••••••••" required 
                    class="w-full bg-slate-700 border border-slate-600 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all mt-1">
            </div>
            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 rounded-xl shadow-lg transition-all active:scale-95">
                ACCESS SYSTEM
            </button>
        </form>
        
    </div>

</body>
</html>