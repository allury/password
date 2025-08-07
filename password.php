<?php
/**
 * ====================================================================
 * 安全密码生成器 - 单文件全栈应用 (PHP + JS)
 *
 * 描述:
 * 这是一个功能完整的安全密码生成工具。它整合了后端PHP逻辑
 * 用于安全地生成密码，以及一个现代、响应式的前端界面
 * 用于用户交互。
 *
 * @version    1.0.0 (正式版)
 * @author     编码助手
 * @lastupdate 2025-08-07
 * ====================================================================
 */

// --- 核心功能：安全地生成密码 ---

/**
 * 根据指定的标准生成一个加密安全的随机密码。
 *
 * @param int    $length            密码的目标长度。
 * @param bool   $include_uppercase 是否包含大写字母。
 * @param bool   $include_lowercase 是否包含小写字母。
 * @param bool   $include_numbers   是否包含数字。
 * @param bool   $include_symbols   是否包含特殊符号。
 * @return string                   生成的安全密码或错误信息。
 */
function generate_secure_password(int $length, bool $include_uppercase, bool $include_lowercase, bool $include_numbers, bool $include_symbols): string {
    // 定义各类字符集
    $lowercase_chars = 'abcdefghijklmnopqrstuvwxyz';
    $uppercase_chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $number_chars = '0123456789';
    $symbol_chars = '!@#$%^&*()-_=+[]{}|;:,.<>?';

    $char_pool = ''; // 用于填充密码剩余长度的字符池
    $password = '';  // 最终的密码

    // 步骤1: 确保每种选定的字符类型都至少在密码中出现一次
    // 这可以保证密码的复杂性，满足大多数网站的密码策略要求。
    if ($include_lowercase) {
        $char_pool .= $lowercase_chars;
        $password .= $lowercase_chars[random_int(0, strlen($lowercase_chars) - 1)];
    }
    if ($include_uppercase) {
        $char_pool .= $uppercase_chars;
        $password .= $uppercase_chars[random_int(0, strlen($uppercase_chars) - 1)];
    }
    if ($include_numbers) {
        $char_pool .= $number_chars;
        $password .= $number_chars[random_int(0, strlen($number_chars) - 1)];
    }
    if ($include_symbols) {
        $char_pool .= $symbol_chars;
        $password .= $symbol_chars[random_int(0, strlen($symbol_chars) - 1)];
    }

    // 如果用户未选择任何字符类型，则返回错误
    if (empty($char_pool)) {
        return '错误：请至少选择一种字符类型。';
    }

    // 检查密码长度是否足够容纳所有选定的字符类型
    $selected_types_count = ($include_uppercase + $include_lowercase + $include_numbers + $include_symbols);
    if ($length < $selected_types_count) {
        return '错误：密码长度不能小于所选字符类型的数量。';
    }

    // 步骤2: 使用完整的字符池随机填充密码的剩余部分
    $remaining_length = $length - strlen($password);
    if ($remaining_length > 0) {
        $pool_length = strlen($char_pool) - 1;
        for ($i = 0; $i < $remaining_length; $i++) {
            // 使用 random_int() 来确保加密级别的安全性，防止可预测的密码
            $password .= $char_pool[random_int(0, $pool_length)];
        }
    }
    
    // 步骤3: 打乱密码字符串，避免初始字符的位置是固定的
    return str_shuffle($password);
}

// --- 页面逻辑：处理用户输入和显示 ---

// 初始化密码选项的默认值
$generated_password = '';
$options = [
    'length' => $_POST['length'] ?? 16,
    'lowercase' => isset($_POST['form_submitted']) ? isset($_POST['lowercase']) : true,
    'uppercase' => isset($_POST['form_submitted']) ? isset($_POST['uppercase']) : true,
    'numbers' => isset($_POST['form_submitted']) ? isset($_POST['numbers']) : true,
    'symbols' => isset($_POST['form_submitted']) ? isset($_POST['symbols']) : true,
];

