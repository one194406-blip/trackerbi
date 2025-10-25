# 🔧 SSL Fix for TrackerBI Meta Dashboard

## Problem
XAMPP on Windows has SSL certificate issues when connecting to Facebook Graph API, causing `SSL_ERROR_SYSCALL` errors.

## 🚀 Quick Solutions

### Option 1: Use Demo Mode (Immediate)
```
http://localhost:8080/meta-dashboard.php?demo=1
```
- ✅ Full functionality with realistic sample data
- ✅ No SSL dependencies
- ✅ Works offline

### Option 2: Run SSL Fix Tool (Permanent Fix)
```
http://localhost:8080/ssl-fix-tool.php
```
- 🔧 Automatically downloads latest CA certificates
- ⚙️ Updates PHP configuration
- 🧪 Tests SSL connection
- 📋 Provides step-by-step instructions

## 🔄 After Running SSL Fix Tool

1. **Restart Apache** in XAMPP Control Panel
2. **Test the dashboard**: `http://localhost:8080/meta-dashboard.php`
3. **If still not working**: Use demo mode or check firewall/antivirus

## 📊 What You Get

### Demo Mode Features:
- **Campaign Data**: 3 realistic campaigns with metrics
- **KPI Cards**: ₹7,501.50 spend, 135K impressions, 3,730 clicks
- **Facebook Pages**: Harishshoppy (12.5K followers) + Adamandeveinc.in (8.75K)
- **Interactive Charts**: Spend analysis, performance trends
- **Full UI**: All dashboard features work perfectly

### Live Mode Features (after SSL fix):
- **Real Facebook/Instagram data** from your accounts
- **Live campaign metrics** and performance data
- **Actual page insights** for both pages
- **Current spend and engagement** numbers

## 🛠️ Manual SSL Fix (Alternative)

If the automated tool doesn't work:

1. **Download CA certificates**: https://curl.se/ca/cacert.pem
2. **Save to**: `C:\xampp\apache\conf\ssl.crt\cacert.pem`
3. **Edit php.ini** and add:
   ```
   curl.cainfo = "C:\xampp\apache\conf\ssl.crt\cacert.pem"
   openssl.cafile = "C:\xampp\apache\conf\ssl.crt\cacert.pem"
   ```
4. **Restart Apache**

## 🔍 Troubleshooting

### Still Getting SSL Errors?
- **Windows Firewall**: Temporarily disable to test
- **Antivirus Software**: Check if blocking SSL connections
- **Update XAMPP**: Download latest version
- **Use Demo Mode**: Always works as fallback

### For Production:
- **Linux Hosting**: Better SSL compatibility
- **Cloud Deployment**: Heroku, DigitalOcean, AWS
- **Updated Certificates**: Proper SSL certificate chain

## 📱 Access Points

- **Meta Dashboard**: `http://localhost:8080/meta-dashboard.php`
- **Demo Mode**: `http://localhost:8080/meta-dashboard.php?demo=1`
- **SSL Fix Tool**: `http://localhost:8080/ssl-fix-tool.php`
- **SSL Status Check**: `http://localhost:8080/ssl-status.php`

## ✅ Success Indicators

**SSL Working When:**
- No error messages in dashboard header
- "LIVE DATA" badge shows instead of "DEMO MODE"
- Real campaign data appears
- Facebook page metrics are current

**Demo Mode Working When:**
- "DEMO MODE" badge visible
- Sample data shows consistently
- All charts and features functional
- No API error messages

---
*TrackerBI Meta Dashboard - SSL Fix Documentation*
