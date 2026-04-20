<?php
include('db.php');
session_start();

// ระบบป้องกัน: ต้องเป็น bigadmin เท่านั้น
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'bigadmin') {
    header("Location: admin_login.php");
    exit();
}

// --- ส่วนจัดการข้อมูล (POST) ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 1. เพิ่ม Admin ใหม่
    // 1. เพิ่ม Admin ใหม่
    if (isset($_POST['add_staff'])) {
        $user = mysqli_real_escape_string($conn, $_POST['username']);
        $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $nickname = mysqli_real_escape_string($conn, $_POST['nickname']);
        $phone = mysqli_real_escape_string($conn, $_POST['phone']); // เลขที่จะส่งเข้า account_number
        $line = mysqli_real_escape_string($conn, $_POST['line_id']);

        // --- 1. เช็คชื่อผู้ใช้ซ้ำ ---
        $check_user = mysqli_query($conn, "SELECT id FROM users WHERE username = '$user'");
        if (mysqli_num_rows($check_user) > 0) {
            echo "<script>alert('❌ ชื่อผู้ใช้นี้ ($user) มีอยู่ในระบบแล้ว'); history.back();</script>";
            exit();
        }

        // --- 2. เช็คเลขบัญชี/เบอร์โทรซ้ำ (ตัวที่ทำให้เกิด Error รอบนี้) ---
        $check_acc = mysqli_query($conn, "SELECT id FROM users WHERE account_number = '$phone'");
        if (mysqli_num_rows($check_acc) > 0) {
            echo "<script>alert('❌ เบอร์โทรนี้ ($phone) มีอยู่ในระบบแล้ว'); history.back();</script>";
            exit();
        }

        // ถ้าผ่านทั้งคู่ค่อยทำการบันทึก
        $sql = "INSERT INTO users (username, password, real_name, real_surname, account_number, role) 
                VALUES ('$user', '$pass', '$nickname', '$line', '$phone', 'admin')";
        
        if(mysqli_query($conn, $sql)) {
            echo "<script>alert('✅ สร้าง Admin สำเร็จ'); window.location='manage_staff.php';</script>";
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    }
    
    // 2. จัดการยอดเงินลูกค้า (เพิ่ม/ลด)
    if (isset($_POST['update_balance'])) {
        $target_id = $_POST['user_id'];
        $amount = floatval($_POST['amount']);
        mysqli_query($conn, "UPDATE users SET balance = balance + ($amount) WHERE id = '$target_id'");
    }

    // 3. เปลี่ยนรหัสผ่าน (ทั้ง Admin และลูกค้า)
    if (isset($_POST['change_pw'])) {
        $target_id = $_POST['user_id'];
        $new_pw = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
        mysqli_query($conn, "UPDATE users SET password = '$new_pw' WHERE id = '$target_id'");
    }

    // 4. ลบบัญชี (Admin เท่านั้น)
    if (isset($_GET['delete_staff'])) {
        $id = $_GET['delete_staff'];
        mysqli_query($conn, "DELETE FROM users WHERE id = '$id' AND role = 'admin'");
    }
    
}

// ดึงข้อมูลพนักงาน (Admin)
$staffs = mysqli_query($conn, "SELECT * FROM users WHERE role = 'admin'");
// ดึงข้อมูลลูกค้า (User)
$customers = mysqli_query($conn, "SELECT * FROM users WHERE role = 'user' ORDER BY last_login_at DESC");
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>👑 ระบบจัดการสูงสุด | Bigadmin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;600&display=swap" rel="stylesheet">
    <style>body { font-family: 'Kanit', sans-serif; }</style>
