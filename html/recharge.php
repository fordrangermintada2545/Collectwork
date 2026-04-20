<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>เติมเงิน | MyWebsite</title>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Kanit', sans-serif; background: #f4f7f6; display: flex; justify-content: center; padding-top: 50px; }
        .box { background: white; padding: 30px; border-radius: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); width: 100%; max-width: 400px; text-align: center; }
        input[type="file"] { margin: 20px 0; }
        .btn-upload { background: #2ed573; color: white; border: none; padding: 12px 25px; border-radius: 10px; cursor: pointer; width: 100%; }
    </style>
</head>
<body>
    <div class="box">
        <h2>💰 เติมเงินเข้าระบบ</h2>
        <p>อัปโหลดสลิปโอนเงิน (QR Code)</p>
        
        <img id="preview" src="#" alt="ตัวอย่างสลิป" style="display:none; width: 100%; max-height: 300px; object-fit: contain; margin-bottom: 20px; border-radius: 10px;">

        <form action="check_slip.php" method="POST" enctype="multipart/form-data">
            <input type="file" name="slip_image" id="slip_input" accept="image/*" required>
            <button type="submit" class="btn-upload">🚀 ตรวจสอบสลิปทันที</button>
        </form>
        
        <br>
        <a href="member.php" style="color: #888; text-decoration: none; font-size: 14px;">← ย้อนกลับไปหน้าสมาชิก</a>
    </div>
    <script>
        // สคริปต์สำหรับโชว์รูปสลิปทันทีที่เลือกไฟล์
        document.getElementById('slip_input').onchange = function (evt) {
            const [file] = this.files;
            if (file) {
                const preview = document.getElementById('preview');
                preview.src = URL.createObjectURL(file);
                preview.style.display = 'block';
            }
        }
    </script>
</body>
</html>