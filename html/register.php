
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สมัครสมาชิก - KonradShop</title>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Kanit', sans-serif; }
        /* ปรับแต่ง Select2 ให้เข้ากับดีไซน์ */
        .select2-container--default .select2-selection--single {
            height: 52px; border-radius: 12px; border-color: #e5e7eb; display: flex; align-items: center;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 52px; padding-left: 12px; color: #374151;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow { height: 52px; }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-900 to-indigo-900 min-h-screen flex items-center justify-center p-4">

    <div class="bg-white max-w-lg w-full rounded-[2.5rem] shadow-2xl p-8 md:p-10 relative">
        
        <a href="login.php" class="absolute left-8 top-10 text-slate-400 hover:text-indigo-600">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </a>

        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-slate-800">ลงทะเบียน</h1>
            <p class="text-slate-400 mt-2">กรอกข้อมูลบัญชีเพื่อเริ่มใช้งาน</p>
        </div>

        <form action="register_db.php" method="POST" class="space-y-4">
            
            <div class="space-y-1">
                <label class="text-sm font-semibold text-slate-600 ml-1">เบอร์โทรศัพท์ (Username)</label>
                <div class="flex gap-2">
                    <div class="w-[80px] bg-slate-100 px-3 py-3 rounded-xl border border-gray-200 text-center text-slate-600 font-bold">
                        +66
                    </div>
                    <input type="tel" name="phone_number" placeholder="812345678" required 
                        pattern="[0-9]{9,10}" title="กรุณากรอกเบอร์โทรศัพท์ 9-10 หลัก"
                        class="flex-1 px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                </div>
                <p class="text-[10px] text-slate-400 ml-1">* ไม่ต้องกรอกเลข 0 ตัวแรก (เช่น 812345678)</p>
            </div>

            <input type="password" name="password" placeholder="กำหนดรหัสผ่าน" required 
                class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500 focus:outline-none">

            <div class="grid grid-cols-2 gap-3">
                <input type="text" name="real_name" placeholder="ชื่อจริง" required class="px-4 py-3 rounded-xl border border-gray-200 focus:outline-none">
                <input type="text" name="real_surname" placeholder="นามสกุล" required class="px-4 py-3 rounded-xl border border-gray-200 focus:outline-none">
            </div>

            <div class="space-y-1">
                <label class="text-sm font-semibold text-slate-600 ml-1">ธนาคาร</label>
                <select name="bank_name" id="bank_select" class="w-full" required>
                    <option value="">ค้นหาหรือเลือกธนาคาร...</option>
                    <optgroup label="ธนาคารหลัก">
                        <option value="กสิกรไทย">กสิกรไทย (KBANK)</option>
                        <option value="ไทยพาณิชย์">ไทยพาณิชย์ (SCB)</option>
                        <option value="กรุงไทย">กรุงไทย (KTB)</option>
                        <option value="กรุงเทพ">กรุงเทพ (BBL)</option>
                        <option value="ทหารไทยธนชาต">ทหารไทยธนชาต (TTB)</option>
                        <option value="กรุงศรีอยุธยา">กรุงศรีอยุธยา (BAY)</option>
                    </optgroup>
                    <optgroup label="ธนาคารอื่นๆ">
                        <option value="ออมสิน">ออมสิน (GSB)</option>
                        <option value="ยูโอบี">ยูโอบี (UOB)</option>
                        <option value="ธ.ก.ส.">ธ.ก.ส. (BAAC)</option>
                        <option value="อาคารสงเคราะห์">อาคารสงเคราะห์ (GHB)</option>
                        <option value="เกียรตินาคินภัทร">เกียรตินาคินภัทร (KKP)</option>
                        <option value="ทิสโก้">ทิสโก้ (TISCO)</option>
                        <option value="ซีไอเอ็มบี ไทย">ซีไอเอ็มบี ไทย (CIMB)</option>
                        <option value="แลนด์ แอนด์ เฮ้าส์">แลนด์ แอนด์ เฮ้าส์ (LHBANK)</option>
                        <option value="ไอซีบีซี">ไอซีบีซี (ICBC)</option>
                        <option value="ไทยเครดิต">ไทยเครดิต (TCRB)</option>
                    </optgroup>
                </select>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <input type="text" name="account_number" placeholder="เลขบัญชีธนาคาร" required class="px-4 py-3 rounded-xl border border-gray-200">
                <input type="text" name="account_name" placeholder="ชื่อบัญชี (ภาษาไทย)" required class="px-4 py-3 rounded-xl border border-gray-200">
            </div>

            <button type="submit" class="w-full py-4 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-2xl shadow-lg transition-all active:scale-95 mt-4">
                ยืนยันสมัครสมาชิก
            </button>
        </form>

        <div class="text-center mt-6">
            <p class="text-slate-500 text-sm">มีบัญชีอยู่แล้ว? <a href="login.php" class="text-indigo-600 font-bold hover:underline">เข้าสู่ระบบ</a></p>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // ใช้ Select2 แบบปกติ ไม่ต้องมีฟังก์ชันรูปภาพ
            $('#bank_select').select2({
                width: '100%'
            });
        });
    </script>
</body>
</html>