# SnapEngage + Hyros Integration

A powerful integration that connects SnapEngage chat widgets with Hyros attribution tracking, enabling automatic email detection and lead attribution for chat conversations.

## 🎯 What This Does

This integration automatically:
- ✅ Detects email addresses from SnapEngage chat conversations
- ✅ Triggers Hyros `[UTS] [hte]` email detection using realistic typing simulation
- ✅ Attributes chat leads to the correct marketing campaigns and traffic sources
- ✅ Sends lead data to Hyros dashboard in real-time
- ✅ Works with any SnapEngage chat widget configuration
- ✅ Prevents infinite loops and duplicate tracking

## 🚀 Features

- **Realistic Typing Simulation**: Mimics human typing behavior to trigger Hyros email detection
- **Automatic Email Detection**: Captures emails from chat forms, pre-chat forms, and user input
- **Loop Prevention**: Smart deduplication prevents infinite tracking loops
- **Multiple Themes**: Supports default and minimal chat widget themes
- **Debug Mode**: Comprehensive logging for troubleshooting
- **Multi-language Support**: Works with SnapEngage's language settings
- **Real-time Attribution**: Immediate lead tracking in Hyros dashboard

## 📋 Prerequisites

Before using this integration, you need:

1. **Active SnapEngage Account** with API key
2. **Active Hyros Account** with pixel tracking ID
3. **Web hosting** that supports PHP (for the chat.php script)
4. **HTTPS website** (required for both SnapEngage and Hyros)

## 🛠️ Installation

### Step 1: Upload Files

Upload the `chat.php` file to your web server (e.g., `https://yourdomain.com/snapengage/chat.php`)

### Step 2: Configure API Key

Edit `chat.php` and update the SnapEngage API key:

```php
$config = [
    'api_key' => 'YOUR-SNAPENGAGE-API-KEY-HERE', // Replace with your actual API key
    // ... other settings
];
```

**To find your SnapEngage API key:**
1. Login to your SnapEngage Dashboard
2. Go to **Settings** → **Get the Code**
3. Find the JavaScript code snippet
4. Copy the API key from: `se.src = '//storage.googleapis.com/code.snapengage.com/js/YOUR-API-KEY.js';`

### Step 3: Client Implementation

Add this code to your website before the closing `</body>` tag:

```html
<!DOCTYPE html>
<html>
<head>
  <!-- Your existing head content -->
  
  <!-- Hyros Tracking Script (replace YOUR-HYROS-PIXEL-ID) -->
  <script>
    var head = document.head;
    var script = document.createElement('script');
    script.type = 'text/javascript';
    script.src = "https://YOUR-HYROS-PIXEL-ID&tag=!clicked&ref_url=" + encodeURI(document.URL);
    head.appendChild(script);
  </script>
</head>
<body>
  <!-- Your page content -->
  
  <!-- SnapEngage + Hyros Integration -->
  <script src="https://yourdomain.com/snapengage/chat.php?hyros=true"></script>
</body>
</html>
```

**Replace:**
- `YOUR-HYROS-PIXEL-ID` with your actual Hyros pixel ID
- `yourdomain.com` with your actual domain where you uploaded chat.php

## ⚙️ Configuration Options

You can customize the integration by adding URL parameters:

### Basic Options

```html
<!-- Enable Hyros integration (default: true) -->
<script src="chat.php?hyros=true"></script>

<!-- Enable debug mode for troubleshooting -->
<script src="chat.php?hyros=true&debug=true"></script>

<!-- Use minimal theme (custom chat button) -->
<script src="chat.php?hyros=true&theme=minimal"></script>

<!-- Set language (default: en) -->
<script src="chat.php?hyros=true&lang=es"></script>

<!-- Show online status indicator -->
<script src="chat.php?hyros=true&status=true"></script>
```

### Advanced Options

```html
<!-- Pre-fill user information (if known from login system) -->
<script>
var userEmail = 'user@example.com'; // From your system
var userName = 'John Doe'; // From your system

var chatUrl = 'https://yourdomain.com/snapengage/chat.php?hyros=true';
chatUrl += '&email=' + encodeURIComponent(userEmail);
chatUrl += '&name=' + encodeURIComponent(userName);

var script = document.createElement('script');
script.src = chatUrl;
document.head.appendChild(script);
</script>
```

