import customtkinter as ctk
from tkinter import messagebox
import requests
import os
import uuid

# --- [ CONFIG ] ---
USER_REPO = "fordrangermintada2545/betatest"
FIREBASE_URL = "https://konradshop-default-rtdb.asia-southeast1.firebasedatabase.app"
SAVE_PATH = os.path.join(os.environ['APPDATA'], 'KonradShop')
if not os.path.exists(SAVE_PATH): os.makedirs(SAVE_PATH)

# --- [ COLORS ] ---
COLOR_BG = "#0A0A0A"        # ดำพื้นหลัง
COLOR_CARD = "#141414"      # เทาเข้มสำหรับ Card/Frame
COLOR_ACCENT = "#ED1C24"    # แดงหลัก
COLOR_ACCENT_HOVER = "#BF161D" # แดงตอนเอาเมาส์วาง
COLOR_TEXT = "#FFFFFF"      # ขาว
COLOR_INPUT = "#1E1E1E"     # สีช่องกรอก

def get_hwid():
    try:
        mac = ':'.join(['{:02x}'.format((uuid.getnode() >> ele) & 0xff)
                        for ele in range(0, 8*6, 8)][::-1])
        return f"HWID-{mac.upper()}"
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
        self.show_login_page()

    def show_login_page(self):
        for widget in self.winfo_children(): widget.destroy()
        
        self.grid_columnconfigure(0, weight=1)
        self.grid_columnconfigure(1, weight=1)
        self.grid_rowconfigure(0, weight=1)

        # ฝั่งซ้าย (Branding)
        left_frame = ctk.CTkFrame(self, fg_color=COLOR_CARD, corner_radius=0)
        left_frame.grid(row=0, column=0, sticky="nsew")
        
        ctk.CTkLabel(left_frame, text="KONRAD SHOP", font=("Orbitron", 18, "bold"), text_color=COLOR_ACCENT).pack(anchor="nw", pady=20, padx=20)
        
        l_content = ctk.CTkFrame(left_frame, fg_color="transparent")
        l_content.pack(expand=True)
        ctk.CTkLabel(l_content, text="WELCOME\nBACK", font=("Impact", 50), text_color=COLOR_TEXT, justify="center").pack()
        ctk.CTkLabel(l_content, text="PREMIUM ACCESS ONLY", font=("Arial", 12), text_color="gray").pack(pady=5)

        # ฝั่งขวา (Login Form)
        right_frame = ctk.CTkFrame(self, fg_color=COLOR_BG, corner_radius=0)
        right_frame.grid(row=0, column=1, sticky="nsew")
        
        r_content = ctk.CTkFrame(right_frame, fg_color="transparent")
        r_content.pack(expand=True)
        
        ctk.CTkLabel(r_content, text="AUTHENTICATION", font=("Arial", 22, "bold"), text_color=COLOR_TEXT).pack(pady=(0, 25))
        
        self.user_ent = ctk.CTkEntry(r_content, width=300, height=45, placeholder_text="Username", 
                                     fg_color=COLOR_INPUT, border_color=COLOR_CARD, text_color="white")
        self.user_ent.pack(pady=10)
        
        self.pass_ent = ctk.CTkEntry(r_content, width=300, height=45, placeholder_text="Password", show="*", 
                                     fg_color=COLOR_INPUT, border_color=COLOR_CARD, text_color="white")
        self.pass_ent.pack(pady=10)

        ctk.CTkButton(r_content, text="LOGIN", font=("Arial", 16, "bold"), 
                      fg_color=COLOR_ACCENT, hover_color=COLOR_ACCENT_HOVER, 
                      width=300, height=50, corner_radius=8, command=self.login_logic).pack(pady=20)
        
        ctk.CTkButton(r_content, text="Create New Account", text_color=COLOR_ACCENT, 
                      fg_color="transparent", hover_color=COLOR_CARD, command=self.show_register_page).pack()

    def show_register_page(self):
        for widget in self.winfo_children(): widget.destroy()
        frame = ctk.CTkFrame(self, fg_color=COLOR_BG, corner_radius=0)
        frame.pack(fill="both", expand=True)
        
        content = ctk.CTkFrame(frame, fg_color="transparent")
        content.pack(expand=True)
        
        ctk.CTkLabel(content, text="REGISTER", font=("Arial", 30, "bold"), text_color=COLOR_ACCENT).pack(pady=20)
        
        self.reg_u = ctk.CTkEntry(content, width=320, height=45, placeholder_text="New Username", fg_color=COLOR_INPUT)
        self.reg_u.pack(pady=5)
        self.reg_p = ctk.CTkEntry(content, width=320, height=45, placeholder_text="New Password", show="*", fg_color=COLOR_INPUT)
        self.reg_p.pack(pady=5)
        self.reg_k = ctk.CTkEntry(content, width=320, height=45, placeholder_text="License Key", border_color=COLOR_ACCENT, fg_color=COLOR_INPUT)
        self.reg_k.pack(pady=15)
        
        ctk.CTkButton(content, text="ACTIVATE LICENSE", fg_color=COLOR_ACCENT, hover_color=COLOR_ACCENT_HOVER, 
                      width=320, height=50, corner_radius=8, font=("Arial", 14, "bold"), command=self.register_logic).pack(pady=10)
        
        ctk.CTkButton(content, text="Cancel", fg_color="transparent", text_color="gray", command=self.show_login_page).pack()

    def show_main_menu(self):
        self.geometry("950x600")
        for widget in self.winfo_children(): widget.destroy()
        
        self.grid_columnconfigure(0, weight=0)
        self.grid_columnconfigure(1, weight=1)
        self.grid_rowconfigure(0, weight=1)

        # Sidebar
        sidebar = ctk.CTkFrame(self, fg_color=COLOR_CARD, width=260, corner_radius=0)
        sidebar.grid(row=0, column=0, sticky="nsew")
        sidebar.grid_propagate(False)

        ctk.CTkLabel(sidebar, text="KONRAD", font=("Orbitron", 24, "bold"), text_color=COLOR_ACCENT).pack(pady=(40, 5))
        ctk.CTkLabel(sidebar, text="PREMIUM SHOP", font=("Arial", 10), text_color="gray").pack()
        
        user_info = ctk.CTkFrame(sidebar, fg_color="#1F1F1F", corner_radius=10)
        user_info.pack(pady=30, padx=20, fill="x")
        ctk.CTkLabel(user_info, text=f"Active: {self.current_user}", font=("Arial", 13, "bold"), text_color="#00FF41").pack(pady=10)

        ctk.CTkButton(sidebar, text="LOGOUT", fg_color="transparent", border_width=1, border_color="#444", 
                      hover_color="#333", command=self.show_login_page).pack(side="bottom", pady=20, padx=20, fill="x")

        # Content area
        content = ctk.CTkFrame(self, fg_color=COLOR_BG, corner_radius=0)
        content.grid(row=0, column=1, sticky="nsew")
        
        ctk.CTkLabel(content, text="DASHBOARD", font=("Arial", 26, "bold"), text_color=COLOR_TEXT).pack(pady=40)

        cards_frame = ctk.CTkFrame(content, fg_color="transparent")
        cards_frame.pack(expand=True)

        for i, bot_name in enumerate(["VALORANT BOT", "FARM BOT", "MINING BOT"]):
            card = ctk.CTkFrame(cards_frame, fg_color=COLOR_CARD, width=210, height=290, corner_radius=15, border_width=1, border_color="#222")
            card.grid(row=0, column=i, padx=15)
            card.grid_propagate(False)
            
            ctk.CTkLabel(card, text=bot_name, font=("Arial", 18, "bold"), text_color=COLOR_TEXT).pack(pady=30)
            
            # จุดวงกลมแสดงสถานะ Online
            status_frame = ctk.CTkFrame(card, fg_color="transparent")
            status_frame.pack()
            ctk.CTkLabel(status_frame, text="●", text_color="#00FF41").grid(row=0, column=0)
            ctk.CTkLabel(status_frame, text=" Online", font=("Arial", 11), text_color="gray").grid(row=0, column=1)

            ctk.CTkButton(card, text="LAUNCH", fg_color=COLOR_ACCENT, hover_color=COLOR_ACCENT_HOVER, 
                          corner_radius=10, height=35, font=("Arial", 12, "bold"),
                          command=lambda n=bot_name: messagebox.showinfo("System", f"Injecting {n}...")).pack(side="bottom", pady=30, padx=20, fill="x")

    # Logic functions remain the same
    def login_logic(self):
        u, p, h = self.user_ent.get(), self.pass_ent.get(), get_hwid()
        try:
            res = requests.get(f"{FIREBASE_URL}/users/{u}.json")
            data = res.json()
            if data and data['password'] == p:
                if data['hwid'] == h:
                    self.current_user = u
                    self.show_main_menu()
                else:
                    messagebox.showerror("Error", "HWID Mismatch!")
            else:
                messagebox.showerror("Error", "Invalid Credentials")
        except:
            messagebox.showerror("Error", "Server Connection Failed")

    def register_logic(self):
        u, p, k = self.reg_u.get().strip(), self.reg_p.get().strip(), self.reg_k.get().strip()
        h = get_hwid()
        if not u or not p or not k:
            messagebox.showwarning("Warning", "Please fill all fields")
            return
        try:
            key_check = requests.get(f"{FIREBASE_URL}/license_keys/{k}.json").json()
            if key_check == "unused":
                payload = {"password": p, "hwid": h, "is_active": True}
                requests.put(f"{FIREBASE_URL}/users/{u}.json", json=payload)
                requests.delete(f"{FIREBASE_URL}/license_keys/{k}.json")
                messagebox.showinfo("Success", "Account Activated!")
                self.show_login_page()
            else:
                messagebox.showerror("Error", "Invalid or Used License Key")
        except:
            messagebox.showerror("Error", "Connection Error")

if __name__ == "__main__":
    app = KonradLauncher()
    app.mainloop()