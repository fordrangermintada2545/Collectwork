<?php
include('db.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 1. รับค่าและทำความสะอาดข้อมูล (Prevent SQL Injection)
    $phone_raw      = ltrim($_POST['phone_number'], '0'); // ตัด 0 ตัวหน้าออกถ้ามี
    $username       = "+66" . $phone_raw;
    $password       = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $real_name      = mysqli_real_escape_string($conn, $_POST['real_name']);
    $real_surname   = mysqli_real_escape_string($conn, $_POST['real_surname']);
    $bank_name      = mysqli_real_escape_string($conn, $_POST['bank_name']);
    $account_number = mysqli_real_escape_string($conn, $_POST['account_number']);
    $account_name   = mysqli_real_escape_string($conn, $_POST['account_name']);

    // 2. ตรวจสอบความซ้ำซ้อน (Unique Validation)
    // เราจะเช็คทีละเงื่อนไขเพื่อให้แจ้งเตือนผู้ใช้ได้ถูกต้อง
    
    // เช็คเบอร์โทรศัพท์ (Username)
    $check_phone = mysqli_query($conn, "SELECT id FROM users WHERE username = '$username'");
    if (mysqli_num_rows($check_phone) > 0) {
        die("<script>alert('❌ เบอร์โทรศัพท์นี้ถูกใช้งานไปแล้ว'); history.back();</script>");
    }

    // เช็คเลขบัญชีธนาคาร (Account Number)
    $check_acc = mysqli_query($conn, "SELECT id FROM users WHERE account_number = '$account_number'");
    if (mysqli_num_rows($check_acc) > 0) {
        die("<script>alert('❌ เลขบัญชีธนาคารนี้ถูกใช้งานไปแล้ว'); history.back();</script>");
    }

    // เช็คชื่อ-นามสกุลจริง (เพื่อกันคนเปลี่ยนเบอร์แต่ใช้ชื่อเดิมสมัครหลายไอดี)
    $check_name = mysqli_query($conn, "SELECT id FROM users WHERE real_name = '$real_name' AND real_surname = '$real_surname'");
    if (mysqli_num_rows($check_name) > 0) {
        die("<script>alert('❌ ชื่อและนามสกุลนี้มีอยู่ในระบบแล้ว ไม่สามารถสมัครซ้ำได้'); history.back();</script>");
    }

    // 3. ถ้าผ่านทุกด่าน ให้ทำการบันทึกข้อมูล
    $sql = "INSERT INTO users (username, password, real_name, real_surname, bank_name, account_number, account_name, balance) 
            VALUES ('$username', '$password', '$real_name', '$real_surname', '$bank_name', '$account_number', '$account_name', 0)";

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('✅ สมัครสมาชิกสำเร็จ!'); window.location='login.php';</script>";
    } else {
        // กรณี Error อื่นๆ เช่น DB พัง
        echo "<script>alert('เกิดข้อผิดพลาด: " . mysqli_error($conn) . "'); history.back();</script>";
    }
}
?>