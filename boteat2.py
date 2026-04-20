import customtkinter as ctk
import pyautogui
import pydirectinput
import threading
import time
import tkinter as tk
import sys
import os

def resource_path(relative_path):
    """ฟังก์ชันสำหรับหา Path ของไฟล์ในกรณีที่รวมเป็น .exe"""
    try:
        base_path = sys._MEIPASS
    except Exception:
        base_path = os.path.abspath(".")
class Overlay(tk.Toplevel):
    def __init__(self, master):
        super().__init__(master)
        self.overrideredirect(True)
        self.attributes("-topmost", True)
        self.attributes("-transparentcolor", "blue")
        self.config(bg="blue")
        self.canvas = tk.Canvas(self, bg="blue", highlightthickness=0)
        self.canvas.pack(fill="both", expand=True)
        self.withdraw()

    def update_view(self, x, y, w, h, target_x, target_y):
        self.geometry(f"{w+20}x{h+20}+{int(x-10)}+{int(y-10)}")
        self.canvas.delete("all")
        self.canvas.create_rectangle(5, 5, w+15, h+15, outline="#00FF00", width=1, dash=(1, 1))
        rel_x = target_x - x + 10
        rel_y = target_y - y + 10
        # กากบาทจิ๋ว
        self.canvas.create_line(rel_x-4, rel_y, rel_x+4, rel_y, fill="red", width=1)
        self.canvas.create_line(rel_x, rel_y-4, rel_x, rel_y+4, fill="red", width=1)
        self.deiconify()