// 判断请求类型：是AJAX请求还是常规的表单提交
// AJAX请求用于无刷新更新密码，提升用户体验
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest' &&
    $_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $generated_password = generate_secure_password(
        (int)($_POST['length'] ?? 16),
        (bool)($_POST['uppercase'] ?? false),
        (bool)($_POST['lowercase'] ?? false),
        (bool)($_POST['numbers'] ?? false),
        (bool)($_POST['symbols'] ?? false)
    );
    
    // 以JSON格式返回结果给前端JavaScript
    header('Content-Type: application/json');
    echo json_encode(['password' => $generated_password]);
    exit;
}
// 常规表单提交用于在JavaScript被禁用的情况下也能正常工作
else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $generated_password = generate_secure_password(
        (int)$options['length'],
        (bool)$options['uppercase'],
        (bool)$options['lowercase'],
        (bool)$options['numbers'],
        (bool)$options['symbols']
    );
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- [优化] 更新页面标题 -->
    <title>安全密码生成器</title>
    <style>
        /* --- 引入外部字体 --- */
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        
        /* --- CSS变量定义 (浅色与深色主题) --- */
        :root {
            --bg-color: #f0f2f5; --card-bg-color: rgba(255, 255, 255, 0.65); --text-color: #1f2937; --text-color-light: #6b7280; --border-color: rgba(0, 0, 0, 0.08); --primary-color: #3b82f6; --primary-color-hover: #2563eb; --success-color: #10b981; --success-color-hover: #059669; --input-bg-color: rgba(255, 255, 255, 0.8); --switch-bg-color: #e5e7eb; --switch-handle-color: #ffffff; --font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; --border-radius: 16px; --shadow: 0 10px 25px -5px rgba(0,0,0,0.1), 0 4px 6px -1px rgba(0,0,0,0.05); --backdrop-blur: blur(12px); --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        body.dark-mode {
            --bg-color: #111827; --card-bg-color: rgba(31, 41, 55, 0.65); --text-color: #f9fafb; --text-color-light: #9ca3af; --border-color: rgba(255, 255, 255, 0.1); --primary-color: #60a5fa; --primary-color-hover: #3b82f6; --success-color: #34d399; --success-color-hover: #10b981; --input-bg-color: rgba(55, 65, 81, 0.8); --switch-bg-color: #4b5563; --switch-handle-color: #e5e7eb; --shadow: 0 10px 25px -5px rgba(0,0,0,0.2), 0 4px 6px -1px rgba(0,0,0,0.1);
        }

        /* --- 全局与基础样式 --- */
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { background-color: var(--bg-color); background-image: radial-gradient(circle at 1% 1%, var(--primary-color), transparent 25%), radial-gradient(circle at 99% 99%, var(--success-color), transparent 25%); display: flex; justify-content: center; align-items: center; min-height: 100vh; padding: 20px; font-family: var(--font-family); color: var(--text-color); transition: var(--transition); }
        .container { width: 100%; max-width: 480px; background: var(--card-bg-color); border-radius: var(--border-radius); box-shadow: var(--shadow); border: 1px solid var(--border-color); backdrop-filter: var(--backdrop-blur); -webkit-backdrop-filter: var(--backdrop-blur); padding: 32px; transition: var(--transition); }
        
        /* --- 头部与主题切换器 --- */
        .header { text-align: center; margin-bottom: 25px; }
        .header h1 { font-size: 28px; color: var(--text-color); font-weight: 700; margin-bottom: 8px; display: flex; align-items: center; justify-content: center; gap: 10px; }
        .header p { color: var(--text-color-light); font-size: 16px; margin-top: 5px; margin-bottom: 20px; }
        .theme-toggle { display: inline-flex; background-color: var(--input-bg-color); border-radius: 99px; padding: 4px; border: 1px solid var(--border-color); }
        .theme-toggle button { background: none; border: none; padding: 6px 16px; cursor: pointer; font-weight: 600; border-radius: 99px; color: var(--text-color-light); transition: var(--transition); }
        .theme-toggle button.active { background: var(--primary-color); color: white; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        
        /* --- 密码显示与操作按钮 --- */
        #result { width: 100%; padding: 14px 16px; border: 1px solid var(--border-color); border-radius: 12px; font-size: 20px; background: var(--input-bg-color); word-break: break-all; height: 80px; resize: none; overflow-y: auto; color: var(--text-color); transition: var(--transition); font-family: 'Courier New', Courier, monospace; display: flex; align-items: center; justify-content: center; text-align: center; }
        #result:focus { outline: none; border-color: var(--primary-color); box-shadow: 0 0 0 3px var(--primary-color-hover-bg, rgba(59, 130, 246, 0.2)); }
        body.dark-mode #result:focus { box-shadow: 0 0 0 3px rgba(96, 165, 250, 0.3); }
        .password-actions { display: flex; gap: 12px; margin-top: 15px; }
        .btn { flex: 1; padding: 12px 15px; border-radius: 12px; font-weight: 600; font-size: 16px; cursor: pointer; transition: var(--transition); display: flex; align-items: center; justify-content: center; gap: 8px; border: none; box-shadow: 0 1px 2px rgba(0,0,0,0.05); }
        .btn:active { transform: scale(0.97); }
        .btn-copy { background-color: var(--success-color); color: white; }
        .btn-copy:hover { background-color: var(--success-color-hover); transform: translateY(-2px); }
        .btn-generate { background-color: var(--primary-color); color: white; }
        .btn-generate:hover { background-color: var(--primary-color-hover); transform: translateY(-2px); }
        
        /* --- 自定义选项区域 --- */
        .options-section { margin-top: 30px; }
        .options-title { font-size: 18px; font-weight: 600; margin-bottom: 18px; color: var(--text-color); text-align: center; }
        .switch-container { display: flex; align-items: center; justify-content: space-between; background: var(--input-bg-color); border: 1px solid var(--border-color); border-radius: 12px; padding: 14px; margin-bottom: 12px; transition: var(--transition); }
        .switch { position: relative; display: inline-block; width: 50px; height: 26px; }
        .switch input { opacity: 0; width: 0; height: 0; }
        .slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: var(--switch-bg-color); transition: .4s cubic-bezier(0.25, 0.8, 0.25, 1); border-radius: 26px; }
        .slider:before { position: absolute; content: ""; height: 20px; width: 20px; left: 3px; bottom: 3px; background-color: var(--switch-handle-color); transition: .4s cubic-bezier(0.25, 0.8, 0.25, 1); border-radius: 50%; box-shadow: 0 1px 3px rgba(0,0,0,0.2); }
        input:checked + .slider { background-color: var(--primary-color); }
        input:checked + .slider:before { transform: translateX(24px); }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-weight: 500; margin-bottom: 10px; font-size: 16px; text-align: center; }
        .length-control { display: flex; align-items: center; background: var(--input-bg-color); border: 1px solid var(--border-color); border-radius: 12px; padding: 4px; }
        .btn-decrement, .btn-increment { width: 44px; height: 44px; background: transparent; border: none; border-radius: 8px; font-size: 24px; font-weight: 400; cursor: pointer; display: flex; align-items: center; justify-content: center; color: var(--text-color-light); transition: var(--transition); }
        .btn-decrement:hover, .btn-increment:hover { background: var(--switch-bg-color); color: var(--text-color); }
        .length-input { flex-grow: 1; text-align: center; font-weight: 700; font-size: 20px; border: none; background: transparent; color: var(--text-color); padding: 0 8px; }
        .length-input:focus { outline: none; }
        input[type=number]::-webkit-inner-spin-button, input[type=number]::-webkit-outer-spin-button { -webkit-appearance: none; margin: 0; }
        input[type=number] { -moz-appearance: textfield; }
        
        /* --- 密码强度指示器 --- */
        .strength-meter { margin-top: 20px; border-radius: 12px; padding: 12px 15px; display: flex; align-items: center; background: var(--input-bg-color); border: 1px solid var(--border-color); }
        .strength-label { font-weight: 500; font-size: 14px; }
        .indicator { flex-grow: 1; height: 8px; background: var(--switch-bg-color); border-radius: 8px; overflow: hidden; margin: 0 12px; }
        .progress { height: 100%; border-radius: 8px; transition: width 0.4s ease, background-color 0.4s ease, box-shadow 0.4s ease; --strength-color: #e0e0e0; box-shadow: 0 0 8px 1px var(--strength-color); }
        
        /* --- 响应式设计 --- */
        @media (max-width: 500px) { .container { padding: 20px; } .header h1 { font-size: 24px; } .password-actions { flex-direction: column; } }
        
        /* --- 动画效果 --- */
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    </style>
</head>
<body>
    <!-- 主容器 -->
    <div class="container">
        
        <!-- 头部区域 -->
        <header class="header">
            <!-- [优化] 更新主标题 -->
            <h1>安全密码生成器</h1>
            <p>快速创建高强度、可自定义的随机密码</p>
            <div class="theme-toggle">
                <button id="light-theme">☀️ 浅色</button>
                <button id="dark-theme">🌙 深色</button>
            </div>
        </header>
        
        <!-- 密码显示与操作区域 -->
        <div class="password-display">
            <textarea id="result" rows="1" readonly placeholder="点击“生成”按钮创建密码"><?php echo htmlspecialchars($generated_password); ?></textarea>
            <div class="password-actions">
                <button class="btn btn-copy" id="copyBtn" aria-live="polite"><span class="state-default" style="display: inline-flex; align-items: center; gap: 8px;"><svg width="18" height="18" viewBox="0 0 24 24"><path fill="currentColor" d="M19,21H8V7H19M19,5H8A2,2 0 0,0 6,7V21A2,2 0 0,0 8,23H19A2,2 0 0,0 21,21V7A2,2 0 0,0 19,5M16,1H4A2,2 0 0,0 2,3V17H4V3H16V1Z"/></svg><span>复制密码</span></span><span class="state-success" style="display: none; align-items: center; gap: 8px;"><svg width="18" height="18" viewBox="0 0 24 24"><path fill="currentColor" d="M21,7L9,19L3.5,13.5L4.91,12.09L9,16.17L19.59,5.59L21,7Z"/></svg><span>已复制!</span></span></button>
                <button class="btn btn-generate" id="generateBtn"><span class="state-default" style="display: inline-flex; align-items: center; gap: 8px;"><svg width="18" height="18" viewBox="0 0 24 24"><path fill="currentColor" d="M17.65,6.35C16.2,4.9 14.21,4 12,4A8,8 0 0,0 4,12A8,8 0 0,0 12,20C15.73,20 18.84,17.45 19.73,14H17.65C16.83,16.33 14.61,18 12,18A6,6 0 0,1 6,12A6,6 0 0,1 12,6C13.66,6 15.14,6.69 16.22,7.78L13,11H20V4L17.65,6.35Z"/></svg><span>生成</span></span><span class="state-loading" style="display: none; align-items: center; gap: 8px;"><svg width="18" height="18" viewBox="0 0 24 24" style="animation: spin 1s linear infinite;"><path fill="currentColor" d="M12,4V2A10,10 0 0,0 2,12H4A8,8 0 0,1 12,4Z"/></svg><span>生成中...</span></span></button>
            </div>
        </div>
        
        <!-- 选项表单区域 -->
        <div class="options-section">
            <h2 class="options-title">自定义选项</h2>
            <form id="passwordForm" method="post">
                <input type="hidden" name="form_submitted" value="1">
                <div class="form-group"><label for="length">密码长度</label><div class="length-control"><button class="btn-decrement" type="button" id="decrement">-</button><input type="number" id="length" name="length" class="length-input" min="8" max="50" value="<?php echo (int)$options['length']; ?>"><button class="btn-increment" type="button" id="increment">+</button></div></div>
                <div class="switch-container"><label for="lowercase">小写字母 (a-z)</label><label class="switch"><input type="checkbox" name="lowercase" id="lowercase" <?php if ($options['lowercase']) echo 'checked'; ?>><span class="slider"></span></label></div>
                <div class="switch-container"><label for="uppercase">大写字母 (A-Z)</label><label class="switch"><input type="checkbox" name="uppercase" id="uppercase" <?php if ($options['uppercase']) echo 'checked'; ?>><span class="slider"></span></label></div>
                <div class="switch-container"><label for="numbers">数字 (0-9)</label><label class="switch"><input type="checkbox" name="numbers" id="numbers" <?php if ($options['numbers']) echo 'checked'; ?>><span class="slider"></span></label></div>
                <div class="switch-container"><label for="symbols">特殊符号 (!@#...)</label><label class="switch"><input type="checkbox" name="symbols" id="symbols" <?php if ($options['symbols']) echo 'checked'; ?>><span class="slider"></span></label></div>
                <div class="strength-meter"><span class="strength-label">密码强度:</span><div class="indicator"><div class="progress" id="strengthIndicator"></div></div><span class="strength-text" id="strengthText">-</span></div>
            </form>
        </div>
    </div>

    <script>
        /**
         * ====================================================================
         * 前端交互逻辑
         * ====================================================================
         */
        document.addEventListener('DOMContentLoaded', () => {
            // --- DOM元素获取 ---
            const getEl = (id) => document.getElementById(id);
            const passwordForm = getEl('passwordForm'), resultTextarea = getEl('result'),
                  copyBtn = getEl('copyBtn'), generateBtn = getEl('generateBtn'),
                  lengthInput = getEl('length'), decrementBtn = getEl('decrement'),
                  incrementBtn = getEl('increment'), strengthIndicator = getEl('strengthIndicator'),
                  strengthText = getEl('strengthText'), body = document.body,
                  lightThemeBtn = getEl('light-theme'), darkThemeBtn = getEl('dark-theme');

            // --- 主题管理 ---
            const setTheme = (theme) => {
                body.className = theme === 'dark' ? 'dark-mode' : '';
                localStorage.setItem('theme', theme);
                lightThemeBtn.classList.toggle('active', theme === 'light');
                darkThemeBtn.classList.toggle('active', theme === 'dark');
            };
            lightThemeBtn.addEventListener('click', () => setTheme('light'));
            darkThemeBtn.addEventListener('click', () => setTheme('dark'));
            setTheme(localStorage.getItem('theme') || 'light'); // 优先从本地存储加载主题

            // --- 核心功能函数 ---

            /**
             * 异步生成密码并更新UI
             */
            function generatePassword() {
                const defaultState = generateBtn.querySelector('.state-default');
                const loadingState = generateBtn.querySelector('.state-loading');
                
                // 切换到加载状态
                defaultState.style.display = 'none'; 
                loadingState.style.display = 'inline-flex';
                
                // 发送AJAX请求到后端
                fetch('', { 
                    method: 'POST', 
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/x-www-form-urlencoded' }, 
                    body: new URLSearchParams(new FormData(passwordForm)) 
                })
                .then(response => response.json())
                .then(data => {
                    resultTextarea.value = data.password;
                    updateStrengthIndicator(data.password);
                })
                .catch(error => { console.error('密码生成失败:', error); })
                .finally(() => {
                    // 无论成功或失败，都恢复按钮的默认状态
                    defaultState.style.display = 'inline-flex'; 
                    loadingState.style.display = 'none';
                });
            }

            /**
             * 根据密码评估其强度并更新UI指示器
             * @param {string} password - 需要评估的密码
             */
            function updateStrengthIndicator(password) {
                if (!password || password.includes('错误')) { password = ""; }
                
                // 评估标准：字符类型数量 + 长度加分
                const hasLowercase = /[a-z]/.test(password), hasUppercase = /[A-Z]/.test(password), hasNumbers = /[0-9]/.test(password), hasSymbols = /[^a-zA-Z0-9]/.test(password);
                const typeCount = [hasLowercase, hasUppercase, hasNumbers, hasSymbols].filter(Boolean).length;
                let score = 0;
                if(password.length > 0) { 
                    score = typeCount + (password.length >= 12 ? 1 : 0) + (password.length >= 16 ? 1 : 0); 
                }
                
                // 根据得分更新UI
                let percentage = 0, color = '#e74c3c', text = '很弱';
                switch(score) {
                    case 0: case 1: percentage = 20; color = '#e74c3c'; text = '很弱'; break;
                    case 2: percentage = 40; color = '#f39c12'; text = '中等'; break;
                    case 3: percentage = 60; color = '#3498db'; text = '强'; break;
                    case 4: percentage = 80; color = '#27ae60'; text = '很强'; break;
                    case 5: case 6: percentage = 100; color = '#27ae60'; text = '极强'; break;
                }
                if (password.length === 0) { percentage = 0; text = '-'; color = '#e0e0e0'; }
                
                strengthIndicator.style.width = `${percentage}%`;
                strengthIndicator.style.backgroundColor = color;
                strengthText.textContent = text;
                strengthText.style.color = color;
                strengthIndicator.style.setProperty('--strength-color', color);
            }

            // --- 事件监听器绑定 ---

            // 复制按钮
            copyBtn.addEventListener('click', function() {
                if (!resultTextarea.value) return;
                navigator.clipboard.writeText(resultTextarea.value).then(() => {
                    const defaultState = this.querySelector('.state-default');
                    const successState = this.querySelector('.state-success');
                    defaultState.style.display = 'none';
                    successState.style.display = 'inline-flex';
                    setTimeout(() => {
                        defaultState.style.display = 'inline-flex';
                        successState.style.display = 'none';
                    }, 2000);
                }).catch(err => { console.error('无法复制密码: ', err); });
            });
            
            // 生成按钮
            generateBtn.addEventListener('click', (e) => { e.preventDefault(); generatePassword(); });
            
            // 长度控制器
            decrementBtn.addEventListener('click', () => { const currentLength = parseInt(lengthInput.value); if (currentLength > parseInt(lengthInput.min)) { lengthInput.value = currentLength - 1; generatePassword(); } });
            incrementBtn.addEventListener('click', () => { const currentLength = parseInt(lengthInput.value); if (currentLength < parseInt(lengthInput.max)) { lengthInput.value = currentLength + 1; generatePassword(); } });
            lengthInput.addEventListener('change', () => { let len = parseInt(lengthInput.value); const min = parseInt(lengthInput.min); const max = parseInt(lengthInput.max); if (isNaN(len) || len < min) { len = min; } else if (len > max) { len = max; } lengthInput.value = len; generatePassword(); });
            
            // 字符类型选项
            ['lowercase', 'uppercase', 'numbers', 'symbols'].forEach(option => { getEl(option).addEventListener('change', generatePassword); });
            
            // --- 初始化 ---
            // 如果页面加载时已有密码（例如，在非JS环境下提交表单后），则更新强度指示器
            if (resultTextarea.value && resultTextarea.value !== '') {
                updateStrengthIndicator(resultTextarea.value);
            }
        });
    </script>
</body>
</html>
