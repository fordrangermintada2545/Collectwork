import customtkinter as ctk
import pydirectinput
import threading
import time
from datetime import datetime
from tkinter import messagebox
import sys

# ตั้งค่าธีม
ctk.set_appearance_mode("dark")
ctk.set_default_color_theme("blue")

# --- วางไว้ตรงนี้ครับ (ต้องเป็นรหัสเดียวกับไฟล์ Login) ---
SECRET_CODE = "Konradshop324152" 

def check_access():
    # ตรวจสอบว่ามี argument ส่งมาไหม และตรงกับรหัสลับหรือไม่
    if len(sys.argv) < 2 or sys.argv[1] != SECRET_CODE:
       messagebox.showerror("Access Denied", "กรุณาเปิดโปรแกรมผ่าน Konradshop.exe เท่านั้น")
       sys.exit() 

# เรียกใช้ก่อนเริ่ม GUI
if __name__ == "__main__":
   check_access()
    # ... โค้ดโปรแกรม EAT ของคุณเดิมที่มีอยู่ ...
    # เช่น app = ctk.CTk() ...
    
class EATProfessional(ctk.CTk):
    def __init__(self):
        super().__init__()

        self.title("EAT AUTO BOT v2.0")
        self.geometry("400x600")
        self.configure(fg_color="#1E1E1E")
        self.resizable(False, False)

        self.running = False
        self.press_count = 0
        self.time_interval = ctk.IntVar(value=3600)

        self.setup_ui()

    def setup_ui(self):
        # ส่วนหัว
        header_frame = ctk.CTkFrame(self, fg_color="transparent")
        header_frame.pack(pady=(30, 20))
        ctk.CTkLabel(header_frame, text="EAT", font=("Inter", 40, "bold"), text_color="#2ED573").pack(side="left")
        ctk.CTkLabel(header_frame, text=" BOT", font=("Inter", 40, "bold"), text_color="#FFFFFF").pack(side="left")
        
        # การ์ดกลาง
        card_frame = ctk.CTkFrame(self, fg_color="#2D2D2D", corner_radius=20, border_width=1, border_color="#3D3D3D")
        card_frame.pack(padx=30, pady=20, fill="both", expand=True)

        ctk.CTkLabel(card_frame, text="ใส่ปุ่มที่ต้องการ", font=("Inter", 11, "bold"), text_color="gray").pack(pady=(20, 5))
        self.entry_key = ctk.CTkEntry(card_frame, width=80, height=40, font=("Inter", 18, "bold"), 
                                     justify="center", fg_color="#1E1E1E", border_color="#444")
        self.entry_key.insert(0, "9")
        self.entry_key.pack()

        ctk.CTkLabel(card_frame, text="TIME INTERVAL", font=("Inter", 11, "bold"), text_color="gray").pack(pady=(20, 5))
        radio_frame = ctk.CTkFrame(card_frame, fg_color="transparent")
        radio_frame.pack()
        ctk.CTkRadioButton(radio_frame, text="30m", variable=self.time_interval, value=1800).pack(side="left", padx=10)
        ctk.CTkRadioButton(radio_frame, text="1h", variable=self.time_interval, value=3600).pack(side="left", padx=10)
        ctk.CTkRadioButton(radio_frame, text="Test", variable=self.time_interval, value=10).pack(side="left", padx=10)

        self.label_timer = ctk.CTkLabel(card_frame, text="00:00", font=("Inter", 50, "bold"), text_color="#FFA502")
        self.label_timer.pack(pady=(25, 5))
        
        self.label_counter = ctk.CTkLabel(card_frame, text="กินไปแล้ว: 0", font=("Inter", 12), text_color="gray")
        self.label_counter.pack(pady=(0, 20))

        # ปุ่มกด
        self.btn_start = ctk.CTkButton(self, text="START", font=("Inter", 14, "bold"), height=50, 
                                      fg_color="#2ED573", hover_color="#26AF5F", text_color="black",
                                      corner_radius=15, command=self.start_engine)
        self.btn_start.pack(padx=40, pady=(10, 5), fill="x")

        self.btn_stop = ctk.CTkButton(self, text="STOP", font=("Inter", 14, "bold"), height=40, 
                                     fg_color="transparent", border_width=2, border_color="#FF4757", 
                                     text_color="#FF4757", hover_color="#3D1C1C",
                                     corner_radius=15, command=self.stop_engine)
        self.btn_stop.pack(padx=40, pady=(5, 30), fill="x")

    def start_engine(self):
        if not self.running:
            self.focus()
            self.running = True
            self.btn_start.configure(state="disabled", text="RUNNING...")
            threading.Thread(target=self.logic_loop, daemon=True).start()

    def stop_engine(self):
        self.running = False
        self.btn_start.configure(state="normal", text="START")
        self.label_timer.configure(text="IDLE", text_color="gray")

    def logic_loop(self):
        while self.running:
            # 1. สั่งกดปุ่มก่อน
            key = self.entry_key.get().lower()
            pydirectinput.keyDown(key)
            time.sleep(0.2)
            pydirectinput.keyUp(key)

            # 2. เริ่มนับถอยหลัง (Timer)
            wait = self.time_interval.get()
            for i in range(wait, 0, -1):
                if not self.running: break
                mins, secs = divmod(i, 60)
                self.label_timer.configure(text=f"{mins:02d}:{secs:02d}", text_color="#FFA502")
                time.sleep(1)
            
            if not self.running: break

            # 3. พอนับถอยหลังเสร็จ (ครบกำหนดเวลา) ค่อยบวกเลขตัวนับ
            self.press_count += 1
            self.label_counter.configure(text=f"กินไปแล้ว: {self.press_count}")

if __name__ == "__main__":
    app = EATProfessional()
    app.mainloop()