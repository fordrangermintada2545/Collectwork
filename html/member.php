<?php
include('db.php');
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT username, real_name, real_surname, balance, role FROM users WHERE id = '$user_id'";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);

$current_balance = number_format($user['balance'], 2);
$first_char = mb_substr($user['real_name'], 0, 1, 'UTF-8');
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>พื้นที่สมาชิก | TestWebsiteAuto</title>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Kanit', sans-serif; background-color: #f8fafc; }
        .nav-shadow { box-shadow: 0 2px 15px rgba(0,0,0,0.05); }
        .dropdown-content { display: none; }
        .show { display: block; }
        /* สไตล์สำหรับบล็อกรูปภาพ */
        .img-placeholder {
            aspect-ratio: 16 / 9;
            background: #e2e8f0;
            border: 2px dashed #cbd5e1;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 1.5rem;
            color: #94a3b8;
            transition: 0.3s;
        }
        .img-placeholder:hover { border-color: #6366f1; color: #6366f1; }
    </style>
</head>
<body>

<nav class="bg-white h-20 nav-shadow sticky top-0 z-50 px-4 md:px-[8%] flex justify-between items-center">
    <a href="member.php" class="text-2xl font-bold text-indigo-600">TestWebsiteAuto</a>

    <div class="flex items-center gap-4">
        <div class="bg-slate-50 px-4 py-2 rounded-2xl border border-slate-100 text-right">
            <p class="text-[10px] text-slate-400 leading-none">ยอดเงินคงเหลือ</p>
            <p class="text-lg font-bold text-slate-700">฿<?php echo $current_balance; ?></p>
        </div>

        <div class="relative cursor-pointer" onclick="toggleDropdown()">
            <div class="flex items-center gap-2 hover:bg-slate-50 p-2 rounded-2xl transition-all">
                <div class="w-10 h-10 bg-indigo-600 text-white rounded-full flex items-center justify-center font-bold">
                    <?php echo $first_char; ?>
                </div>
                <span class="text-sm font-medium text-slate-700">คุณ <?php echo $user['real_name']; ?> ▾</span>
            </div>
            
            <div id="myDropdown" class="dropdown-content absolute right-0 mt-3 w-56 bg-white rounded-2xl shadow-2xl border border-slate-100 overflow-hidden">
                <div class="py-2">
                    <a href="recharge.php" class="block px-4 py-3 text-sm text-slate-600 hover:bg-emerald-50 hover:text-emerald-600">
                        💰 เติมเงิน (ฝาก)
                    </a>
                    <a href="withdraw.php" class="block px-4 py-3 text-sm text-slate-600 hover:bg-rose-50 hover:text-rose-600">
                        💸 ถอนเงิน
                    </a>
                    
                    <div class="border-t border-slate-100 my-1"></div>

                    <a href="change_password.php" class="block px-4 py-3 text-sm text-slate-600 hover:bg-indigo-50 hover:text-indigo-600">
                        🔑 เปลี่ยนรหัสผ่าน
                    </a>
                    <?php if ($user['role'] == 'admin' || $user['role'] == 'bigadmin'): ?>
                        <a href="admin.php" class="block px-4 py-3 text-sm text-slate-600 hover:bg-indigo-50 hover:text-indigo-600">
                            ⚙️ หน้าจัดการ (Admin)
                        </a>
                    <?php endif; ?>
                    
                    <a href="logout.php" class="block px-4 py-3 text-sm text-rose-500 hover:bg-rose-50 border-t border-slate-50">
                        🚪 ออกจากระบบ
                    </a>
                </div>
            </div>
        </div>
    </div>
</nav>

<div class="max-w-7xl mx-auto px-4 py-10">
    
    <div class="mb-10">
        <h2 class="text-3xl font-bold text-slate-800">ยินดีต้อนรับ, <?php echo $user['real_name']; ?>!</h2>
        <p class="text-slate-500 mt-2">เลือกดูรายการและบริการต่างๆ ของเรา</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="img-placeholder"><span>เพิ่มรูปภาพ 1</span></div>
        <div class="img-placeholder"><span>เพิ่มรูปภาพ 2</span></div>
        <div class="img-placeholder"><span>เพิ่มรูปภาพ 3</span></div>
        <div class="img-placeholder"><span>เพิ่มรูปภาพ 4</span></div>
        <div class="img-placeholder"><span>เพิ่มรูปภาพ 5</span></div>
        <div class="img-placeholder"><span>เพิ่มรูปภาพ 6</span></div>
    </div>

</div>

<script>
function toggleDropdown() {
    document.getElementById("myDropdown").classList.toggle("show");
}

// ปิด dropdown เมื่อคลิกที่อื่น
window.onclick = function(event) {
    if (!event.target.closest('.relative')) {
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