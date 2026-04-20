import customtkinter as ctk
import requests
import os
import threading

# ตั้งค่าลิงก์ GitHub ของคุณ
RAW_VERSION_URL = "https://raw.githubusercontent.com/fordrangermintada2545/betatest/main/version.txt"
RAW_SCRIPT_URL = "https://raw.githubusercontent.com/fordrangermintada2545/betatest/main/script.py"
LOCAL_VERSION_FILE = "current_version.txt"

class UpdateLauncher(ctk.CTk):
    def __init__(self):
        super().__init__()

        self.title("Software Updater")
        self.geometry("400x200")
        self.resizable(False, False)

        # ส่วนแสดงสถานะ
        self.label = ctk.CTkLabel(self, text="กำลังตรวจสอบการอัปเดต...", font=("Kanit", 16))
        self.label.pack(pady=20)

        # หลอดโหลด
        self.progress = ctk.CTkProgressBar(self, width=300)
        self.progress.pack(pady=10)
        self.progress.set(0)

        # เริ่มเช็คอัปเดตใน Thread แยก (เพื่อไม่ให้หน้าต่างค้าง)
        threading.Thread(target=self.check_update, daemon=True).start()

    def check_update(self):
        try:
            # 1. เช็คเวอร์ชัน
            current_v = "0"
            if os.path.exists(LOCAL_VERSION_FILE):
                with open(LOCAL_VERSION_FILE, "r") as f:
                    current_v = f.read().strip()

            response = requests.get(RAW_VERSION_URL)
            latest_v = response.text.strip()

            if latest_v > current_v:
                self.label.configure(text=f"กำลังอัปเดตเป็นเวอร์ชัน {latest_v}...")
                self.progress.set(0.5)
                
                # 2. โหลดไฟล์ใหม่
                new_script = requests.get(RAW_SCRIPT_URL)
                with open("script.py", "wb") as f:
                    f.write(new_script.content)
                
                with open(LOCAL_VERSION_FILE, "w") as f:
                    f.write(latest_v)
                
                self.progress.set(1)
            
            self.label.configure(text="เสร็จสิ้น! กำลังเปิดโปรแกรม...")
            self.after(1000, self.launch_main_app)

        except Exception as e:
            self.label.configure(text="เกิดข้อผิดพลาดในการเชื่อมต่อ")
            self.after(2000, self.launch_main_app)

    def launch_main_app(self):
        self.destroy() # ปิดหน้าต่างโหลด
        if os.path.exists("script.py"):
            # รันโปรแกรมหลัก (ใช้ pythonw เพื่อไม่ให้เปิดหน้าจอ CMD)
            os.system("pythonw script.py")

if __name__ == "__main__":
    app = UpdateLauncher()
    app.mainloop()