### Widget-Specific Configuration

```html
<!-- Use specific SnapEngage widget ID -->
<script src="chat.php?hyros=true&widget=YOUR-WIDGET-ID"></script>
```

## 🎨 Theme Options

### Default Theme
Uses SnapEngage's standard chat widget appearance.

```html
<script src="chat.php?hyros=true"></script>
```

### Minimal Theme
Creates a custom floating chat button and hides the default SnapEngage widget.

```html
<script src="chat.php?hyros=true&theme=minimal"></script>
```

## 🌍 Multi-language Support

The integration supports SnapEngage's language settings:

```html
<!-- Spanish -->
<script src="chat.php?hyros=true&lang=es"></script>

<!-- French -->
<script src="chat.php?hyros=true&lang=fr"></script>

<!-- German -->
<script src="chat.php?hyros=true&lang=de"></script>

<!-- Portuguese -->
<script src="chat.php?hyros=true&lang=pt"></script>

<!-- Italian -->
<script src="chat.php?hyros=true&lang=it"></script>
```

## 🔧 WordPress Integration

For WordPress sites, add this to your theme's `functions.php` file:

```php
function add_snapengage_hyros_chat() {
    // Get current user info if logged in
    $current_user = wp_get_current_user();
    $email = $current_user->user_email;
    $name = $current_user->display_name;
    
    // Build chat URL
    $chat_url = 'https://yourdomain.com/snapengage/chat.php?hyros=true';
    if ($email) {
        $chat_url .= '&email=' . urlencode($email) . '&name=' . urlencode($name);
    }
    
    // Add Hyros script first (replace with your pixel ID)
    echo '<script>
    var head = document.head;
    var script = document.createElement("script");
    script.type = "text/javascript";
    script.src = "https://t.partygo.mx/v1/lst/universal-script?ph=YOUR-HYROS-PIXEL-ID&tag=!clicked&ref_url=" + encodeURI(document.URL);
    head.appendChild(script);
    </script>';
    
    // Add enhanced chat
    echo '<script src="' . esc_url($chat_url) . '"></script>';
}
add_action('wp_footer', 'add_snapengage_hyros_chat');
```

## 🧪 Testing & Verification

### 1. Test Email Detection

1. Open your website with browser developer tools (F12)
2. Go to the **Console** tab
3. Start a chat and enter an email address
4. Look for these success indicators:

```
[SnapEngage-Realistic] 🎯 REALISTIC: Starting realistic typing simulation
[SnapEngage-Realistic] 🎯 REALISTIC: Typed "u" (current: "user@example.com")
[UTS] [hte].  ← SUCCESS! This means Hyros detected the email
[SnapEngage-Realistic] ✅ REALISTIC: Typing simulation complete
```

### 2. Debug Mode

Enable debug mode for detailed logging:

```html
<script src="chat.php?hyros=true&debug=true"></script>
```

Debug mode shows:
- Integration loading status
- Email detection events
- Hyros API calls
- Error messages
- Timing information

### 3. Verify in Hyros Dashboard

1. Login to your Hyros dashboard
2. Go to **Leads** or **Attribution** section
3. Look for new leads with chat source
4. Verify email addresses are being captured correctly

## 🔍 Troubleshooting

### Common Issues

#### 1. No `[UTS] [hte]` in Console

**Symptoms:** Chat works but no Hyros email detection
**Solutions:**
- Verify Hyros pixel ID is correct
- Check that both scripts are loading (Hyros + SnapEngage)
- Enable debug mode to see detailed logs
- Ensure email format is valid

#### 2. SnapEngage Not Loading

**Symptoms:** No chat widget appears
**Solutions:**
- Verify SnapEngage API key is correct
- Check browser console for JavaScript errors
- Ensure chat.php file is accessible
- Test SnapEngage API key with direct implementation

#### 3. Infinite Loops

**Symptoms:** Repeated console messages, browser slowdown
**Solutions:**
- The integration includes loop prevention
- Clear browser cache completely
- Check for conflicting chat widgets
- Verify you're using the latest version

#### 4. CORS Errors

**Symptoms:** "blocked by CORS policy" errors
**Solutions:**
- Ensure HTTPS is enabled
- Check that chat.php is on the same domain
- Verify server headers allow cross-origin requests

