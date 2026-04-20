import os
import subprocess
import ctypes
import customtkinter as ctk
from tkinter import messagebox
import winreg
import re

# ตั้งค่าหน้าตาโปรแกรม
ctk.set_appearance_mode("Dark")
ctk.set_default_color_theme("blue")

class OptimizerApp(ctk.CTk):
    def __init__(self):
        super().__init__()

        self.title("FiveM All-in-One Optimizer Pro")
        self.geometry("500x550")

        self.label = ctk.CTkLabel(self, text="FiveM Optimization Tool", font=("Inter", 24, "bold"))
        self.label.pack(pady=20)

        # --- Section 1: System & Input Master ---
        self.create_button("🚀 Full System & Input Optimize", self.master_system_optimize, "#3498DB")
        
        # --- Section 2: Graphics Master ---
        self.create_button("🎮 Ultimate Graphic Fix (No Shadow/No Lag)", self.master_graphic_optimize, "#E67E22")
        
        # --- Section 3: In-Game Action ---
        self.create_button("⚡ Set FiveM High Priority (เปิดเกมก่อน)", self.priority_logic, "#9B59B6")

        # --- Section 4: Maintenance ---
        self.create_button("🧹 Clear FiveM Cache", self.clear_cache, "#7F8C8D")
        self.create_button("⏪ Restore All Settings", self.restore_registry, "#E74C3C")

        self.status_label = ctk.CTkLabel(self, text="Status: Ready", font=("Inter", 13))
        self.status_label.pack(side="bottom", pady=20)

    def create_button(self, text, command, color):
        btn = ctk.CTkButton(self, text=text, command=command, fg_color=color, height=45, font=("Inter", 14, "bold"))
        btn.pack(pady=10, padx=30, fill="x")

    def is_admin(self):
        try: return ctypes.windll.shell32.IsUserAnAdmin()
        except: return False

    def get_settings_path(self):
        user_profile = os.environ['USERPROFILE']
        paths = [
            os.path.join(user_profile, "Documents", "Rockstar Games", "GTA V", "settings.xml"),
            os.path.join(user_profile, "OneDrive", "Documents", "Rockstar Games", "GTA V", "settings.xml")
        ]
        return next((p for p in paths if os.path.exists(p)), None)

    # --- รวม: Windows + Net + Mouse + Keyboard ---
    def master_system_optimize(self):
        if not self.is_admin():
            messagebox.showerror("Error", "ต้องรันโปรแกรมแบบ Administrator เท่านั้นค่าถึงจะเปลี่ยน!")
            return
        
        try:
            # คำสั่งแก้ไขแบบบังคับ (Force)
            commands = [
                # System & Net
                'reg add "HKLM\\SOFTWARE\\Microsoft\\Windows NT\\CurrentVersion\\Multimedia\\SystemProfile" /v "NetworkThrottlingIndex" /t REG_DWORD /d 4294967295 /f',
                'reg add "HKLM\\SOFTWARE\\Microsoft\\Windows NT\\CurrentVersion\\Multimedia\\SystemProfile" /v "SystemResponsiveness" /t REG_DWORD /d 0 /f',
                'reg add "HKCU\\System\\GameConfigStore" /v "GameDVR_Enabled" /t REG_DWORD /d 0 /f',
                
                # Mouse (บังคับปิด Acceleration)
                'reg add "HKCU\\Control Panel\\Mouse" /v "MouseSpeed" /t REG_SZ /d 0 /f',
                'reg add "HKCU\\Control Panel\\Mouse" /v "MouseThreshold1" /t REG_SZ /d 0 /f',
                'reg add "HKCU\\Control Panel\\Mouse" /v "MouseThreshold2" /t REG_SZ /d 0 /f',
                
                # Keyboard (Response Time)
                'reg add "HKCU\\Control Panel\\Accessibility\\Keyboard Response" /v "AutoRepeatDelay" /t REG_SZ /d 200 /f',
                'reg add "HKCU\\Control Panel\\Accessibility\\Keyboard Response" /v "AutoRepeatRate" /t REG_SZ /d 15 /f',
                'reg add "HKCU\\Control Panel\\Accessibility\\Keyboard Response" /v "Flags" /t REG_SZ /d 59 /f'
            ]
            
            for cmd in commands:
                # รันแบบซ่อนหน้าต่างและเช็คผล
                subprocess.run(cmd, shell=True, stdout=subprocess.PIPE, stderr=subprocess.PIPE)

            # สั่งให้ Windows อัปเดตการตั้งค่าเมาส์และคีย์บอร์ดทันทีโดยไม่ต้องรีสตาร์ท (SPI_SETMOUSESPEED)
            ctypes.windll.user32.SystemParametersInfoW(0x0071, 0, 10, 0x01 | 0x02) 

            # รีสตาร์ท Explorer
            subprocess.run("taskkill /f /im explorer.exe && start explorer.exe", shell=True)
            
            messagebox.showinfo("Success", "✅ บังคับแก้ค่าเรียบร้อย! กรุณาเช็คใน regedit อีกครั้ง\n(ถ้าบางค่ายังไม่เปลี่ยน แนะนำให้ Restart เครื่องครับ)")
        except Exception as e:
            messagebox.showerror("Error", f"เกิดข้อผิดพลาด: {e}")

    # --- รวม: Extreme Low + Fix Stuttering ---
    def master_graphic_optimize(self):
        target = self.get_settings_path()
        if target:
            try:
                ctypes.windll.kernel32.SetFileAttributesW(target, 128)
                with open(target, 'r', encoding='utf-8') as f:
                    data = f.read()

                # ปิดเงา/หญ้า/น้ำ/แสงสะท้อน
                data = re.sub(r'<ShadowQuality value="\d+" />', '<ShadowQuality value="0" />', data)
                data = re.sub(r'<GrassQuality value="\d+" />', '<GrassQuality value="0" />', data)
                data = re.sub(r'<ReflectionQuality value="\d+" />', '<ReflectionQuality value="0" />', data)
                data = re.sub(r'<WaterQuality value="\d+" />', '<WaterQuality value="0" />', data)
                
                # แก้กระชาก (Tessellation, LodScale, AO, Particle)
                data = re.sub(r'<Tessellation value="\d+" />', '<Tessellation value="0" />', data)
                data = re.sub(r'<LodScale value="[\d.]+" />', '<LodScale value="0.000000" />', data)
                data = re.sub(r'<AmbientOcclusionQuality value="\d+" />', '<AmbientOcclusionQuality value="0" />', data)
                data = re.sub(r'<ParticleQuality value="\d+" />', '<ParticleQuality value="0" />', data)

                with open(target, 'w', encoding='utf-8') as f:
                    f.write(data)
                
                self.status_label.configure(text="Status: Graphics Ultimate Optimized!", text_color="#2ECC71")
                messagebox.showinfo("Success", "✅ ปรับแต่งกราฟิกขั้นสูงสุดเรียบร้อย!\n- ปิดเงา/หญ้า และลดอาการกระชากแล้ว")
            except Exception as e:
                messagebox.showerror("Error", f"Error: {e}")
        else:
            messagebox.showwarning("Warning", "ไม่พบไฟล์ settings.xml")

    def priority_logic(self):
        if not self.is_admin():
            messagebox.showerror("Error", "กรุณารันในโหมด Admin!")
            return
        ps_cmd = 'Get-Process FiveM* -ErrorAction SilentlyContinue | ForEach-Object { $_.PriorityClass = "High" }'
        check = subprocess.run(["powershell", "-Command", "Get-Process FiveM* -ErrorAction SilentlyContinue"], capture_output=True, text=True)
        if not check.stdout:
            messagebox.showwarning("Warning", "กรุณาเปิดเกมก่อน!")
            return
        subprocess.run(["powershell", "-Command", ps_cmd])
        messagebox.showinfo("Status", "SET HIGH PRIORITY เรียบร้อย!")

    def clear_cache(self):
        path = os.path.join(os.getenv('LOCALAPPDATA'), "FiveM", "FiveM.app", "data", "cache")
        if os.path.exists(path):
            subprocess.run(f'rmdir /s /q "{path}"', shell=True)
            messagebox.showinfo("Success", "ลบ Cache เรียบร้อย")
        else: messagebox.showwarning("Warning", "ไม่พบ Cache")

    def restore_registry(self):
        backups = ["system_backup.reg", "mouse_backup.reg", "kb_backup.reg"]
        found = False
        for f in backups:
            if os.path.exists(f):
                subprocess.run(f'reg import "{f}"', shell=True)
                found = True
        
        if found:
            subprocess.run("taskkill /f /im explorer.exe && start explorer.exe", shell=True)
            messagebox.showinfo("Success", "คืนค่าทุกอย่างสำเร็จ!")
        else:
            messagebox.showwarning("Warning", "ไม่พบไฟล์ Backup")

if __name__ == "__main__":
    app = OptimizerApp()
    app.mainloop()