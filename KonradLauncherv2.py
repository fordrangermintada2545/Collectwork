import customtkinter as ctk
from tkinter import messagebox
import requests
import os
import uuid
import subprocess
import sys
from PIL import Image, ImageEnhance

# --- [ CONFIG ] ---
FIREBASE_URL = "https://konradshop-default-rtdb.asia-southeast1.firebasedatabase.app"
USER_REPO = "fordrangermintada2545/betatest"
RAW_BASE_URL = f"https://raw.githubusercontent.com/{USER_REPO}/main"
VERSION_FILE = "version.txt"
SECRET_CODE = "Konradshop324152" 

# Path สำหรับเก็บไฟล์ใน AppData (ตรงกับ Launcher ตัวโหลด)
SAVE_PATH = os.path.join(os.environ['APPDATA'], 'KonradShop')
LOCAL_VERSION_PATH = os.path.join(SAVE_PATH, "current_version.txt")

# รหัสลับสำหรับเปิดไฟล์ลูก
LAUNCHER_SECRET = "KonradSecureOpen777"

COLOR_BG = "#0A0A0A"
COLOR_CARD = "#141414"
COLOR_ACCENT = "#ED1C24"
COLOR_TEXT = "#FFFFFF"
COLOR_INPUT = "#1E1E1E"

def get_hwid():
    try:
        # ใช้คำสั่ง PowerShell เพื่อดึง UUID ที่แม่นยำกว่าเดิม
        cmd = 'powershell -command "(Get-CimInstance -Class Win32_ComputerSystemProduct).UUID"'
        return subprocess.check_output(cmd, shell=True).decode().strip()
    except:
        return f"SYS-{uuid.getnode()}"