class EatPrecisionPro(ctk.CTk):
    def __init__(self):
        super().__init__()
        self.title("EAT AUTO - Ghost Check V5")
        self.geometry("450x850")
        self.configure(fg_color="#1E1E1E")

        self.running = False
        self.total_eaten = 0
        self.offset_x, self.offset_y = 0, 0
        self.target_x, self.target_y = 0, 0
        self.memory_color = None
        
        self.overlay = Overlay(self)
        self.setup_ui()

    def setup_ui(self):
        ctk.CTkLabel(self, text="EAT AUTO - GHOST CHECK", font=("Inter", 22, "bold"), text_color="#2ED573").pack(pady=20)
        
        ## ส่วนแสดงจำนวนที่กิน (ขนาดพอดีคำ อ่านง่าย)
        self.label_count_display = ctk.CTkLabel(self, text="กินไปแล้ว : 0", font=("Inter", 20, "bold"), text_color="#FFFFFF")
        self.label_count_display.pack(pady=10)
        # ส่วนแสดงสถานะการทำงาน (Status)
        self.label_info = ctk.CTkLabel(self, text="สถานะ: พร้อมทำงาน", font=("Inter", 14), text_color="gray")
        self.label_info.pack(pady=5)
        ctk.CTkFrame(self, height=2, width=300, fg_color="#333333").pack(pady=10)

        # 1. ปุ่มค้นหา UI
        self.btn_lock_ui = ctk.CTkButton(self, text="1. ค้นหาแถบอาหาร (Lock UI)", fg_color="#3498DB", command=self.lock_initial)
        self.btn_lock_ui.pack(pady=10)

        # 2. ปรับจูนพิกัด
        frame_adj = ctk.CTkFrame(self, fg_color="transparent")
        frame_adj.pack(pady=5)
        ctk.CTkButton(frame_adj, text="▲", width=40, command=lambda: self.adjust(0, -2)).grid(row=0, column=1)
        ctk.CTkButton(frame_adj, text="◀", width=40, command=lambda: self.adjust(-2, 0)).grid(row=1, column=0)
        ctk.CTkButton(frame_adj, text="▼", width=40, command=lambda: self.adjust(0, 2)).grid(row=1, column=1)
        ctk.CTkButton(frame_adj, text="▶", width=40, command=lambda: self.adjust(2, 0)).grid(row=1, column=2)

        # 3. ปุ่มแยกสำหรับเช็คสี (ตามที่คุณขอ)
        self.btn_check_color = ctk.CTkButton(self, text="2. เช็คค่าสีตรงกากบาท (Ghost Check)", fg_color="#747D8C", command=self.manual_ghost_check)
        self.btn_check_color.pack(pady=15)

        # 4. ปุ่มจำสีอ้างอิง
        self.btn_memory = ctk.CTkButton(self, text="3. จำสีนี้เป็นสีตอนอิ่ม (Lock Color)", fg_color="#FFA502", text_color="black", command=self.remember_color)
        self.btn_memory.pack(pady=5)

        self.label_info = ctk.CTkLabel(self, text="สถานะ: พร้อมตั้งค่า", font=("Inter", 14))
        self.label_info.pack(pady=10)

        self.label_live_rgb = ctk.CTkLabel(self, text="สีที่เห็นล่าสุด: RGB(?, ?, ?)", font=("Consolas", 14))
        self.label_live_rgb.pack()

        # 5. ปุ่มเริ่มและหยุด
        self.btn_start = ctk.CTkButton(self, text="START BOT", fg_color="#28a745", font=("Inter", 18, "bold"), height=50, command=self.start_bot)
        self.btn_start.pack(pady=20)
        
        self.btn_stop = ctk.CTkButton(self, text="STOP BOT", fg_color="#e74c3c", command=self.stop_bot)
        self.btn_stop.pack()

        self.log_box = ctk.CTkTextbox(self, width=400, height=120)
        self.log_box.pack(pady=10)

    def log(self, message):
        self.log_box.insert("end", f"[{time.strftime('%H:%M:%S')}] {message}\n")
        self.log_box.see("end")

    def get_pix_ghost(self, x, y):
        """ลอจิกซ่อนเป้า -> แคป -> โชว์เป้า"""
        self.overlay.withdraw() # ซ่อนกากบาท
        self.update() # สั่งให้หน้าต่าง UI หายไปทันที
        time.sleep(0.05) # รอให้ Windows เคลียร์ภาพหน้าจอแวบหนึ่ง
        
        img = pyautogui.screenshot(region=(int(x), int(y), 1, 1))
        pix = img.getpixel((0, 0))
        
        self.overlay.deiconify() # นำกากบาทกลับมาโชว์
        return pix

    def manual_ghost_check(self):
        """ฟังก์ชันสำหรับปุ่มเช็คสีแยกต่างหาก"""
        if self.target_x == 0:
            self.log("ERROR: กรุณากดหาแถบอาหารก่อน")
            return
        
        pix = self.get_pix_ghost(self.target_x, self.target_y)
        self.label_live_rgb.configure(text=f"สีที่เห็นล่าสุด: RGB{pix}", text_color=f"#{pix[0]:02x}{pix[1]:02x}{pix[2]:02x}")
        self.log(f"GHOST CHECK: เห็นสี {pix}")

    def remember_color(self):
        """จำสีล่าสุดไว้เป็นสีอิ่ม"""
        pix = self.get_pix_ghost(self.target_x, self.target_y)
        self.memory_color = pix
        self.label_info.configure(text=f"จำสีอิ่มแล้ว: RGB{pix}", text_color="#2ED573")
        self.log(f"MEMORY LOCKED: จำสี {pix} เป็นสีหลัก")

    def lock_initial(self):
        loc = pyautogui.locateOnScreen('hunger_bar.png', confidence=0.6)
        if loc:
            self.origin_x, self.origin_y = loc.left, loc.top
            self.origin_w, self.origin_h = loc.width, loc.height
            self.target_x = loc.left + int(loc.width * 0.5)
            self.target_y = loc.top + int(loc.height * 0.5)
            self.offset_x = self.target_x - self.origin_x
            self.offset_y = self.target_y - self.origin_y
            self.overlay.update_view(self.origin_x, self.origin_y, self.origin_w, self.origin_h, self.target_x, self.target_y)
            self.log("LOCKED UI: ค้นหาเจอแล้ว กรุณาเลื่อนกากบาทแดง")
        else:
            self.log("ERROR: ไม่พบภาพอ้างอิงบนหน้าจอ")

    def adjust(self, dx, dy):
        self.target_x += dx
        self.target_y += dy
        if hasattr(self, 'origin_x'):
            self.offset_x = self.target_x - self.origin_x
            self.offset_y = self.target_y - self.origin_y
            self.overlay.update_view(self.origin_x, self.origin_y, self.origin_w, self.origin_h, self.target_x, self.target_y)

    def start_bot(self):
        if not self.memory_color:
            self.log("ERROR: กรุณาเลื่อนเป้าและกด 'จำสี' ก่อนเริ่ม")
            return
        self.running = True
        self.btn_start.configure(state="disabled")
        threading.Thread(target=self.main_logic, daemon=True).start()

    def stop_bot(self):
        self.running = False
        self.btn_start.configure(state="normal")
        self.log("SYSTEM: หยุดทำงาน")

    def main_logic(self):
        # 1. ดีเลย์เริ่มต้น 10 วินาที
        self.log("🚀 เตรียมตัวเริ่มใน 10 วินาที...")
        for i in range(10, 0, -1):
            if not self.running: return
            self.label_info.configure(text=f"ระบบจะเริ่มใน: {i} วินาที", text_color="#FF4757")
            time.sleep(1)

        self.log("✅ เริ่มทำงาน: ระบบเฝ้าดูความหิวออนไลน์แล้ว")
        
        while self.running:
            try:
                # ค้นหาตำแหน่งแถบอาหาร
                loc = pyautogui.locateOnScreen('hunger_bar.png', confidence=0.6)
                if loc:
                    cx, cy = int(loc.left + self.offset_x), int(loc.top + self.offset_y)
                    self.overlay.update_view(loc.left, loc.top, loc.width, loc.height, cx, cy)
                    
                    # เช็คสีแบบ Ghost (ซ่อนกากบาทแดงก่อนแคป)
                    pix = self.get_pix_ghost(cx, cy)
                    diff = sum(abs(a - b) for a, b in zip(pix, self.memory_color))

                    if diff <= 60: 
                        # --- กรณี: ยังอิ่ม ---
                        self.label_info.configure(text="สถานะ: ยังอิ่มอยู่ (ปกติ)", text_color="#2ED573")
                        self.log(f"🛡️ ปกติ: ค่าสีใกล้เคียงเดิม (Diff: {diff})")
                        
                        for i in range(30, 0, -1):
                            if not self.running: break
                            self.label_info.configure(text=f"ตรวจซ้ำใน: {i} วิ", text_color="#2ED573")
                            time.sleep(1)
                    
                    else:
                        # --- กรณี: หิว (สีเปลี่ยน) ---
                        self.log(f"⚠️ ตรวจพบ: สีเปลี่ยนไป {diff}! กำลังกิน...")
                        pydirectinput.press('9')
                        
                        # อัปเดตจำนวนที่กิน (โชว์แบบ "กินไปแล้ว : X")
                        self.total_eaten += 1
                        self.label_count_display.configure(text=f"กินไปแล้ว : {self.total_eaten}")
                        
                        self.log(f"🍴 สำเร็จ: กินชิ้นที่ {self.total_eaten} แล้ว ระบบทำงานปกติ")
                        
                        # รอ 30 วินาทีเพื่อให้หลอดกลับมาเต็ม
                        for i in range(30, 0, -1):
                            if not self.running: break
                            self.label_info.configure(text=f"เช็คผลหลังกินใน: {i} วิ", text_color="#FFA502")
                            time.sleep(1)
                            
                else:
                    self.label_info.configure(text="⚠️ ไม่พบแถบอาหารบนจอ", text_color="#FF4757")
                    time.sleep(2)
            except Exception as e:
                self.log(f"❌ ผิดพลาด: {str(e)}")
                time.sleep(2)

if __name__ == "__main__":
    app = EatPrecisionPro()
    app.mainloop()