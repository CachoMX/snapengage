<?php
/**
 * SnapEngage + Hyros Integration with Email Detection Trigger
 * 
 * This version properly triggers Hyros email detection
 */

// Configuration
$config = [
    'api_key' => 'a2b945fb-b46d-4897-94a5-4932ef96fe3e', // Replace with your actual SnapEngage API key
    'default_widget_id' => null,
    'allowed_domains' => [],
    'cache_timeout' => 300,
    'debug' => false,
    'hyros_integration' => true,
    'hyros_pixel_id' => null
];

// Get parameters from URL
$widgetId = $_GET['widget'] ?? $config['default_widget_id'];
$userEmail = $_GET['email'] ?? null;
$userName = $_GET['name'] ?? null;
$language = $_GET['lang'] ?? 'en';
$showStatus = isset($_GET['status']) ? (bool)$_GET['status'] : false;
$theme = $_GET['theme'] ?? 'default';
$debug = isset($_GET['debug']) ? (bool)$_GET['debug'] : $config['debug'];
$hyrosIntegration = isset($_GET['hyros']) ? (bool)$_GET['hyros'] : $config['hyros_integration'];

// Set proper headers
header('Content-Type: application/javascript; charset=utf-8');
header('Cache-Control: public, max-age=300');
header('Access-Control-Allow-Origin: *');

if (!$debug) {
    error_reporting(0);
    ini_set('display_errors', 0);
}

function isWidgetOnline($widgetId, $cacheTimeout = 300) {
    if (!$widgetId) return true;
    
    $cacheFile = sys_get_temp_dir() . '/snapengage_status_' . md5($widgetId);
    
    if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < $cacheTimeout) {
        return (bool)file_get_contents($cacheFile);
    }
    
    $url = "https://www.snapengage.com/public/api/v2/chat/{$widgetId}";
    $context = stream_context_create([
        'http' => [
            'timeout' => 5,
            'user_agent' => 'CallWithCarlos-SnapEngage/1.0'
        ]
    ]);
    
    $response = @file_get_contents($url, false, $context);
    $isOnline = false;
    
    if ($response) {
        $data = json_decode($response, true);
        $isOnline = isset($data['data']['online']) ? ($data['data']['online'] === 'true') : false;
    }
    
    @file_put_contents($cacheFile, $isOnline ? '1' : '0');
    return $isOnline;
}

$isOnline = isWidgetOnline($widgetId, $config['cache_timeout']);
?>
/*!
 * SnapEngage + Hyros Integration with Email Detection Trigger
 * Generated: <?php echo date('c'); ?>

 * SOLUTION: Creates hidden email input that Hyros can detect
 */