</head>
<body class="bg-slate-900 text-slate-200 p-4 md:p-8">

    <div class="max-w-7xl mx-auto">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-4">
            <div>
                <h1 class="text-3xl font-bold text-white">ระบบจัดการสูงสุด (Bigadmin)</h1>
                <p class="text-slate-400">จัดการทั้งทีมงานแอดมินและข้อมูลลูกค้าในหน้าเดียว</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="admin_withdraw.php" class="bg-rose-500 hover:bg-rose-600 text-white px-5 py-2 rounded-xl text-sm font-bold shadow-lg">อนุมัติถอนเงิน</a>
                <a href="admin_slips.php" class="bg-emerald-500 hover:bg-emerald-600 text-white px-5 py-2 rounded-xl text-sm font-bold shadow-lg">เช็คสลิปเติมเงิน</a>
                <a href="logout.php" class="bg-slate-700 hover:bg-slate-600 text-white px-5 py-2 rounded-xl text-sm font-bold">ออกจากระบบ</a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-slate-800 p-6 rounded-[2rem] border border-slate-700 shadow-xl">
                    <h3 class="text-lg font-bold mb-4 text-indigo-400 flex items-center gap-2">
                        <span>👤 เพิ่ม Admin ใหม่</span>
                    </h3>
                    <form method="POST" class="space-y-3">
                        <input type="text" name="username" placeholder="Username" required class="w-full bg-slate-900 border-none rounded-xl p-3 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                        <input type="password" name="password" placeholder="Password" required class="w-full bg-slate-900 border-none rounded-xl p-3 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                        <input type="text" name="nickname" placeholder="ชื่อเล่น" required class="w-full bg-slate-900 border-none rounded-xl p-3 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                        <input type="text" name="line_id" placeholder="ID Line" class="w-full bg-slate-900 border-none rounded-xl p-3 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                        <input type="text" name="phone" placeholder="เบอร์โทร" class="w-full bg-slate-900 border-none rounded-xl p-3 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                        <button type="submit" name="add_staff" class="w-full bg-indigo-600 py-3 rounded-xl font-bold hover:bg-indigo-700 transition-all">บันทึกแอดมิน</button>
                    </form>
                </div>

                <div class="bg-slate-800 p-6 rounded-[2rem] border border-slate-700 shadow-xl overflow-hidden">
                    <h3 class="text-lg font-bold mb-4 text-slate-300">ทีมงานแอดมิน</h3>
                    <div class="space-y-4">
                        <?php while($s = mysqli_fetch_assoc($staffs)): ?>
                        <div class="bg-slate-900/50 p-4 rounded-2xl border border-slate-700">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="font-bold text-white"><?php echo $s['real_name']; ?></p>
                                    <p class="text-[10px] text-slate-500">User: <?php echo $s['username']; ?></p>
                                </div>
                                <a href="?delete_staff=<?php echo $s['id']; ?>" onclick="return confirm('ลบแอดมิน?')" class="text-rose-500 text-xs">ลบ</a>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-3">
                <div class="bg-slate-800 rounded-[2rem] border border-slate-700 shadow-xl overflow-hidden">
                    <div class="p-6 border-b border-slate-700 bg-slate-800/50">
                        <h3 class="text-xl font-bold text-white">จัดการข้อมูลลูกค้า</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-slate-900/50 text-slate-400 text-xs uppercase">
                                <tr>
                                    <th class="p-5">ลูกค้า / ข้อมูลติดต่อ</th>
                                    <th class="p-5">ธนาคาร</th>
                                    <th class="p-5">ยอดเงิน</th>
                                    <th class="p-5">IP / ออนไลน์ล่าสุด</th>
                                    <th class="p-5">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-700">
                                <?php while($row = mysqli_fetch_assoc($customers)): ?>
                                <tr class="hover:bg-slate-700/20 transition-all text-sm">
                                    <td class="p-5">
                                        <p class="font-bold text-white"><?php echo $row['real_name']." ".$row['real_surname']; ?></p>
                                        <p class="text-[10px] text-slate-500"><?php echo $row['username']; ?></p>
                                    </td>
                                    <td class="p-5">
                                        <p class="text-indigo-400"><?php echo $row['bank_name']; ?></p>
                                        <p class="text-[11px] font-mono"><?php echo $row['account_number']; ?></p>
                                    </td>
                                    <td class="p-5">
                                        <span class="text-lg font-bold text-emerald-500">฿<?php echo number_format($row['balance'], 2); ?></span>
                                    </td>
                                    <td class="p-5">
                                        <p class="text-[10px] font-mono text-slate-400"><?php echo $row['last_ip'] ?? '-'; ?></p>
                                        <p class="text-[10px] text-slate-500"><?php echo $row['last_login_at'] ?? 'Never'; ?></p>
                                    </td>
                                    <td class="p-5">
                                        <div class="space-y-2">
                                            <form method="POST" class="flex gap-1">
                                                <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
                                                <input type="number" name="amount" placeholder="+/-" class="w-16 bg-slate-900 text-[10px] p-1.5 rounded-lg border-none focus:ring-1 focus:ring-emerald-500">
                                                <button name="update_balance" class="bg-emerald-600 text-[10px] px-2 rounded-lg font-bold">ตกลง</button>
                                            </form>
                                            <form method="POST" class="flex gap-1">
                                                <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
                                                <input type="text" name="new_password" placeholder="รหัสใหม่" class="w-16 bg-slate-900 text-[10px] p-1.5 rounded-lg border-none focus:ring-1 focus:ring-amber-500">
                                                <button name="change_pw" class="bg-amber-500 text-black text-[10px] px-2 rounded-lg font-bold">แก้ PW</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

</body>
</html>