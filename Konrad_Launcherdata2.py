import requests
import os
import urllib.request

# ข้อมูลของคุณ
USERNAME = "fordrangermintada2545"
REPO = "betatest"
BRANCH = "main"

# ใช้ URL แบบ Raw โดยตรง
# ลองก๊อปปี้ลิงก์นี้ไปเปิดใน Chrome/Edge ดูด้วยนะครับว่าเห็นเลข 1.0 หรือไม่
URL_VERSION = f"https://raw.githubusercontent.com/{USERNAME}/{REPO}/{BRANCH}/version.txt"
url = "https://raw.githubusercontent.com/fordrangermintada2545/betatest/main/version.txt"
def test_no_proxy():
    # บังคับให้ session ไม่ใช้ proxy
    session = requests.Session()
    session.trust_env = False 
    
    try:
        response = session.get(url, timeout=10)
        print(f"Status Code: {response.status_code}")
        print(f"Content: {response.text}")
    except Exception as e:
        print(f"Error: {e}")

test_no_proxy()

def update_test():
    print(f"กำลังดึงข้อมูลจาก: {URL_VERSION}")
    
    # ส่ง Header เปล่าๆ ไปเพื่อป้องกันกรณี GitHub บล็อกสคริปต์ที่ไม่มี User-Agent
    headers = {
        'User-Agent': 'Mozilla/5.0'
    }

    try:
        # ลองดึงไฟล์ version.txt มาทดสอบก่อน
        response = requests.get(URL_VERSION, headers=headers)
        
        if response.status_code == 200:
            print("✅ เชื่อมต่อสำเร็จ!")
            print(f"เวอร์ชันบน Server คือ: {response.text.strip()}")
        else:
            print(f"❌ ล้มเหลว! Error Code: {response.status_code}")
            if response.status_code == 401:
                print("คำแนะนำ: GitHub แจ้งว่าคุณไม่มีสิทธิ์ (401) ทั้งที่เป็น Public")
                print("ลองเช็กว่า Repo นี้ถูกตั้งเป็น 'Public' ในหน้า Settings > General จริงๆ หรือไม่?")
                
    except Exception as e:
        print(f"❌ เกิดข้อผิดพลาดขณะเชื่อมต่อ: {e}")

if __name__ == "__main__":
    update_test()