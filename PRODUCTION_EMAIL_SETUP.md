# 📧 Production Email Setup Guide

## Choose Your Email Provider

Select the email service you want to use for sending emails:

---

## 🔵 Option 1: Gmail (Google Workspace or Personal Gmail)

### Requirements:
- Gmail account
- App Password (required for security)

### Step 1: Create App Password
1. Go to: https://myaccount.google.com/security
2. Enable **2-Step Verification** (if not already enabled)
3. Search for "App passwords"
4. Create new app password for "Mail"
5. Copy the 16-character password

### Step 2: Update .env
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-16-char-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="your-email@gmail.com"
MAIL_FROM_NAME="HRMS System"

# Your admin email (where vacation requests go)
ADMIN_EMAIL="your-admin-email@gmail.com"
```

### Important Notes:
- ✅ Use App Password, NOT your regular Gmail password
- ✅ Enable "Less secure app access" if using old Gmail
- ✅ Port 587 with TLS is recommended
- ✅ Can also use Port 465 with SSL

---

## 🔷 Option 2: Microsoft Outlook / Office 365

### Step 1: Get SMTP Settings
- Use your Outlook email and password
- No app password needed

### Step 2: Update .env
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp-mail.outlook.com
MAIL_PORT=587
MAIL_USERNAME=your-email@outlook.com
MAIL_PASSWORD=your-outlook-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="your-email@outlook.com"
MAIL_FROM_NAME="HRMS System"

ADMIN_EMAIL="your-admin-email@outlook.com"
```

### For Office 365 Business:
```env
MAIL_HOST=smtp.office365.com
MAIL_PORT=587
```

---

## 🟢 Option 3: SendGrid (Recommended for High Volume)

### Why SendGrid?
- ✅ 100 emails/day FREE forever
- ✅ No email account needed
- ✅ Better deliverability
- ✅ Professional service

### Step 1: Create SendGrid Account
1. Go to: https://signup.sendgrid.com/
2. Sign up (free account)
3. Verify your email
4. Create an API Key: Settings → API Keys → Create API Key

### Step 2: Update .env
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=YOUR_SENDGRID_API_KEY_HERE
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
MAIL_FROM_NAME="HRMS System"

ADMIN_EMAIL="your-admin@yourdomain.com"
```

**Note:** Username is literally the word "apikey"

---

## 🟡 Option 4: Mailgun (Alternative to SendGrid)

### Features:
- ✅ 5,000 emails/month FREE for 3 months
- ✅ Then $35/month or pay-as-you-go
- ✅ Good API and documentation

### Setup:
1. Sign up: https://signup.mailgun.com/
2. Get SMTP credentials from dashboard
3. Update .env:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailgun.org
MAIL_PORT=587
MAIL_USERNAME=postmaster@your-domain.mailgun.org
MAIL_PASSWORD=your-mailgun-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
MAIL_FROM_NAME="HRMS System"

ADMIN_EMAIL="your-admin@yourdomain.com"
```

---

## 🟠 Option 5: Custom SMTP Server

### If you have your own mail server:
```env
MAIL_MAILER=smtp
MAIL_HOST=mail.yourdomain.com
MAIL_PORT=587
MAIL_USERNAME=noreply@yourdomain.com
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
MAIL_FROM_NAME="HRMS System"

ADMIN_EMAIL="admin@yourdomain.com"
```

### Common Ports:
- **587** - TLS (recommended)
- **465** - SSL
- **25** - No encryption (not recommended)

---

## 🎯 Configuration Explained

### Required Settings:

| Setting | Purpose | Example |
|---------|---------|---------|
| `MAIL_MAILER` | Driver to use | `smtp` |
| `MAIL_HOST` | SMTP server address | `smtp.gmail.com` |
| `MAIL_PORT` | SMTP port | `587` |
| `MAIL_USERNAME` | Your email or API username | `you@gmail.com` |
| `MAIL_PASSWORD` | Password or API key | `your-app-password` |
| `MAIL_ENCRYPTION` | Security protocol | `tls` or `ssl` |
| `MAIL_FROM_ADDRESS` | Sender email (shown in inbox) | `noreply@company.com` |
| `MAIL_FROM_NAME` | Sender name (shown in inbox) | `HRMS System` |
| `ADMIN_EMAIL` | Where vacation requests go | `admin@company.com` |

---

## 📬 How Emails Will Be Sent

### When Employee Submits Vacation Request:

```
┌─────────────────────────────────────────────────┐
│ FROM: MAIL_FROM_ADDRESS (noreply@company.com)  │
│ TO:   Employee Email (from database)           │
│ SUBJECT: Vacation Request Submitted            │
│ CONTENT: Confirmation with request details     │
└─────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────┐
│ FROM: MAIL_FROM_ADDRESS (noreply@company.com)  │
│ TO:   ADMIN_EMAIL (admin@company.com)          │
│ SUBJECT: New Vacation Request - [Employee]     │
│ CONTENT: Request details + Approve/Reject btns │
└─────────────────────────────────────────────────┘
```

### When Admin Approves/Rejects:

