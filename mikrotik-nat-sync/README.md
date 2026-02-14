
# 🌐 MikroTik NAT Sync for Pelican Panel

![License](https://img.shields.io/badge/license-MIT-blue.svg)
![Platform](https://img.shields.io/badge/platform-Pelican%20Panel-orange.svg)
![AI](https://img.shields.io/badge/Created%20with-AI%20Gemini-brightgreen.svg)

**MikroTik NAT Sync**

---

## 🇺🇸 English

### 🚀 Features
* **Full Automation**: Automatically creates/removes DST-NAT rules based on Pelican allocations.
* **Security First**: Define a "Forbidden Ports" list to protect sensitive services (SSH, SFTP, etc.).
* **Smart Tags**: Manages only its own rules using the `Pelican:` comment tag.
* **Easy Setup**: Configure everything (IP, credentials, intervals) directly in the Admin UI.

### 🛠 MikroTik Configuration
Enable the REST API on your router to allow communication:
```Bash
/ip service set www-ssl disabled=no port=9443
```
Note: We recommend creating a dedicated user with specific firewall permissions.

### 📦 Installation

**Method 1: Via Web Interface (Easiest)**
1. In your Pelican Admin Panel, go to **Plugins** -> **Import**.
2. Paste the URL or upload the downloaded ZIP file.
3. Click **Install** and configure via the Gear icon.

Developed with AI Assistance (Gemini)
