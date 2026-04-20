import requests
import os
import subprocess
import sys
import threading

try:
    import customtkinter as ctk
    from tkinter import messagebox
except ImportError:
    print("กรุณาติดตั้ง: pip install customtkinter requests")
    sys.exit(1)

# --- ตั้งค่าระบบ (ส่วนความลับ) ---
USER_REPO = "fordrangermintada2545/betatest"
RAW_BASE_URL = f"https://raw.githubusercontent.com/{USER_REPO}/main"
GITHUB_TOKEN = "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" 

# สร้าง Header สำหรับดึงข้อมูลจาก Private Repo
HEADERS = {
    "Authorization": f"token {GITHUB_TOKEN}",
    "Accept": "application/vnd.github.v3.raw"
}

# 1. กำหนดโฟลเดอร์เก็บไฟล์ใน AppData
SAVE_PATH = os.path.join(os.environ['APPDATA'], 'KonradShop')
if not os.path.exists(SAVE_PATH):
    os.makedirs(SAVE_PATH)

FILES_LIST = {
    "MAIN": "Konradshop.exe",
    "EAT": "EAT.exe",
    "FPS": "Bootfpsv2.exe",
    "VER": "version.txt"
}

LOCAL_VERSION_FILE = os.path.join(SAVE_PATH, "current_version.txt")

class KonradLauncher(ctk.CTk):
    def __init__(self):
        super().__init__()
        self.title("KonradShop Launcher")
        
        w, h = 450, 280
        sx, sy = self.winfo_screenwidth(), self.winfo_screenheight()
        self.geometry(f"{w}x{h}+{int(sx/2-w/2)}+{int(sy/2-h/2)}")
        self.resizable(False, False)

        self.label = ctk.CTkLabel(self, text="KonradShop System", font=("Kanit", 22, "bold"))
        self.label.pack(pady=20)

        # แก้ไขข้อความเริ่มต้นให้ดูเป็นทางการขึ้น
        self.status_label = ctk.CTkLabel(self, text="กำลังตรวจสอบสถานะการเชื่อมต่อ...", font=("Kanit", 14))
        self.status_label.pack(pady=5)

        self.progress = ctk.CTkProgressBar(self, width=350)
        self.progress.pack(pady=20)
        self.progress.set(0)

        threading.Thread(target=self.start_process, daemon=True).start()

    def start_process(self):
        try:
            current_v = "0"
            if os.path.exists(LOCAL_VERSION_FILE):
                with open(LOCAL_VERSION_FILE, "r") as f:
                    current_v = f.read().strip()

            v_res = requests.get(f"{RAW_BASE_URL}/{FILES_LIST['VER']}", headers=HEADERS, timeout=10)
            v_res.raise_for_status()
            latest_v = v_res.text.strip()

            main_exe_path = os.path.join(SAVE_PATH, FILES_LIST['MAIN'])

            if latest_v > current_v or not os.path.exists(main_exe_path):
                # เปลี่ยนข้อความเป็นแบบรวมๆ ไม่ระบุชื่อไฟล์
                self.status_label.configure(text="กำลังเตรียมการอัปเดตระบบ...")
                
                to_download = [f for f in FILES_LIST.values() if f != "version.txt"]
                
                for i, filename in enumerate(to_download):
                    # แก้ไขตรงนี้: ไม่แสดงชื่อไฟล์ แต่แสดงเปอร์เซ็นต์หรือสถานะรวม
                    self.status_label.configure(text=f"กำลังซิงค์ข้อมูลระบบ... ({int((i/len(to_download))*100)}%)")
                    
                    full_path = os.path.join(SAVE_PATH, filename)
                    
                    with requests.get(f"{RAW_BASE_URL}/{filename}", headers=HEADERS, stream=True, timeout=60) as r:
                        r.raise_for_status()
                        with open(full_path, "wb") as f:
                            for chunk in r.iter_content(chunk_size=8192):
                                if chunk:
                                    f.write(chunk)
                    
                    if os.name == 'nt':
                        subprocess.run(['attrib', '+h', full_path], check=False)
                        
                    self.progress.set((i + 1) / len(to_download))

                with open(LOCAL_VERSION_FILE, "w") as f:
                    f.write(latest_v)
                
                self.status_label.configure(text="จัดการข้อมูลเสร็จสิ้น!")
            else:
                self.status_label.configure(text="ระบบพร้อมทำงาน")
                self.progress.set(1)

            self.after(1500, self.launch_main_app)

        except Exception as e:
            # พิมพ์ Error ไว้ดูใน Console ฝั่งเราคนเดียว
            print(f"Error: {e}")
            self.status_label.configure(text="ตรวจสอบล้มเหลว กำลังเข้าสู่โหมดออฟไลน์...")
            self.after(2000, self.launch_main_app)

    def launch_main_app(self):
        self.destroy()
        target = os.path.join(SAVE_PATH, FILES_LIST['MAIN'])
        
        if os.path.exists(target):
            try:
                subprocess.Popen([target, "KonradSecureOpen777"], cwd=SAVE_PATH, shell=True)
            except Exception as e:
                messagebox.showerror("System Error", "ไม่สามารถเริ่มระบบได้ กรุณาลองใหม่อีกครั้ง")
        else:
            messagebox.showerror("Access Error", "ไม่พบข้อมูลไฟล์สำคัญในเครื่อง")

if __name__ == "__main__":
    app = KonradLauncher()
    app.mainloop()