(function() {
    'use strict';
    
    var config = {
        apiKey: '<?php echo $config['api_key']; ?>',
        widgetId: '<?php echo addslashes($widgetId); ?>',
        userEmail: '<?php echo addslashes($userEmail); ?>',
        userName: '<?php echo addslashes($userName); ?>',
        language: '<?php echo $language; ?>',
        showStatus: <?php echo $showStatus ? 'true' : 'false'; ?>,
        isOnline: <?php echo $isOnline ? 'true' : 'false'; ?>,
        theme: '<?php echo $theme; ?>',
        debug: <?php echo $debug ? 'true' : 'false'; ?>,
        hyrosIntegration: <?php echo $hyrosIntegration ? 'true' : 'false'; ?>,
        hyrosPixelId: '<?php echo $config['hyros_pixel_id']; ?>'
    };
    
    function debugLog(message, data) {
        if (config.debug && typeof console !== 'undefined') {
            console.log('[SnapEngage-Hyros] ' + message, data || '');
        }
    }
    
    debugLog('🎯 SOLUTION: Initializing SnapEngage with Hyros Email Detection Trigger', config);
    
    if (window.SnapEngageIntegrationLoaded) {
        debugLog('Already loaded, skipping...');
        return;
    }
    window.SnapEngageIntegrationLoaded = true;
    
    // SOLUTION: Create hidden email input that Hyros can detect
    function createHyrosEmailTrigger() {
        if (!config.hyrosIntegration) return;
        
        debugLog('🎯 Creating hidden email input for Hyros detection');
        
        // Create hidden form that Hyros can monitor
        var hiddenForm = document.createElement('form');
        hiddenForm.style.cssText = 'position: absolute; left: -9999px; opacity: 0; pointer-events: none; height: 1px; width: 1px; overflow: hidden;';
        hiddenForm.id = 'hyros-email-trigger-form';
        
        var hiddenEmailInput = document.createElement('input');
        hiddenEmailInput.type = 'email';
        hiddenEmailInput.name = 'email';
        hiddenEmailInput.id = 'hyros-email-trigger-input';
        hiddenEmailInput.placeholder = 'email@example.com';
        hiddenEmailInput.style.cssText = 'width: 1px; height: 1px; opacity: 0;';
        
        hiddenForm.appendChild(hiddenEmailInput);
        
        // Add to page when DOM is ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function() {
                document.body.appendChild(hiddenForm);
                debugLog('✅ Hidden email input added to page for Hyros');
            });
        } else {
            document.body.appendChild(hiddenForm);
            debugLog('✅ Hidden email input added to page for Hyros');
        }
        
        return hiddenEmailInput;
    }
    
    // SOLUTION: Trigger Hyros email detection when chat email is captured
    function triggerHyrosEmailDetection(email) {
        if (!config.hyrosIntegration || !email) return;
        
        debugLog('🎯 TRIGGERING: Hyros email detection for: ' + email);
        
        try {
            // Method 1: Fill hidden input and trigger events
            var hiddenInput = document.getElementById('hyros-email-trigger-input');
            if (hiddenInput) {
                debugLog('📧 Filling hidden email input');
                
                // Set the value
                hiddenInput.value = email;
                
                // Trigger all the events Hyros might be listening for
                var events = ['input', 'change', 'keyup', 'keydown', 'blur', 'focus'];
                events.forEach(function(eventType) {
                    var event = new Event(eventType, { bubbles: true, cancelable: true });
                    hiddenInput.dispatchEvent(event);
                });
                
                // Special keyup event with email-like typing
                setTimeout(function() {
                    hiddenInput.focus();
                    setTimeout(function() {
                        hiddenInput.blur();
                        debugLog('🎯 Triggered focus/blur events on hidden email input');
                    }, 100);
                }, 50);
                
                debugLog('✅ Triggered all email events on hidden input');
            }
            
            // Method 2: Try to call Hyros directly if available
            if (typeof window.hyros !== 'undefined' && window.hyros.trackEmail) {
                try {
                    window.hyros.trackEmail(email);
                    debugLog('✅ Called hyros.trackEmail() directly');
                } catch (e) {
                    debugLog('⚠️ hyros.trackEmail() failed: ' + e.message);
                }
            }
            
            // Method 3: Try UTS (Universal Tracking Script) direct call
            if (typeof window.UTS !== 'undefined' && window.UTS.trackEmail) {
                try {
                    window.UTS.trackEmail(email);
                    debugLog('✅ Called UTS.trackEmail() directly');
                } catch (e) {
                    debugLog('⚠️ UTS.trackEmail() failed: ' + e.message);
                }
            }
            
            // Method 4: Dispatch custom event
            var customEvent = new CustomEvent('hyros_email_detected', {
                detail: { email: email, source: 'snapengage_chat' }
            });
            document.dispatchEvent(customEvent);
            debugLog('✅ Dispatched custom hyros_email_detected event');
            
            // Method 5: Try postMessage to any Hyros frames
            var frames = document.querySelectorAll('iframe');
            frames.forEach(function(frame) {
                try {
                    frame.contentWindow.postMessage({
                        type: 'HYROS_EMAIL_DETECTED',
                        email: email,
                        source: 'snapengage_chat'
                    }, '*');
                } catch (e) {
                    // Ignore cross-origin errors
                }
            });
            
            debugLog('🎯 All Hyros email detection methods triggered');
            
        } catch (error) {
            debugLog('❌ Error triggering Hyros email detection: ' + error.message);
        }
    }
    
    // Enhanced email tracking with Hyros trigger
    function sendEmailToHyros(email, eventType, additionalData) {
        if (!config.hyrosIntegration || !email) return;
        
        debugLog('🎯 ENHANCED: Processing email for Hyros', {
            email: email, 
            eventType: eventType, 
            additionalData: additionalData
        });
        
        // FIRST: Trigger Hyros email detection
        triggerHyrosEmailDetection(email);
        
        // Wait a moment for Hyros to detect the email, then send the event
        setTimeout(function() {
            try {
                // Send pixel with email
                var pixelUrl = 'https://t.partygo.mx/v1/lst/universal-script';
                pixelUrl += '?ph=' + encodeURIComponent(config.hyrosPixelId);
                pixelUrl += '&email=' + encodeURIComponent(email);
                pixelUrl += '&event=' + encodeURIComponent(eventType);
                pixelUrl += '&source=snapengage_chat_triggered';
                pixelUrl += '&ref_url=' + encodeURIComponent(window.location.href);
                pixelUrl += '&ts=' + Date.now();
                
                if (additionalData) {
                    for (var key in additionalData) {
                        if (additionalData.hasOwnProperty(key)) {
                            pixelUrl += '&' + encodeURIComponent('custom_' + key) + '=' + 
                                      encodeURIComponent(additionalData[key]);
                        }
                    }
                }
                
                debugLog('🎯 Enhanced Pixel URL: ' + pixelUrl);
                
                // Create tracking pixel
                var img = document.createElement('img');
                img.style.display = 'none';
                img.style.position = 'absolute';
                img.style.left = '-9999px';
                
                img.onload = function() {
                    debugLog('✅ Enhanced Hyros pixel loaded successfully');
                    setTimeout(function() {
                        if (img.parentNode) img.parentNode.removeChild(img);
                    }, 2000);
                };
                
                img.onerror = function() {
                    debugLog('❌ Enhanced Hyros pixel failed to load');
                    setTimeout(function() {
                        if (img.parentNode) img.parentNode.removeChild(img);
                    }, 2000);
                };
                
                img.src = pixelUrl;
                document.body.appendChild(img);
                
                // Backup fetch call
                fetch(pixelUrl, { mode: 'no-cors' })
                    .then(function() {
                        debugLog('✅ Enhanced fetch backup completed');
                    })
                    .catch(function(error) {
                        debugLog('⚠️ Enhanced fetch backup failed: ' + error.message);
                    });
                
            } catch (error) {
                debugLog('❌ Error in enhanced email tracking: ' + error.message);
            }
        }, 500); // Wait 500ms for email detection to register
    }
    
    // Initialize hidden email input
    createHyrosEmailTrigger();
    
    // Add status indicator if requested
    if (config.showStatus) {
        addStatusIndicator();
    }
    
    loadSnapEngageWidget();
    
    function addStatusIndicator() {
        var statusDiv = document.createElement('div');
        statusDiv.id = 'snapengage-status-hyros-enhanced';
        statusDiv.innerHTML = config.isOnline ? 
            '<span style="color: #28a745;">● Chat Online + Enhanced Hyros Detection</span>' : 
            '<span style="color: #dc3545;">● Chat Offline</span>';
        
        statusDiv.style.cssText = 'position: fixed; top: 10px; right: 10px; background: rgba(255,255,255,0.95); padding: 8px 12px; border: 1px solid #dee2e6; border-radius: 20px; z-index: 10000; font-size: 12px; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; box-shadow: 0 2px 8px rgba(0,0,0,0.1);';
        
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function() {
                document.body.appendChild(statusDiv);
            });
        } else {
            document.body.appendChild(statusDiv);
        }
        
        setTimeout(function() {
            if (statusDiv.parentNode) {
                statusDiv.style.opacity = '0';
                statusDiv.style.transition = 'opacity 0.5s ease';
                setTimeout(function() {
                    if (statusDiv.parentNode) {
                        statusDiv.parentNode.removeChild(statusDiv);
                    }
                }, 500);
            }
        }, 8000);
    }
    
    function loadSnapEngageWidget() {
        debugLog('Loading SnapEngage widget script');
        
        var se = document.createElement('script');
        se.type = 'text/javascript';
        se.async = true;
        se.src = '//storage.googleapis.com/code.snapengage.com/js/' + config.apiKey + '.js';
        
        var done = false;
        se.onload = se.onreadystatechange = function() {
            if (!done && (!this.readyState || this.readyState === 'loaded' || this.readyState === 'complete')) {
                done = true;
                debugLog('SnapEngage script loaded, configuring...');
                setTimeout(configureSnapEngage, 100);
            }
        };
        
        se.onerror = function() {
            debugLog('Error loading SnapEngage script');
        };
        
        var s = document.getElementsByTagName('script')[0];
        s.parentNode.insertBefore(se, s);
    }
    
    function configureSnapEngage() {
        if (typeof SnapEngage === 'undefined') {
            debugLog('SnapEngage not available, retrying...');
            setTimeout(configureSnapEngage, 500);
            return;
        }
        
        debugLog('Configuring SnapEngage with enhanced Hyros integration');
        
        try {
            if (config.widgetId) {
                SnapEngage.setWidgetId(config.widgetId);
            }
            
            if (config.language && config.language !== 'en') {
                SnapEngage.setLocale(config.language);
            }
            
            if (config.userEmail) {
                SnapEngage.setUserEmail(config.userEmail);
                // Immediately trigger Hyros detection for pre-filled email
                triggerHyrosEmailDetection(config.userEmail);
            }
            
            if (config.userName) {
                SnapEngage.setUserName(config.userName);
            }
            
            SnapEngage.allowChatSound(true);
            SnapEngage.allowProactiveChat(true);
            
            setupEventHandlers();
            applyTheme(config.theme);
            
            debugLog('✅ SnapEngage configured with enhanced Hyros integration');
            
        } catch (error) {
            debugLog('❌ Error configuring SnapEngage: ' + error.message);
        }
    }
    
    function setupEventHandlers() {
        debugLog('Setting up enhanced event handlers for Hyros email detection');
        
        SnapEngage.setCallback('StartChat', function(email, msg, type) {
            debugLog('🎉 Chat started - ENHANCED Hyros email detection', {
                email: email, 
                message: msg, 
                type: type
            });
            
            if (config.hyrosIntegration && email && email.trim()) {
                debugLog('📧 Email detected, triggering ENHANCED Hyros detection: ' + email);
                
                sendEmailToHyros(email, 'chat_started', {
                    chat_type: type,
                    first_message: msg || '',
                    widget_id: config.widgetId || '',
                    user_agent: navigator.userAgent,
                    page_title: document.title,
                    timestamp: new Date().toISOString()
                });
            } else {
                debugLog('⚠️ No email provided or Hyros disabled - no tracking');
            }
            
            if (typeof gtag !== 'undefined') {
                gtag('event', 'chat_started', {
                    'event_category': 'engagement',
                    'event_label': type
                });
            }
            
            if (typeof window.onSnapEngageChatStart === 'function') {
                window.onSnapEngageChatStart(email, msg, type);
            }
        });
        
        SnapEngage.setCallback('ChatMessageReceived', function(agent, msg) {
            debugLog('Message received from: ' + agent);
            if (typeof window.onSnapEngageMessageReceived === 'function') {
                window.onSnapEngageMessageReceived(agent, msg);
            }
        });
        
        SnapEngage.setCallback('ChatMessageSent', function(msg) {
            debugLog('Message sent: ' + msg);
            if (typeof window.onSnapEngageMessageSent === 'function') {
                window.onSnapEngageMessageSent(msg);
            }
        });
        
        SnapEngage.setCallback('Close', function(type, status) {
            debugLog('Chat closed', {type: type, status: status});
            if (typeof window.onSnapEngageChatClose === 'function') {
                window.onSnapEngageChatClose(type, status);
            }
        });
    }
    
    function applyTheme(theme) {
        if (theme === 'minimal') {
            SnapEngage.hideButton();
            
            var trigger = document.createElement('div');
            trigger.innerHTML = '💬';
            trigger.title = 'Start Chat - Enhanced Hyros Email Detection';
            trigger.style.cssText = 'position: fixed; bottom: 20px; right: 20px; width: 60px; height: 60px; background: linear-gradient(135deg, #007cba, #005a8b); color: white; border-radius: 50%; text-align: center; line-height: 60px; cursor: pointer; z-index: 9999; font-size: 24px; box-shadow: 0 4px 20px rgba(0,124,186,0.3); transition: all 0.3s ease;';
            
            trigger.onclick = function() {
                debugLog('Custom trigger clicked - enhanced Hyros detection ready');
                SnapEngage.startLink();
            };
            
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', function() {
                    document.body.appendChild(trigger);
                });
            } else {
                document.body.appendChild(trigger);
            }
        }
    }
    
    // Enhanced utility functions
    window.SnapEngageUtils = {
        openChat: function(message) {
            debugLog('Opening chat with enhanced Hyros email detection');
            if (typeof SnapEngage !== 'undefined') {
                if (message) {
                    SnapEngage.startChat(message);
                } else {
                    SnapEngage.startLink();
                }
            }
        },
        
        triggerHyrosEmail: function(email) {
            triggerHyrosEmailDetection(email);
        },
        
        testHyrosDetection: function(email) {
            email = email || 'test-enhanced-' + Date.now() + '@example.com';
            debugLog('🧪 Testing enhanced Hyros email detection with: ' + email);
            triggerHyrosEmailDetection(email);
            return email;
        },
        
        isLoaded: function() {
            return typeof SnapEngage !== 'undefined';
        },
        
        config: config
    };
    
    debugLog('✅ Enhanced SnapEngage + Hyros email detection integration complete');
    
})();