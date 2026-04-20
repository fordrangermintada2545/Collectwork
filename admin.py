import customtkinter as ctk
from tkinter import messagebox
import requests
import random
import string
import threading

# --- ตั้งค่า Firebase ---
FIREBASE_URL = "https://konradshop-default-rtdb.asia-southeast1.firebasedatabase.app"

# --- [ COLORS ] ---
COLOR_BG = "#0A0A0A"        # ดำพื้นหลัง
COLOR_CARD = "#141414"      # เทาเข้มสำหรับกรอบ
COLOR_ACCENT = "#ED1C24"    # แดงหลัก
COLOR_TEXT = "#FFFFFF"      # ขาว (ตัวหนังสือ)

class KonradKeyGen(ctk.CTk):
    def __init__(self):
        super().__init__()
        self.title("KonradShop - Private Key Generator")
        self.geometry("500x650")
        self.configure(fg_color=COLOR_BG)

        # หัวข้อใหญ่
        ctk.CTkLabel(self, text="KEY GENERATOR", font=("Impact", 35), text_color=COLOR_ACCENT).pack(pady=30)
        
        # กรอบเมนูกลาง
        frame = ctk.CTkFrame(self, fg_color=COLOR_CARD, corner_radius=15, border_width=1, border_color="#333")
        frame.pack(pady=10, padx=40, fill="both", expand=True)

        # ส่วนเลือกจำนวน
        ctk.CTkLabel(frame, text="จำนวนคีย์ที่ต้องการสร้าง:", font=("Arial", 14, "bold"), text_color=COLOR_TEXT).pack(pady=(20, 5))
        self.count_entry = ctk.CTkEntry(frame, width=200, height=40, justify="center", 
                                        fg_color="#1E1E1E", text_color=COLOR_TEXT, border_color="#444")
        self.count_entry.insert(0, "10")
        self.count_entry.pack(pady=10)

        # ส่วนเลือกประเภท (User / Admin)
        ctk.CTkLabel(frame, text="ประเภทของสิทธิ์ (Role):", font=("Arial", 14, "bold"), text_color=COLOR_TEXT).pack(pady=(15, 5))
        self.key_type = ctk.StringVar(value="user")
        
        radio_frame = ctk.CTkFrame(frame, fg_color="transparent")
        radio_frame.pack(pady=5)
        
        ctk.CTkRadioButton(radio_frame, text="USER", variable=self.key_type, value="user", 
                           text_color=COLOR_TEXT, fg_color=COLOR_ACCENT, hover_color="#BF161D").grid(row=0, column=0, padx=10)
        ctk.CTkRadioButton(radio_frame, text="ADMIN", variable=self.key_type, value="admin", 
                           text_color=COLOR_TEXT, fg_color=COLOR_ACCENT, hover_color="#BF161D").grid(row=0, column=1, padx=10)

        # ปุ่มกด
        self.btn_gen = ctk.CTkButton(frame, text="GENERATE & UPLOAD", fg_color=COLOR_ACCENT, hover_color="#BF161D",
                                     text_color="white", font=("Arial", 15, "bold"), height=50, command=self.start_process)
        self.btn_gen.pack(pady=25, padx=30, fill="x")

        # ส่วนแสดง Log (คีย์ที่สร้างเสร็จแล้ว)
        ctk.CTkLabel(frame, text="LOGS / STATUS:", font=("Arial", 12), text_color="gray").pack()
        self.log_box = ctk.CTkTextbox(frame, fg_color="#000000", text_color="#00FF41", 
                                      font=("Consolas", 12), border_width=1, border_color="#333")
        self.log_box.pack(pady=10, padx=15, fill="both", expand=True)

    def start_process(self):
        try:
            count = int(self.count_entry.get())
            if count <= 0: raise ValueError
            self.btn_gen.configure(state="disabled")
            self.log_box.delete("1.0", "end")
            threading.Thread(target=self.generate, args=(count,), daemon=True).start()
        except: 
            messagebox.showerror("Error", "กรุณาใส่จำนวนเป็นตัวเลขที่ถูกต้อง")

    def generate(self, count):
        k_type = self.key_type.get()
        chars = string.ascii_uppercase + string.digits
        success = 0
        
        for i in range(count):
            raw = ''.join(random.choice(chars) for _ in range(16))
            key = '-'.join(raw[i:i+4] for i in range(0, len(raw), 4))
            
            try:
                # ส่งข้อมูลเข้า Firebase ในรูปแบบ Object เพื่อเก็บ Role
                res = requests.put(f"{FIREBASE_URL}/license_keys/{key}.json", 
                                   json={"role": k_type, "status": "unused"}, timeout=10)
                
                if res.status_code == 200:
                    self.log_box.insert("end", f"[{k_type.upper()}] {key} -> SUCCESS\n")
                    success += 1
                else:
                    self.log_box.insert("end", f"FAILED: {key}\n")
            except:
                self.log_box.insert("end", f"ERROR: Connection Lost\n")
            
            self.log_box.see("end")
            
        self.btn_gen.configure(state="normal")
        messagebox.showinfo("Done", f"สร้างคีย์ {k_type} สำเร็จทั้งหมด {success} คีย์")

if __name__ == "__main__":
    app = KonradKeyGen()
    app.mainloop()