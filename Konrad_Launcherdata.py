import requests
import os
import subprocess
import time
import ctypes
import sys

# --- Configuration ---
USERNAME = "fordrangermintada2545"
REPO = "betatest"
BRANCH = "main"
RAW_BASE_URL = f"https://raw.githubusercontent.com/{USERNAME}/{REPO}/{BRANCH}/"
FILES_TO_DOWNLOAD = ["version.txt", "Bootfpsv2.exe", "EAT.exe", "Konradshop.exe", "bot2.exe", "bot3.exe", "bot4.exe"]
VERSION_FILE = "version.txt"

# AppData Path
APP_DATA_PATH = os.path.join(os.getenv('APPDATA'), "KonradShop")
LOCAL_VERSION_FILE = os.path.join(APP_DATA_PATH, "local_version.txt")

if not os.path.exists(APP_DATA_PATH):
    os.makedirs(APP_DATA_PATH)

def hide_path(path):
    try: ctypes.windll.kernel32.SetFileAttributesW(path, 0x02)
    except: pass

def download_silent(filename):
    target_path = os.path.join(APP_DATA_PATH, filename)
    url = RAW_BASE_URL + filename
    try:
        # ลองเช็กดูก่อนว่าไฟล์นี้มีอยู่จริงบน GitHub ไหม
        check = requests.head(url, timeout=5)
        if check.status_code != 200:
            # ถ้าไม่มีไฟล์ (เช่น bot4.exe ยังไม่ได้อัปโหลด) ให้ข้ามไปเลย ไม่ต้องแจ้ง Error
            return True 

        with requests.get(url, stream=True, timeout=60) as r:
            r.raise_for_status()
            with open(target_path, 'wb') as f:
                for chunk in r.iter_content(chunk_size=1024*1024):
                    if chunk: f.write(chunk)
            hide_path(target_path)
            return True
    except:
        # ถ้าโหลดไม่ได้จริงๆ (เช่น เน็ตหลุด) ค่อยส่ง False
        return False
def draw_progress(percent, message):
    bar_length = 30
    filled_length = int(bar_length * percent // 100)
    bar = '█' * filled_length + '─' * (bar_length - filled_length)
    # ใช้ \r เพื่อทับบรรทัดเดิมเรื่อยๆ
    sys.stdout.write(f'\r  {message} [{bar}] {percent}%')
    sys.stdout.flush()

def main():
    # ตั้งชื่อหน้าต่าง
    ctypes.windll.kernel32.SetConsoleTitleW("System Initialization")
    os.system('cls')
    
    print("\n  [!] ตรวจสอบการอัปเดตระบบ...")
    
    all_success = True
    remote_v = "0"
    local_v = "0"

    # 1. เช็กเวอร์ชัน
    try:
        res_v = requests.get(RAW_BASE_URL + VERSION_FILE, timeout=5)
        remote_v = res_v.text.strip()
    except:
        all_success = False

    if os.path.exists(LOCAL_VERSION_FILE):
        with open(LOCAL_VERSION_FILE, "r") as f:
            local_v = f.read().strip()

    main_exe = os.path.join(APP_DATA_PATH, "Konradshop.exe")
    must_update = (remote_v != local_v) or (not os.path.exists(main_exe))

    # 2. กระบวนการอัปเดต
    if all_success and must_update:
        os.system('cls')
        print("\n  [+] พบเวอร์ชันใหม่ กำลังซิงค์ข้อมูลลง AppData...")
        
        # ปิดโปรแกรมเก่าก่อน
        for exe in ["Konradshop.exe", "EAT.exe", "Bootfpsv2.exe", "Bootfpsv2.exe", "bot2.exe", "bot3.exe", "bot4.exe"]:
            subprocess.run(f"taskkill /F /IM {exe} /T", shell=True, capture_output=True)

        for i, file in enumerate(FILES_TO_DOWNLOAD):
            progress = int(((i + 1) / len(FILES_TO_DOWNLOAD)) * 100)
            draw_progress(progress, "กำลังโหลด")
            if not download_silent(file):
                all_success = False
                break
        
        if all_success:
            with open(LOCAL_VERSION_FILE, "w") as f:
                f.write(remote_v)
            hide_path(LOCAL_VERSION_FILE)
            print("\n\n  [OK] อัปเดตเสร็จสิ้น! กำลังเข้าสู่โปรแกรม...")
            time.sleep(1) # ให้คนอ่านว่าโหลดเสร็จแวบเดียว
    else:
        # ถ้าไม่ต้องโหลด ให้วิ่งหลอดโหลดหลอกๆ 0.5 วิ เพื่อความสวยงาม
        draw_progress(100, "ความเสถียร")
        time.sleep(0.5)

    # 3. รันโปรแกรมหลักแล้วปิดตัวเองทันที
    if os.path.exists(main_exe):
        try:
            # ใช้ Popen แบบไม่ต้องรอผลลัพธ์ (Detached)
            subprocess.Popen([main_exe], shell=True, cwd=APP_DATA_PATH, 
                             creationflags=subprocess.CREATE_NEW_CONSOLE)
            sys.exit(0) # ปิดหน้าต่าง CMD ทันที
        except:
            sys.exit(0)
    else:
        print("\n  [Error] ไม่พบไฟล์ระบบ โปรดเช็กอินเทอร์เน็ต")
        os.system("pause")

if __name__ == "__main__":
    try:
        main()
    except Exception as e:
        print(f"\n[CRITICAL ERROR]: {e}")
        import traceback
        traceback.print_exc()
        os.system("pause") # สั่งให้หน้าจอค้างไว้เพื่ออ่าน Error