### Debug Commands

Use these in the browser console for debugging:

```javascript
// Check if integration is loaded
console.log(typeof window.SnapEngageUtils !== 'undefined' ? 'Loaded' : 'Not loaded');

// Test email detection manually
window.SnapEngageUtils.testHyrosDetection('test@example.com');

// Check processed emails
console.log(window.SnapEngageUtils.getProcessedEmails());

// Reset processed emails cache
window.SnapEngageUtils.resetProcessedEmails();

// Check configuration
console.log(window.SnapEngageUtils.config);
```

## 📊 How It Works

### Technical Overview

1. **Script Loading**: The integration loads SnapEngage's widget script
2. **Email Detection**: Monitors chat events for email addresses
3. **Realistic Simulation**: Simulates human typing to trigger Hyros detection
4. **Event Sequence**: 
   - Focus hidden email input
   - Type each character with realistic timing
   - Trigger keydown, input, keyup events
   - Send final change and blur events
5. **Hyros Tracking**: Hyros detects the "typed" email and fires `[UTS] [hte]`
6. **Attribution**: Lead is attributed to correct traffic source

### Event Flow

```
User starts chat → Email captured → Realistic typing simulation → 
Hyros detects email → [UTS] [hte] fires → Lead attributed → 
Data appears in Hyros dashboard
```

### Loop Prevention

The integration includes multiple safeguards:
- Email deduplication using Set data structure
- Trigger locks to prevent re-entry
- Invalid email filtering
- Rate limiting (max 5 triggers per minute)
- Processed email cleanup after 5 minutes

## 🔐 Security Considerations

- **HTTPS Required**: Both SnapEngage and Hyros require HTTPS
- **API Key Protection**: Keep SnapEngage API keys secure
- **Input Validation**: All email inputs are validated and sanitized
- **No Sensitive Data**: No sensitive information is logged or transmitted
- **CORS Compliance**: Follows cross-origin security policies

## 📈 Performance

- **Lightweight**: ~15KB additional JavaScript
- **Efficient**: Only processes valid email addresses
- **Non-blocking**: Asynchronous loading and processing
- **Cached**: Widget status cached for 5 minutes
- **Optimized**: Minimal DOM manipulation and event handling

## 🆕 Version History

### v3.0.0 (Current)
- ✅ Realistic typing simulation for Hyros detection
- ✅ Complete loop prevention system
- ✅ Enhanced debugging and logging
- ✅ Multi-language support
- ✅ WordPress integration helper
- ✅ Improved error handling

### v2.0.0
- ✅ Enhanced email detection
- ✅ Basic loop prevention
- ✅ Multiple event monitoring

### v1.0.0
- ✅ Basic SnapEngage + Hyros integration
- ✅ Simple email detection

## 📞 Support

### Self-Help Resources

1. **Enable Debug Mode**: Add `&debug=true` to see detailed logs
2. **Check Console**: Look for error messages in browser developer tools
3. **Test Components**: Verify SnapEngage and Hyros work independently
4. **Clear Cache**: Clear browser cache and test again

### Getting Help

If you need assistance:

1. **Gather Information**:
   - Browser console logs (with debug mode enabled)
   - SnapEngage API key (keep private)
   - Hyros pixel ID (keep private)
   - Website URL where integration is installed
   - Specific error messages

2. **Create Issue**: Include all gathered information
3. **Expected vs Actual**: Describe what should happen vs what actually happens

## 📄 License

This project is licensed under the MIT License - see the LICENSE file for details.

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

### Development Setup

1. Clone the repository
2. Set up a local PHP server
3. Configure with your test SnapEngage and Hyros accounts
4. Test thoroughly before submitting changes

---

## ✅ Quick Start Checklist

- [ ] Upload `chat.php` to your web server
- [ ] Update SnapEngage API key in `chat.php`
- [ ] Get your Hyros pixel ID
- [ ] Add both scripts to your website
- [ ] Test with debug mode enabled
- [ ] Verify `[UTS] [hte]` appears in console
- [ ] Check Hyros dashboard for new leads
- [ ] Remove debug mode for production

**Need help?** Enable debug mode and check the console for detailed information about what's happening.