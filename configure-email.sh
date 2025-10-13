#!/bin/bash

# HRMS Email Configuration Script
# This script helps you configure production email settings

echo "╔════════════════════════════════════════════════════════╗"
echo "║       HRMS Production Email Configuration             ║"
echo "╚════════════════════════════════════════════════════════╝"
echo ""

# Backup current .env
cp .env .env.backup
echo "✓ Backed up .env to .env.backup"
echo ""

echo "Choose your email provider:"
echo "1) Gmail (Google Workspace or Personal)"
echo "2) Microsoft Outlook / Office 365"
echo "3) SendGrid"
echo "4) Custom SMTP Server"
echo ""
read -p "Enter choice (1-4): " choice

case $choice in
    1)
        echo ""
        echo "═══ Gmail Configuration ═══"
        echo ""
        echo "⚠️  IMPORTANT: You need a Gmail App Password"
        echo "   1. Go to: https://myaccount.google.com/security"
        echo "   2. Enable 2-Step Verification"
        echo "   3. Create App Password for 'Mail'"
        echo "   4. Copy the 16-character password"
        echo ""
        read -p "Your Gmail address: " email
        read -p "Your App Password (16 chars): " password
        read -p "Admin email (where requests go): " admin_email
        
        # Update .env
        sed -i '' "s/MAIL_HOST=.*/MAIL_HOST=smtp.gmail.com/" .env
        sed -i '' "s/MAIL_PORT=.*/MAIL_PORT=587/" .env
        sed -i '' "s/MAIL_USERNAME=.*/MAIL_USERNAME=$email/" .env
        sed -i '' "s/MAIL_PASSWORD=.*/MAIL_PASSWORD=$password/" .env
        sed -i '' "s/MAIL_ENCRYPTION=.*/MAIL_ENCRYPTION=tls/" .env
        sed -i '' "s/MAIL_FROM_ADDRESS=.*/MAIL_FROM_ADDRESS=\"$email\"/" .env
        sed -i '' "s/ADMIN_EMAIL=.*/ADMIN_EMAIL=\"$admin_email\"/" .env
        
        echo ""
        echo "✓ Gmail configuration updated!"
        ;;
        
    2)
        echo ""
        echo "═══ Outlook Configuration ═══"
        echo ""
        read -p "Your Outlook email: " email
        read -p "Your Outlook password: " password
        read -p "Admin email (where requests go): " admin_email
        
        # Update .env
        sed -i '' "s/MAIL_HOST=.*/MAIL_HOST=smtp-mail.outlook.com/" .env
        sed -i '' "s/MAIL_PORT=.*/MAIL_PORT=587/" .env
        sed -i '' "s/MAIL_USERNAME=.*/MAIL_USERNAME=$email/" .env
        sed -i '' "s/MAIL_PASSWORD=.*/MAIL_PASSWORD=$password/" .env
        sed -i '' "s/MAIL_ENCRYPTION=.*/MAIL_ENCRYPTION=tls/" .env
        sed -i '' "s/MAIL_FROM_ADDRESS=.*/MAIL_FROM_ADDRESS=\"$email\"/" .env
        sed -i '' "s/ADMIN_EMAIL=.*/ADMIN_EMAIL=\"$admin_email\"/" .env
        
        echo ""
        echo "✓ Outlook configuration updated!"
        ;;
        
    3)
        echo ""
        echo "═══ SendGrid Configuration ═══"
        echo ""
        echo "⚠️  You need a SendGrid API Key"
        echo "   1. Sign up: https://signup.sendgrid.com/"
        echo "   2. Go to: Settings → API Keys"
        echo "   3. Create API Key"
        echo ""
        read -p "Your SendGrid API Key: " apikey
        read -p "From email address: " from_email
        read -p "Admin email (where requests go): " admin_email
        
        # Update .env
        sed -i '' "s/MAIL_HOST=.*/MAIL_HOST=smtp.sendgrid.net/" .env
        sed -i '' "s/MAIL_PORT=.*/MAIL_PORT=587/" .env
        sed -i '' "s/MAIL_USERNAME=.*/MAIL_USERNAME=apikey/" .env
        sed -i '' "s/MAIL_PASSWORD=.*/MAIL_PASSWORD=$apikey/" .env
        sed -i '' "s/MAIL_ENCRYPTION=.*/MAIL_ENCRYPTION=tls/" .env
        sed -i '' "s/MAIL_FROM_ADDRESS=.*/MAIL_FROM_ADDRESS=\"$from_email\"/" .env
        sed -i '' "s/ADMIN_EMAIL=.*/ADMIN_EMAIL=\"$admin_email\"/" .env
        
        echo ""
        echo "✓ SendGrid configuration updated!"
        ;;
        
    4)
        echo ""
        echo "═══ Custom SMTP Configuration ═══"
        echo ""
        read -p "SMTP Host: " host
        read -p "SMTP Port (587 recommended): " port
        read -p "SMTP Username: " username
        read -p "SMTP Password: " password
        read -p "Encryption (tls/ssl): " encryption
        read -p "From email address: " from_email
        read -p "Admin email (where requests go): " admin_email
        
        # Update .env
        sed -i '' "s/MAIL_HOST=.*/MAIL_HOST=$host/" .env
        sed -i '' "s/MAIL_PORT=.*/MAIL_PORT=$port/" .env
        sed -i '' "s/MAIL_USERNAME=.*/MAIL_USERNAME=$username/" .env
        sed -i '' "s/MAIL_PASSWORD=.*/MAIL_PASSWORD=$password/" .env
        sed -i '' "s/MAIL_ENCRYPTION=.*/MAIL_ENCRYPTION=$encryption/" .env
        sed -i '' "s/MAIL_FROM_ADDRESS=.*/MAIL_FROM_ADDRESS=\"$from_email\"/" .env
        sed -i '' "s/ADMIN_EMAIL=.*/ADMIN_EMAIL=\"$admin_email\"/" .env
        
        echo ""
        echo "✓ Custom SMTP configuration updated!"
        ;;
        
    *)
        echo "Invalid choice. Exiting."
        exit 1
        ;;
esac

echo ""
echo "═══ Configuration Complete ═══"
echo ""
echo "Next steps:"
echo "1. Clear Laravel cache:"
echo "   php artisan config:clear"
echo "   php artisan cache:clear"
echo ""
echo "2. Test email sending:"
echo "   php artisan tinker"
echo "   Then: Mail::raw('Test', fn(\$m) => \$m->to('$admin_email')->subject('Test'));"
echo ""
echo "3. Submit a vacation request to test the full flow"
echo ""
echo "✅ Done! Your production email is configured."

