import customtkinter as ctk
import requests
import secrets
import string


# --- ตั้งค่า Firebase ---
DB_URL = "https://konradshop-default-rtdb.asia-southeast1.firebasedatabase.app/keys.json" 

class KeyGenApp(ctk.CTk):
    def __init__(self):
        super().__init__()

        self.title("Key Generator (Admin Only)")
        self.geometry("400x450")
        ctk.set_appearance_mode("dark")

        # UI Elements
        self.label = ctk.CTkLabel(self, text="ระบบสร้าง Key เข้าใช้งาน", font=("Inter", 20, "bold"))
        self.label.pack(pady=20)

        self.amount_entry = ctk.CTkEntry(self, placeholder_text="ระบุจำนวนคีย์ที่ต้องการ")
        self.amount_entry.pack(pady=10)

        self.gen_button = ctk.CTkButton(self, text="สร้างและบันทึกคีย์", command=self.generate_and_upload)
        self.gen_button.pack(pady=10)

        self.result_box = ctk.CTkTextbox(self, width=300, height=200)
        self.result_box.pack(pady=20)

    def generate_random_key(self, length=16):
        chars = string.ascii_uppercase + string.digits
        key = ''.join(secrets.choice(chars) for _ in range(length))
        return '-'.join([key[i:i+4] for i in range(0, len(key), 4)])

    def generate_and_upload(self):
        try:
            amount = int(self.amount_entry.get())
            new_keys_data = {}
            display_text = ""

            for _ in range(amount):
                key = self.generate_random_key()
                new_keys_data[key] = {"status": "unused"}
                display_text += f"{key}\n"

            # ส่งข้อมูลไป Firebase
            response = requests.patch(DB_URL, json=new_keys_data)
            
            if response.status_code == 200:
                self.result_box.delete("1.0", "end")
                self.result_box.insert("1.0", display_text)
                self.label.configure(text="✅ สำเร็จ!", text_color="green")
            else:
                self.label.configure(text="❌ เชื่อมต่อพลาด", text_color="red")
        
        except ValueError:
            self.label.configure(text="❌ ใส่ตัวเลขเท่านั้น", text_color="red")

if __name__ == "__main__":
    app = KeyGenApp()
    app.mainloop()