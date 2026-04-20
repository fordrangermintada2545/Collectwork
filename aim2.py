import customtkinter as ctk
import pyautogui
import pydirectinput
import threading
import time
import tkinter as tk

# ระบบเป้าเล็งกลางจอ (Crosshair)
class CrosshairOverlay(tk.Toplevel):
    def __init__(self, master):
        super().__init__(master)
        self.overrideredirect(True)
        self.attributes("-topmost", True)
        self.attributes("-transparentcolor", "blue")
        self.config(bg="blue")
        self.canvas = tk.Canvas(self, bg="blue", highlightthickness=0)
        self.canvas.pack(fill="both", expand=True)
        self.withdraw()

    def show_crosshair(self, x, y, size=8):
        # ปรับขนาดเป้าให้เล็กและคมชัด
        self.geometry(f"{size*2}x{size*2}+{x-size}+{y-size}")
        self.canvas.delete("all")
        # กากบาทสีแดงจิ๋ว
        self.canvas.create_line(size-4, size, size+4, size, fill="red", width=1)
        self.canvas.create_line(size, size-4, size, size+4, fill="red", width=1)
        self.deiconify()

class TriggerBotPro(ctk.CTk):
    def __init__(self):
        super().__init__()
        self.title("TRIGGER BOT - AUTO SHOOT")
        self.geometry("400x600")
        self.configure(fg_color="#1E1E1E")

        self.running = False
        self.memory_color = None
        self.shot_count = 0
        
        # พิกัดกึ่งกลางหน้าจอ
        sw, sh = pyautogui.size()
        self.mid_x, self.mid_y = sw // 2, sh // 2
        
        self.overlay = CrosshairOverlay(self)
        self.setup_ui()

    def setup_ui(self):
        ctk.CTkLabel(self, text="TRIGGER BOT SYSTEM", font=("Inter", 20, "bold"), text_color="#FF4757").pack(pady=20)
        
        # แสดงสถิติการยิง
        self.label_shots = ctk.CTkLabel(self, text="ยิงไปแล้ว : 0 นัด", font=("Inter", 18, "bold"), text_color="#2ED573")
        self.label_shots.pack(pady=10)

        # ปุ่มจำสีเป้าหมาย
        self.btn_lock = ctk.CTkButton(self, text="LOCK TARGET COLOR", fg_color="#3498DB", height=40, command=self.lock_target)
        self.btn_lock.pack(pady=10)
        
        self.label_target_info = ctk.CTkLabel(self, text="เป้าหมาย: ยังไม่ได้ล็อคสี", text_color="gray")
        self.label_target_info.pack()

        # ปุ่มเริ่ม/หยุด
        self.btn_start = ctk.CTkButton(self, text="START AUTO SHOOT", fg_color="#28a745", height=50, font=("Inter", 16, "bold"), command=self.start_bot)
        self.btn_start.pack(pady=20)
        
        self.btn_stop = ctk.CTkButton(self, text="STOP SYSTEM", fg_color="#C0392B", command=self.stop_bot)
        self.btn_stop.pack()

        self.log_box = ctk.CTkTextbox(self, width=350, height=150)
        self.log_box.pack(pady=20)
        
        # แสดงเป้ามาร์คกลางจอทันที
        self.overlay.show_crosshair(self.mid_x, self.mid_y)

    def log(self, msg):
        self.log_box.insert("end", f"[{time.strftime('%H:%M:%S')}] {msg}\n")
        self.log_box.see("end")

    def get_pixel_ghost(self):
        self.overlay.withdraw()
        self.update()
        time.sleep(0.05) # เพิ่มเป็น 0.05 เพื่อให้ชัวร์ว่าเป้าหายไปแล้ว
        img = pyautogui.screenshot(region=(self.mid_x, self.mid_y, 1, 1))
        pix = img.getpixel((0, 0))
        self.overlay.deiconify()
        return pix

    def lock_target(self):
        self.memory_color = self.get_pixel_ghost()
        self.label_target_info.configure(text=f"ล็อคสีเป้าหมาย: RGB{self.memory_color}", text_color="#2ED573")
        self.log(f"LOCKED: จำสี {self.memory_color} เป็นสีที่จะยิง")

    def start_bot(self):
        if not self.memory_color:
            self.log("ERROR: กรุณาล็อคสีก่อนเริ่ม!")
            return
        self.running = True
        self.btn_start.configure(state="disabled")
        threading.Thread(target=self.trigger_logic, daemon=True).start()

    def stop_bot(self):
        self.running = False
        self.btn_start.configure(state="normal")
        self.log("STOPPED: หยุดระบบ")

    def trigger_logic(self):
        while self.running:
            current_pix = self.get_pixel_ghost()
            diff = sum(abs(a - b) for a, b in zip(current_pix, self.memory_color))

            # ลองปรับเลข 50 เป็น 100 ถ้ายังไม่ยิง
            if diff <= 100: 
                # เปลี่ยนมาใช้ pydirectinput.mouseDown() และ mouseUp() 
                # บางเกมไม่รับ .click() ตรงๆ
                pydirectinput.mouseDown(button='left')
                time.sleep(0.05)
                pydirectinput.mouseUp(button='left')
                
                self.shot_count += 1
                self.label_shots.configure(text=f"ยิงไปแล้ว : {self.shot_count} นัด")
                time.sleep(0.2) 
            time.sleep(0.01)
if __name__ == "__main__":
    app = TriggerBotPro()
    app.mainloop()