```
┌─────────────────────────────────────────────────┐
│ FROM: MAIL_FROM_ADDRESS (noreply@company.com)  │
│ TO:   Employee Email (from database)           │
│ SUBJECT: Vacation Request Approved/Rejected    │
│ CONTENT: Status update and next steps          │
└─────────────────────────────────────────────────┘
```

---

## 🧪 Testing Your Configuration

### Step 1: Update .env
Choose one of the options above and update your `.env` file.

### Step 2: Clear Cache
```bash
php artisan config:clear
php artisan cache:clear
```

### Step 3: Test Email Sending
```bash
php artisan tinker
```

Then run:
```php
use Illuminate\Support\Facades\Mail;

// Test email to admin
Mail::raw('This is a test email from your HRMS system. If you receive this, your email configuration is working correctly!', function ($message) {
    $message->to(config('mail.admin_email'))
            ->subject('HRMS Test Email - Configuration Working!');
});

echo "✅ Test email sent to: " . config('mail.admin_email') . "\n";
echo "Check your inbox!\n";
exit;
```

### Step 4: Submit Real Vacation Request
1. Go to: `http://127.0.0.1:8000/vacation-request`
2. Submit a request
3. Check **both** email inboxes:
   - Employee email (should get confirmation)
   - Admin email (should get notification with buttons)

---

## ⚠️ Common Issues & Solutions

### Issue 1: "Failed to authenticate"
**Solution:** 
- Gmail: Use App Password, not regular password
- Enable 2FA first
- Check username format (some need full email, some don't)

### Issue 2: "Connection timeout"
**Solution:**
- Check if port is correct (587 or 465)
- Try changing `MAIL_ENCRYPTION` between `tls` and `ssl`
- Check firewall settings
- Try port 465 with `ssl` if 587 doesn't work

### Issue 3: "TLS/SSL error"
**Solution:**
```env
# Change from:
MAIL_ENCRYPTION=tls

# To:
MAIL_ENCRYPTION=ssl
MAIL_PORT=465
```

### Issue 4: "Email in spam folder"
**Solution:**
- Use a real domain email (not noreply@test.com)
- Use SendGrid or Mailgun for better deliverability
- Add SPF and DKIM records to your domain
- Send a test email to yourself first

### Issue 5: "530 5.7.0 Must issue STARTTLS"
**Solution:**
```env
MAIL_ENCRYPTION=tls
MAIL_PORT=587
```

---

## 🔐 Security Best Practices

1. **Never commit `.env` to Git**
   ```bash
   # Already in .gitignore, but verify:
   cat .gitignore | grep .env
   ```

2. **Use App Passwords (Gmail)**
   - Never use your main password
   - Generate app-specific passwords

3. **Use Environment Variables**
   - Keep sensitive data in `.env`
   - Never hardcode passwords in code

4. **Regular Password Rotation**
   - Change SMTP passwords every 90 days
   - Update app passwords if compromised

---

## 📊 Recommended Configuration by Use Case

### Small Business (< 50 employees):
**Gmail or Outlook**
- ✅ Free
- ✅ Easy setup
- ✅ Reliable
- ⚠️ Daily sending limits

### Medium Business (50-200 employees):
**SendGrid Free Tier**
- ✅ 100 emails/day free
- ✅ Professional
- ✅ Better deliverability
- ✅ Analytics included

### Large Business (200+ employees):
**SendGrid Paid or Mailgun**
- ✅ Unlimited sending
- ✅ Dedicated IP
- ✅ Advanced features
- ✅ Support included

---

## 🎯 Quick Setup Commands

### After updating .env:
```bash
# Clear all caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear

# Verify configuration
php artisan tinker --execute="
echo 'Current Mail Config:' . PHP_EOL;
echo 'Host: ' . config('mail.mailers.smtp.host') . PHP_EOL;
echo 'Port: ' . config('mail.mailers.smtp.port') . PHP_EOL;
echo 'Username: ' . config('mail.mailers.smtp.username') . PHP_EOL;
echo 'From: ' . config('mail.from.address') . PHP_EOL;
echo 'Admin Email: ' . config('mail.admin_email') . PHP_EOL;
"
```

---

## ✅ Final Checklist

Before going live:
- [ ] Email provider chosen
- [ ] `.env` updated with correct settings
- [ ] Caches cleared
- [ ] Test email sent successfully
- [ ] Employee email works (gets confirmation)
- [ ] Admin email works (gets notification with buttons)
- [ ] Approve/Reject buttons work from email
- [ ] Status change emails work
- [ ] Check spam folders
- [ ] Verify email formatting looks good

---

## 🆘 Need Help?

### Check Laravel Logs:
```bash
tail -f storage/logs/laravel.log
```

### Debug Mail Sending:
```bash
# Enable mail debugging
php artisan tinker --execute="
config(['mail.log_channel' => 'stack']);
// Try sending test email
"
```

### Common Gmail Error Codes:
- `535 5.7.8` - Username/password incorrect
- `530 5.7.0` - Need TLS enabled
- `534 5.7.9` - App password required

---

## 🎉 You're Ready!

Choose your email provider from the options above, update your `.env`, and test!

**Recommended for most users: Gmail with App Password** 👈 Easiest to set up!

