<?php
/**
 * REALISTIC TYPING: SnapEngage + Hyros - Mimics Real User Email Input
 * 
 * This version simulates exactly how a real user types an email
 */

// Configuration
$config = [
    'api_key' => 'xxxx-xxxxx-xxxx-xxxx',
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
 * REALISTIC TYPING: SnapEngage + Hyros - Mimics Real User Email Input
 * Generated: <?php echo date('c'); ?>
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
    
    // Track processed emails to prevent loops
    var processedEmails = new Set();
    var isSimulatingTyping = false;
    
    function debugLog(message, data) {
        if (config.debug && typeof console !== 'undefined') {
            console.log('[SnapEngage-Realistic] ' + message, data || '');
        }
    }
    
    debugLog('🎯 REALISTIC: Loading SnapEngage with realistic typing simulation', config);
    
    if (window.SnapEngageIntegrationLoaded) {
        debugLog('Already loaded, skipping...');
        return;
    }
    window.SnapEngageIntegrationLoaded = true;
    
    // Create hidden input for Hyros detection
    function createHyrosEmailInput() {
        if (!config.hyrosIntegration) return;
        
        debugLog('🎯 Creating realistic email input for Hyros detection');
        
        var hiddenForm = document.createElement('form');
        hiddenForm.style.cssText = 'position: absolute; left: -9999px; opacity: 0; pointer-events: none; height: 1px; width: 1px; overflow: hidden;';
        hiddenForm.id = 'hyros-email-trigger-form';
        
        var hiddenEmailInput = document.createElement('input');
        hiddenEmailInput.type = 'email';
        hiddenEmailInput.name = 'email';
        hiddenEmailInput.id = 'hyros-email-trigger-input';
        hiddenEmailInput.placeholder = 'email@example.com';
        hiddenEmailInput.style.cssText = 'width: 1px; height: 1px; opacity: 0;';
        hiddenEmailInput.setAttribute('data-hyros-input', 'true');
        
        hiddenForm.appendChild(hiddenEmailInput);
        
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function() {
                document.body.appendChild(hiddenForm);
                debugLog('✅ Realistic email input created');
            });
        } else {
            document.body.appendChild(hiddenForm);
            debugLog('✅ Realistic email input created');
        }
        
        return hiddenEmailInput;
    }
    
    // REALISTIC: Simulate real user typing behavior
    function simulateRealisticTyping(email) {
        if (!config.hyrosIntegration || !email || isSimulatingTyping) return;
        
        var emailKey = email.toLowerCase().trim();
        
        // Skip invalid emails
        if (emailKey.includes('fromwsdsdsd') || 
            emailKey.includes('dddddd') || 
            emailKey.length > 100 ||
            emailKey.length < 5 ||
            !emailKey.includes('@') ||
            !emailKey.includes('.')) {
            debugLog('⚠️ REALISTIC: Invalid email, skipping: ' + email);
            return;
        }
        
        // Skip if already processed
        if (processedEmails.has(emailKey)) {
            debugLog('⚠️ REALISTIC: Email already processed, skipping: ' + email);
            return;
        }
        
        debugLog('🎯 REALISTIC: Starting realistic typing simulation for: ' + email);
        
        isSimulatingTyping = true;
        processedEmails.add(emailKey);
        
        var hiddenInput = document.getElementById('hyros-email-trigger-input');
        if (!hiddenInput) {
            debugLog('❌ REALISTIC: Hidden input not found');
            isSimulatingTyping = false;
            return;
        }
        
        try {
            // Step 1: Focus the input (user clicks)
            debugLog('🎯 REALISTIC: Step 1 - Focus input');
            hiddenInput.focus();
            
            // Step 2: Clear any existing value
            hiddenInput.value = '';
            
            // Step 3: Simulate typing each character with realistic timing
            var characters = email.split('');
            var currentValue = '';
            
            function typeNextCharacter(index) {
                if (index >= characters.length) {
                    // Typing complete - trigger final events
                    setTimeout(function() {
                        debugLog('🎯 REALISTIC: Step 4 - Typing complete, triggering final events');
                        
                        // Final input event
                        var inputEvent = new Event('input', { bubbles: true, cancelable: true });
                        hiddenInput.dispatchEvent(inputEvent);
                        
                        // Change event
                        setTimeout(function() {
                            var changeEvent = new Event('change', { bubbles: true, cancelable: true });
                            hiddenInput.dispatchEvent(changeEvent);
                            
                            // Final blur (user finished typing)
                            setTimeout(function() {
                                hiddenInput.blur();
                                debugLog('🎯 REALISTIC: Step 5 - Blur (user finished)');
                                
                                // Reset simulation lock
                                setTimeout(function() {
                                    isSimulatingTyping = false;
                                    debugLog('✅ REALISTIC: Typing simulation complete');
                                }, 1000);
                            }, 200);
                        }, 100);
                    }, 300);
                    return;
                }
                
                // Add next character
                currentValue += characters[index];
                hiddenInput.value = currentValue;
                
                // Trigger keydown
                var keydownEvent = new KeyboardEvent('keydown', {
                    key: characters[index],
                    code: 'Key' + characters[index].toUpperCase(),
                    bubbles: true,
                    cancelable: true
                });
                hiddenInput.dispatchEvent(keydownEvent);
                
                // Trigger input event
                var inputEvent = new Event('input', { bubbles: true, cancelable: true });
                hiddenInput.dispatchEvent(inputEvent);
                
                // Trigger keyup
                var keyupEvent = new KeyboardEvent('keyup', {
                    key: characters[index],
                    code: 'Key' + characters[index].toUpperCase(),
                    bubbles: true,
                    cancelable: true
                });
                hiddenInput.dispatchEvent(keyupEvent);
                
                debugLog('🎯 REALISTIC: Typed "' + characters[index] + '" (current: "' + currentValue + '")');
                
                // Continue with next character after realistic delay
                var delay = Math.random() * 150 + 50; // 50-200ms between keystrokes
                setTimeout(function() {
                    typeNextCharacter(index + 1);
                }, delay);
            }
            
            // Start typing simulation after focus
            setTimeout(function() {
                debugLog('🎯 REALISTIC: Step 3 - Starting character-by-character typing');
                typeNextCharacter(0);
            }, 100);
            
        } catch (error) {
            debugLog('❌ REALISTIC: Error in typing simulation: ' + error.message);
            isSimulatingTyping = false;
        }
    }
    
    // Send email to Hyros with tracking
    function sendEmailToHyros(email, eventType, additionalData) {
        if (!config.hyrosIntegration || !email) return;
        
        debugLog('🎯 REALISTIC: Processing email for Hyros', {
            email: email, 
            eventType: eventType, 
            additionalData: additionalData
        });
        
        // Start realistic typing simulation
        simulateRealisticTyping(email);
        
        // Send tracking pixel after typing simulation
        setTimeout(function() {
            try {
                var pixelUrl = 'https://t.partygo.mx/v1/lst/universal-script';
                pixelUrl += '?ph=' + encodeURIComponent(config.hyrosPixelId);
                pixelUrl += '&email=' + encodeURIComponent(email);
                pixelUrl += '&event=' + encodeURIComponent(eventType);
                pixelUrl += '&source=snapengage_realistic';
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
                
                debugLog('🎯 Realistic Pixel URL: ' + pixelUrl);
                
                var img = document.createElement('img');
                img.style.display = 'none';
                img.style.position = 'absolute';
                img.style.left = '-9999px';
                
                img.onload = function() {
                    debugLog('✅ Realistic Hyros pixel loaded successfully');
                    setTimeout(function() {
                        if (img.parentNode) img.parentNode.removeChild(img);
                    }, 2000);
                };
                
                img.onerror = function() {
                    debugLog('❌ Realistic Hyros pixel failed to load');
                    setTimeout(function() {
                        if (img.parentNode) img.parentNode.removeChild(img);
                    }, 2000);
                };
                
                img.src = pixelUrl;
                document.body.appendChild(img);
                
            } catch (error) {
                debugLog('❌ Error in realistic email tracking: ' + error.message);
            }
        }, 3000); // Wait for typing simulation to complete
    }
    
    // Initialize
    createHyrosEmailInput();
    
    if (config.showStatus) {
        addStatusIndicator();
    }
    
    loadSnapEngageWidget();
    
    function addStatusIndicator() {
        var statusDiv = document.createElement('div');
        statusDiv.id = 'snapengage-status-realistic';
        statusDiv.innerHTML = config.isOnline ? 
            '<span style="color: #28a745;">● Chat Online + Realistic Typing Detection</span>' : 
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
        
        debugLog('Configuring SnapEngage with REALISTIC typing simulation');
        
        try {
            if (config.widgetId) {
                SnapEngage.setWidgetId(config.widgetId);
            }
            
            if (config.language && config.language !== 'en') {
                SnapEngage.setLocale(config.language);
            }
            
            if (config.userEmail) {
                SnapEngage.setUserEmail(config.userEmail);
                // Trigger realistic typing for pre-filled email
                simulateRealisticTyping(config.userEmail);
            }
            
            if (config.userName) {
                SnapEngage.setUserName(config.userName);
            }
            
            SnapEngage.allowChatSound(true);
            SnapEngage.allowProactiveChat(true);
            
            setupEventHandlers();
            applyTheme(config.theme);
            
            debugLog('✅ SnapEngage configured with REALISTIC typing simulation');
            
        } catch (error) {
            debugLog('❌ Error configuring SnapEngage: ' + error.message);
        }
    }
    
    function setupEventHandlers() {
        debugLog('Setting up REALISTIC event handlers');
        
        // Primary chat start handler
        SnapEngage.setCallback('StartChat', function(email, msg, type) {
            debugLog('🎉 Chat started - REALISTIC typing simulation', {
                email: email, 
                message: msg, 
                type: type
            });
            
            if (config.hyrosIntegration && email && email.trim()) {
                debugLog('📧 Valid email detected, starting REALISTIC typing: ' + email);
                
                sendEmailToHyros(email, 'chat_started', {
                    chat_type: type,
                    first_message: msg || '',
                    widget_id: config.widgetId || '',
                    user_agent: navigator.userAgent,
                    page_title: document.title,
                    timestamp: new Date().toISOString()
                });
            } else {
                debugLog('⚠️ No valid email provided - no realistic typing');
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
        
        // Monitor SnapEngage's setUserEmail
        if (SnapEngage.setUserEmail) {
            var originalSetUserEmail = SnapEngage.setUserEmail;
            SnapEngage.setUserEmail = function(email) {
                debugLog('📧 REALISTIC: SnapEngage.setUserEmail called with: ' + email);
                
                var result = originalSetUserEmail.call(this, email);
                
                if (config.hyrosIntegration && email && email.trim()) {
                    var emailKey = email.toLowerCase().trim();
                    if (!processedEmails.has(emailKey)) {
                        debugLog('🎯 REALISTIC: New email via setUserEmail, starting typing: ' + email);
                        setTimeout(function() {
                            simulateRealisticTyping(email);
                        }, 500);
                    }
                }
                
                return result;
            };
        }
        
        // Other standard callbacks
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
            trigger.title = 'Start Chat - Realistic Typing Detection';
            trigger.style.cssText = 'position: fixed; bottom: 20px; right: 20px; width: 60px; height: 60px; background: linear-gradient(135deg, #007cba, #005a8b); color: white; border-radius: 50%; text-align: center; line-height: 60px; cursor: pointer; z-index: 9999; font-size: 24px; box-shadow: 0 4px 20px rgba(0,124,186,0.3); transition: all 0.3s ease;';
            
            trigger.onclick = function() {
                debugLog('Custom trigger clicked - realistic typing ready');
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
    
    // Utility functions
    window.SnapEngageUtils = {
        openChat: function(message) {
            debugLog('Opening chat with realistic typing detection');
            if (typeof SnapEngage !== 'undefined') {
                if (message) {
                    SnapEngage.startChat(message);
                } else {
                    SnapEngage.startLink();
                }
            }
        },
        
        triggerHyrosEmail: function(email) {
            simulateRealisticTyping(email);
        },
        
        testHyrosDetection: function(email) {
            email = email || 'test-realistic-' + Date.now() + '@example.com';
            debugLog('🧪 Testing realistic typing detection with: ' + email);
            simulateRealisticTyping(email);
            return email;
        },
        
        isLoaded: function() {
            return typeof SnapEngage !== 'undefined';
        },
        
        config: config,
        
        getProcessedEmails: function() {
            return Array.from(processedEmails);
        },
        
        resetProcessedEmails: function() {
            processedEmails.clear();
            isSimulatingTyping = false;
            debugLog('🔄 Reset processed emails cache');
        }
    };
    
    debugLog('✅ REALISTIC SnapEngage + Hyros typing simulation integration complete');
    
})();