class KonradLauncher(ctk.CTk):
    def __init__(self):
        super().__init__()
        self.title("KonradShop - Premium Edition")
        self.geometry("800x500")
        self.resizable(False, False)
        self.configure(fg_color=COLOR_BG)
        self.current_user = None
        self.user_role = "user" 
        self.img_cache = {} 
        
        # ตรวจสอบว่ามีโฟลเดอร์ AppData หรือยัง
        if not os.path.exists(SAVE_PATH):
            os.makedirs(SAVE_PATH)
            
        self.show_login_page()

    # ==========================================
    #      CORE LOGIC: UPDATE & LAUNCH
    # ==========================================
    def update_local_version_file(self):
        """ดึงเวอร์ชันล่าสุดจาก GitHub มาเขียนลง AppData เพื่อให้โปรแกรมลูกเช็คผ่าน"""
        try:
            res = requests.get(f"{RAW_BASE_URL}/{VERSION_FILE}", timeout=5)
            if res.status_code == 200:
                with open(LOCAL_VERSION_PATH, "w") as f:
                    f.write(res.text.strip())
                return True
        except:
            return False

    def launch_software(self, cmd_key):
        """ ฟังก์ชันสำหรับเปิดไฟล์บอทโดยส่งรหัสลับไปด้วย """
        file_map = {
            "BOT 1": "EAT.exe",
            "BOT 2": "Bootfpsv2.exe"
        }
        
        filename = file_map.get(cmd_key)
        if not filename:
            messagebox.showinfo("System", "Coming Soon...")
            return

        # ชี้ไปที่ไฟล์ใน AppData ตามที่ตัวดาวน์โหลดโหลดไว้ให้
        exe_path = os.path.join(SAVE_PATH, filename)

        try:
            if os.path.exists(exe_path):
                # --- [ จุดสำคัญ ] ---
                # รันไฟล์พร้อมส่ง SECRET_CODE ไปเป็น Argument ตัวที่ 1 (sys.argv[1])
                # เพื่อให้ฟังก์ชัน check_security() ใน EAT.exe ยอมให้ทำงาน
                subprocess.Popen([exe_path, SECRET_CODE], cwd=SAVE_PATH, shell=True)
            else:
                messagebox.showerror("Error", f"ไม่พบไฟล์ {filename} ในระบบ\nกรุณารันผ่านตัว Launcher เพื่อดาวน์โหลดไฟล์")
                
        except Exception as e:
            messagebox.showerror("Launch Error", f"เกิดข้อผิดพลาด: {str(e)}")

    # ==========================================
    #            PAGE: LOGIN & REGISTER
    # ==========================================
    def show_login_page(self):
        self.geometry("800x500")
        for widget in self.winfo_children(): widget.destroy()
        self.grid_columnconfigure(0, weight=1)
        self.grid_columnconfigure(1, weight=1)
        self.grid_rowconfigure(0, weight=1)
        
        left_frame = ctk.CTkFrame(self, fg_color=COLOR_CARD, corner_radius=0)
        left_frame.grid(row=0, column=0, sticky="nsew")
        ctk.CTkLabel(left_frame, text="KONRAD SHOP", font=("Arial", 16, "bold"), text_color=COLOR_ACCENT).pack(anchor="nw", pady=20, padx=20)
        
        l_content = ctk.CTkFrame(left_frame, fg_color="transparent")
        l_content.pack(expand=True)
        ctk.CTkLabel(l_content, text="WELCOME BACK", font=("Impact", 45), text_color=COLOR_TEXT).pack()

        right_frame = ctk.CTkFrame(self, fg_color=COLOR_BG, corner_radius=0)
        right_frame.grid(row=0, column=1, sticky="nsew")
        r_content = ctk.CTkFrame(right_frame, fg_color="transparent")
        r_content.pack(expand=True)
        
        ctk.CTkLabel(r_content, text="AUTHENTICATION", font=("Arial", 22, "bold"), text_color=COLOR_ACCENT).pack(pady=20)
        self.user_ent = ctk.CTkEntry(r_content, width=300, height=45, placeholder_text="Username", fg_color=COLOR_INPUT, text_color="white")
        self.user_ent.pack(pady=10)
        self.pass_ent = ctk.CTkEntry(r_content, width=300, height=45, placeholder_text="Password", show="*", fg_color=COLOR_INPUT, text_color="white")
        self.pass_ent.pack(pady=10)

        ctk.CTkButton(r_content, text="LOGIN", font=("Arial", 16, "bold"), fg_color=COLOR_ACCENT, hover_color="#BF161D", width=300, height=50, corner_radius=25, command=self.login_logic).pack(pady=20)
        ctk.CTkButton(r_content, text="Create New Account", text_color="#555", fg_color="transparent", command=self.show_register_page).pack()

    def show_register_page(self):
        for widget in self.winfo_children(): widget.destroy()
        frame = ctk.CTkFrame(self, fg_color=COLOR_BG, corner_radius=0)
        frame.pack(fill="both", expand=True)
        content = ctk.CTkFrame(frame, fg_color="transparent")
        content.pack(expand=True)
        
        ctk.CTkLabel(content, text="CREATE ACCOUNT", font=("Arial", 28, "bold"), text_color=COLOR_ACCENT).pack(pady=(0, 20))
        self.reg_u = ctk.CTkEntry(content, width=320, height=45, placeholder_text="Enter Username", fg_color=COLOR_INPUT, text_color="white")
        self.reg_u.pack(pady=5)
        self.reg_p = ctk.CTkEntry(content, width=320, height=45, placeholder_text="Enter Password", show="*", fg_color=COLOR_INPUT, text_color="white")
        self.reg_p.pack(pady=5)
        self.reg_k = ctk.CTkEntry(content, width=320, height=45, placeholder_text="License Key", fg_color=COLOR_INPUT, border_color=COLOR_ACCENT, text_color="white")
        self.reg_k.pack(pady=15)
        
        ctk.CTkButton(content, text="REGISTER NOW", font=("Arial", 16, "bold"), fg_color=COLOR_ACCENT, width=320, height=50, corner_radius=10, command=self.register_logic).pack(pady=10)
        ctk.CTkButton(content, text="Back to Login", fg_color="transparent", text_color="gray", command=self.show_login_page).pack()

    # ==========================================
    #            DASHBOARD: MAIN MENU
    # ==========================================
    def show_main_menu(self):
        self.geometry("1000x700") 
        for widget in self.winfo_children(): widget.destroy()
        
        self.grid_columnconfigure(0, weight=0)
        self.grid_columnconfigure(1, weight=1)
        self.grid_rowconfigure(0, weight=1)

        sidebar = ctk.CTkFrame(self, fg_color=COLOR_CARD, width=220, corner_radius=0)
        sidebar.grid(row=0, column=0, sticky="nsew")
        sidebar.grid_propagate(False)

        ctk.CTkLabel(sidebar, text="KONRAD SHOP", font=("Impact", 24), text_color=COLOR_ACCENT).pack(pady=40)
        
        user_info = ctk.CTkFrame(sidebar, fg_color="#1a1a1a", corner_radius=10)
        user_info.pack(pady=10, padx=15, fill="x")
        ctk.CTkLabel(user_info, text=f"{self.current_user}", font=("Arial", 14, "bold"), text_color=COLOR_TEXT).pack(pady=(10, 2))
        ctk.CTkLabel(user_info, text=f"{self.user_role.upper()}", font=("Arial", 10), text_color="gray").pack(pady=(0, 10))

        if self.user_role == "admin":
            ctk.CTkButton(sidebar, text="ONLINE MONITOR", fg_color="#222", hover_color=COLOR_ACCENT, command=self.show_online_monitor).pack(fill="x", padx=15, pady=20)

        ctk.CTkButton(sidebar, text="LOGOUT", fg_color="transparent", border_width=1, border_color="#333", command=self.logout).pack(side="bottom", pady=20, padx=20, fill="x")

        self.content_area = ctk.CTkFrame(self, fg_color=COLOR_BG, corner_radius=0)
        self.content_area.grid(row=0, column=1, sticky="nsew")
        self.show_dashboard_home()

    def show_dashboard_home(self):
        try:
            if not self.winfo_exists(): return
            for widget in self.content_area.winfo_children(): widget.destroy()
        except: return

        ctk.CTkLabel(self.content_area, text="SELECT SOFTWARE FUNCTION", font=("Impact", 32), text_color=COLOR_TEXT).pack(pady=(40, 20))

        bots_data = [
            {"name": "EAT SYSTEM (AFK)", "cmd": "BOT 1", "img_path": "police1.png"},
            {"name": "BOOT FPS V2", "cmd": "BOT 2", "img_path": "FiveM-Symbol.png"},
            {"name": "COMING SOON", "cmd": "BOT 3", "img_path": "bot3.png"}
        ]

        grid_container = ctk.CTkFrame(self.content_area, fg_color="transparent")
        grid_container.pack(expand=True, fill="both", padx=30, pady=20)
        grid_container.grid_columnconfigure((0,1,2), weight=1)

        for i, bot in enumerate(bots_data):
            card = ctk.CTkFrame(grid_container, fg_color="#000000", width=250, height=480, corner_radius=20, border_width=1, border_color="#222")
            card.grid(row=0, column=i, padx=15, pady=10, sticky="ns")
            card.grid_propagate(False)

            img_container = ctk.CTkLabel(card, text="", corner_radius=20)
            img_container.place(relx=0, rely=0, relwidth=1, relheight=1)

            try:
                orig_img = Image.open(bot["img_path"]).convert("RGBA")
                orig_img = orig_img.resize((250, 480), Image.Resampling.LANCZOS)
                
                bright_ctk = ctk.CTkImage(light_image=orig_img, dark_image=orig_img, size=(250, 480))
                enhancer = ImageEnhance.Brightness(orig_img)
                dark_img = enhancer.enhance(0.3) 
                dark_ctk = ctk.CTkImage(light_image=dark_img, dark_image=dark_img, size=(250, 480))
                
                img_container.configure(image=dark_ctk)
                
                def on_enter(e, c=img_container, b=bright_ctk): c.configure(image=b)
                def on_leave(e, c=img_container, d=dark_ctk): c.configure(image=d)
                
                img_container.bind("<Enter>", on_enter)
                img_container.bind("<Leave>", on_leave)
                
                self.img_cache[f"b_{i}"] = bright_ctk
                self.img_cache[f"d_{i}"] = dark_ctk
            except:
                img_container.configure(text="IMAGE NOT FOUND", text_color="#333")

            lbl_name = ctk.CTkLabel(card, text=bot["name"], font=("Impact", 22), text_color=COLOR_TEXT, fg_color="transparent")
            lbl_name.place(relx=0.5, rely=0.75, anchor="center")
            
            btn_launch = ctk.CTkButton(card, text="LAUNCH", fg_color=COLOR_ACCENT, hover_color="#BF161D",
                                      font=("Arial", 16, "bold"), corner_radius=10, height=45,
                                      command=lambda n=bot["cmd"]: self.launch_software(n))
            btn_launch.place(relx=0.5, rely=0.88, relwidth=0.8, anchor="center")

            lbl_name.bind("<Enter>", lambda e, c=img_container, b=self.img_cache.get(f"b_{i}"): c.configure(image=b) if b else None)
            btn_launch.bind("<Enter>", lambda e, c=img_container, b=self.img_cache.get(f"b_{i}"): c.configure(image=b) if b else None)

    # ==========================================
    #            LOGIC & FIREBASE
    # ==========================================
    def login_logic(self):
        u, p, h = self.user_ent.get(), self.pass_ent.get(), get_hwid()
        try:
            res = requests.get(f"{FIREBASE_URL}/users/{u}.json", timeout=5)
            data = res.json()
            if data and data['password'] == p:
                if data['hwid'] == h:
                    self.current_user = u
                    self.user_role = data.get('role', 'user')
                    requests.put(f"{FIREBASE_URL}/online_users/{u}.json", json={"hwid": h}, timeout=5)
                    self.show_main_menu()
                else: messagebox.showerror("Error", "HWID Mismatch!")
            else: messagebox.showerror("Error", "Invalid Credentials")
        except Exception as e: 
            messagebox.showerror("Server Error", f"Cannot connect: {e}")

    def register_logic(self):
        u, p, k = self.reg_u.get().strip(), self.reg_p.get().strip(), self.reg_k.get().strip()
        h = get_hwid()
        if not u or not p or not k: return
        try:
            key_data = requests.get(f"{FIREBASE_URL}/license_keys/{k}.json", timeout=5).json()
            if key_data and key_data.get("status") == "unused":
                role = key_data.get("role", "user")
                payload = {"password": p, "hwid": h, "role": role}
                requests.put(f"{FIREBASE_URL}/users/{u}.json", json=payload, timeout=5)
                requests.delete(f"{FIREBASE_URL}/license_keys/{k}.json", timeout=5)
                messagebox.showinfo("Success", f"Activated as {role.upper()}")
                self.show_login_page()
            else: messagebox.showerror("Error", "Invalid Key")
        except: messagebox.showerror("Error", "Server Error")

    def show_online_monitor(self):
        for widget in self.content_area.winfo_children(): widget.destroy()
        ctk.CTkLabel(self.content_area, text="LIVE MONITORING", font=("Arial", 24, "bold"), text_color=COLOR_ACCENT).pack(pady=20)
        scroll = ctk.CTkScrollableFrame(self.content_area, fg_color=COLOR_CARD, width=600, height=450)
        scroll.pack(pady=10, padx=20)
        try:
            data = requests.get(f"{FIREBASE_URL}/online_users.json", timeout=5).json()
            if data:
                for user, info in data.items():
                    row = ctk.CTkFrame(scroll, fg_color="#1E1E1E")
                    row.pack(fill="x", pady=2, padx=5)
                    ctk.CTkLabel(row, text=f"● {user}", text_color="#00FF41", font=("Arial", 14, "bold")).pack(side="left", padx=15)
                    ctk.CTkLabel(row, text=f"HWID: {info.get('hwid')}", text_color="gray").pack(side="right", padx=15)
        except: pass
        ctk.CTkButton(self.content_area, text="Back", command=self.show_dashboard_home).pack(pady=10)

    def logout(self):
        try: requests.delete(f"{FIREBASE_URL}/online_users/{self.current_user}.json", timeout=5)
        except: pass
        self.show_login_page()

if __name__ == "__main__":
    app = KonradLauncher()
    app.mainloop()