<?php

// 卡立得PHP+mysql通用卡券系统物流领取版 V2024.12.12
// 演示地址: http://kaquan.chalide.cn
// 文件路径: user.addr.php
// 文件大小: 22835 字节
// 最后修改时间: 2024-12-17 20:57:05
// 作者: yujianyue
// 邮件: 15058593138@qq.com
// 版权所有,保留发行权和署名权

// 卡立得PHP+mysql通用卡券系统物流领取版 V2024.12.12
// 演示地址: http://kaquan.chalide.cn
// 文件路径: user.addr.php
// 文件大小: 22541 字节
// 最后修改时间: 2024-12-10 16:02:45
// 作者: yujianyue
// 邮件: 15058593138@qq.com
// 版权所有,保留发行权和署名权
require_once 'inc/conn.php';
require_once 'inc/pubs.php';
require_once 'inc/sqls.php';

// 初始化数据库操作类
$db = new Database($conn);


// 处理AJAX请求
if (isset($_GET['act'])) {
    switch ($_GET['act']) {
        case 'check':
            // 检查卡券是否可用
            $code = safe_string($_POST['code']);
            $sql = "SELECT status FROM cards WHERE code = '{$code}'";
            $card = $db->get_one($sql);
            
            if (!$card) {
                json_response(0, '卡券不存在');
            }
            
            if ($card['status'] != 0) {
                json_response(0, '该卡券已被使用或已作废');
            }
            
            json_response(1, '卡券可用');
            break;
            
        case 'save':
            // 保存收货地址
            $code = safe_string($_POST['code']);
            $data = [
                'receiver' => safe_string($_POST['receiver']),
                'mobile' => safe_string($_POST['mobile']),
                'province' => safe_string($_POST['province']),
                'city' => safe_string($_POST['city']),
                'county' => safe_string($_POST['county']),
                'address' => safe_string($_POST['address']),
                'status' => 1,
                'bind_time' => date('Y-m-d H:i:s')
            ];
            
            // 验证手机号
            if (!preg_match('/^1[3-9]\d{9}$/', $data['mobile'])) {
                json_response(0, '请输入正确的手机号');
            }
            
            // 检查卡券状态
            $sql = "SELECT status FROM cards WHERE code = '{$code}'";
            $card = $db->get_one($sql);
            
            if (!$card) {
                json_response(0, '卡券不存在');
            }
            
            if ($card['status'] != 0) {
                json_response(0, '该卡券已被使用或已作废');
            }
            
            // 更新数据
            if ($db->update('cards', $data, "code = '{$code}'")) {
                json_response(1, '地址保存成功');
            } else {
                json_response(0, '地址保存失败');
            }
            break;
    }
    exit;
}


// 获取网站配置
$config = $db->get_one("SELECT site_name FROM site_config WHERE id=1");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $config['site_name']; ?> - 填写收货地址</title>
    <link rel="stylesheet" href="inc/css/css.css">
    <style>
        :root {
            --primary: #3498db;
            --success: #2ecc71;
            --warning: #f1c40f;
            --danger: #e74c3c;
            --dark: #2c3e50;
            --light: #ecf0f1;
            --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            --radius: 12px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #f6f8fa 0%, #e9ecef 100%);
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            color: var(--dark);
            line-height: 1.6;
        }

        .container {
            width: 61.8%;
            min-height: 88vh;
            min-width: 360px;
            max-width: 720px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .card {
            background: white;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            overflow: hidden;
            position: relative;
        }

        .card-header {
            background: var(--primary);
            color: white;
            padding: 20px;
            text-align: center;
            position: relative;
        }

        .card-header::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            border-left: 10px solid transparent;
            border-right: 10px solid transparent;
            border-top: 10px solid var(--primary);
        }

        .card-title {
            font-size: 24px;
            font-weight: 600;
            margin: 0;
        }

        .card-body {
            padding: 30px;
        }

        /* 步骤指示器 */
        .steps {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
            position: relative;
        }

        .step-item {
            flex: 1;
            text-align: center;
            position: relative;
            padding: 0 20px;
        }

        .step-number {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: var(--light);
            color: var(--dark);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
            font-weight: bold;
            position: relative;
            z-index: 1;
            transition: var(--transition);
        }

        .step-title {
            font-size: 14px;
            color: var(--dark);
            opacity: 0.7;
            transition: var(--transition);
        }

        .step-item.active .step-number {
            background: var(--primary);
            color: white;
            transform: scale(1.2);
        }

        .step-item.active .step-title {
            color: var(--primary);
            opacity: 1;
        }

        .steps::before {
            content: '';
            position: absolute;
            top: 18px;
            left: 20%;
            right: 20%;
            height: 2px;
            background: var(--light);
            z-index: 0;
        }

        /* 表单样式 */
        .form-step {
            display: none;
            animation: slideIn 0.3s ease-out;
        }

        .form-step.active {
            display: block;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--dark);
        }

        .form-control {
            width: 100%;
            padding: 8px 6px;
            border: 2px solid var(--light);
            border-radius: var(--radius);
            font-size: 16px;
            transition: var(--transition);
            background: white;
        }

        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
            outline: none;
        }

        .form-tip {
            font-size: 13px;
            color: #666;
            margin-top: 6px;
        }

        /* 地址选择网格 */
        .address-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }

        /* 按钮样式 */
        .btn-group {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }

        .btn {
            flex: 1;
            padding: 14px 24px;
            border: none;
            border-radius: var(--radius);
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .btn::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .btn:active::after {
            width: 300px;
            height: 300px;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
        }

        .btn-secondary {
            background: var(--light);
            color: var(--dark);
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow);
        }

        /* 加载动画 */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.9);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transition: var(--transition);
        }

        .loading-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .loading-spinner {
            width: 50px;
            height: 50px;
            border: 4px solid var(--light);
            border-top-color: var(--primary);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        /* 提示消息 */
        .toast {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 25px;
            background: white;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            transform: translateX(120%);
            transition: var(--transition);
            z-index: 1000;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .toast.show {
            transform: translateX(0);
        }

        .toast::before {
            content: '';
            width: 20px;
            height: 20px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .toast.success::before {
            background: var(--success);
        }

        .toast.error::before {
            background: var(--danger);
        }

        /* 动画 */
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* 响应式设计 */
        @media (max-width: 768px) {
            .container {
                margin: 20px auto;
                padding: 0 15px;
            }

            .card-body {
                padding: 20px;
            }

            .address-grid {
                grid-template-columns: 1fr;
            }

            .btn-group {
                flex-direction: column;
            }

            .steps {
                flex-direction: column;
                gap: 20px;
            }

            .steps::before {
                display: none;
            }

            .step-item {
                display: flex;
                align-items: center;
                gap: 15px;
            }

            .step-number {
                margin: 0;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h1 class="card-title">填写收货地址</h1>
            </div>
            
            <div class="card-body">
                <!-- 步骤指示器 -->
                <div class="steps">
                    <div class="step-item active" id="step1-indicator">
                        <div class="step-number">1</div>
                        <div class="step-title">验证卡券</div>
                    </div>
                    <div class="step-item" id="step2-indicator">
                        <div class="step-number">2</div>
                        <div class="step-title">填写地址</div>
                    </div>
                </div>

                <!-- 步骤1：验证卡券 -->
                <div class="form-step active" id="step1">
                    <div class="form-group">
                        <label class="form-label">卡券码</label>
                        <input type="text" class="form-control" id="codeInput" 
                               placeholder="请输入卡券码" maxlength="32">
                        <div class="form-tip">请输入需要填写收货地址的卡券码</div>
                    </div>

                    <div class="btn-group">
                        <button class="btn btn-primary" onclick="verifyCode()">
                            下一步 <span class="btn-arrow">→</span>
                        </button>
                    </div>
                </div>

                <!-- 步骤2：填写地址 -->
                <div class="form-step" id="step2">
                    <div class="form-group">
                        <label class="form-label">收件人</label>
                        <input type="text" class="form-control" id="receiverInput" 
                               placeholder="请输入收件人姓名">
                    </div>

                    <div class="form-group">
                        <label class="form-label">手机号码</label>
                        <input type="tel" class="form-control" id="mobileInput" 
                               placeholder="请输入11位手机号码">
                    </div>

                    <label class="form-label">所在地区</label>
                    <div class="address-grid">
                        <select class="form-control" id="provinceSelect">
                            <option value="">选择省份</option>
                        </select>
                        <select class="form-control" id="citySelect" disabled>
                            <option value="">选择城市</option>
                        </select>
                        <select class="form-control" id="countySelect" disabled>
                            <option value="">选择区县</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">详细地址</label>
                        <textarea class="form-control" id="addressInput" rows="3" 
                                  placeholder="请输入详细地址，如街道名称、门牌号等"></textarea>
                    </div>

                    <div class="btn-group">
                        <button class="btn btn-secondary" onclick="prevStep()">
                            <span class="btn-arrow">←</span> 返回修改
                        </button>
                        <button class="btn btn-primary" onclick="submitAddress()">
                            确认提交
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 加载遮罩 -->
    <div class="loading-overlay" id="loading">
        <div class="loading-spinner"></div>
    </div>

    <!-- 提示消息 -->
    <div class="toast" id="toast"></div>

    <script src="inc/js/js.js"></script>
    <script src="inc/js/addr.js"></script>
    <script>
        let currentCode = '';
        // 初始化地址选择器
        function initAddressSelector() {
            const provinceSelect = document.getElementById('provinceSelect');
            const citySelect = document.getElementById('citySelect');
            const countySelect = document.getElementById('countySelect');
            
            // 填充省份选项
            getProvinces().forEach(province => {
                const option = document.createElement('option');
                option.value = province;
                option.textContent = province;
                provinceSelect.appendChild(option);
            });
            
            // 省份变化时更新城市
            provinceSelect.onchange = function() {
                citySelect.innerHTML = '<option value="">请选择城市</option>';
                countySelect.innerHTML = '<option value="">请选择区县</option>';
                
                if (this.value) {
                    getCities(this.value).forEach(city => {
                        const option = document.createElement('option');
                        option.value = city;
                        option.textContent = city;
                        citySelect.appendChild(option);
                    });
                    citySelect.disabled = false;
                    countySelect.disabled = true;
                } else {
                    citySelect.disabled = true;
                    countySelect.disabled = true;
                }
            };
            
            // 城市变化时更新区县
            citySelect.onchange = function() {
                countySelect.innerHTML = '<option value="">请选择区县</option>';
                
                if (this.value) {
                    getCounties(provinceSelect.value, this.value).forEach(county => {
                        const option = document.createElement('option');
                        option.value = county;
                        option.textContent = county;
                        countySelect.appendChild(option);
                    });
                    countySelect.disabled = false;
                } else {
                    countySelect.disabled = true;
                }
            };
        }

        // 显示提示消息
        function showToast(message, type = 'success') {
            const toast = document.getElementById('toast');
            toast.textContent = message;
            toast.className = `toast ${type} show`;
            
            setTimeout(() => {
                toast.className = 'toast';
            }, 3000);
        }

        // 显示/隐藏加载动画
        function toggleLoading(show) {
            document.getElementById('loading').className = 
                'loading-overlay' + (show ? ' active' : '');
        }

        // 验证卡券
        function verifyCode() {
            const code = document.getElementById('codeInput').value.trim();
            if (!code) {
                showToast('请输入卡券码', 'error');
                return;
            }

            toggleLoading(true);
            ajax({
                url: '?act=check',
                method: 'POST',
                data: { code },
                success: function(res) {
                    toggleLoading(false);
                    if (res.status === 1) {
                        currentCode = code;
                        goToStep(2);
                    } else {
                        showToast(res.message, 'error');
                    }
                },
                error: function() {
                    toggleLoading(false);
                    showToast('网络请求失败，请重试', 'error');
                }
            });
        }

        // 提交地址
        function submitAddress() {
            const data = {
                code: currentCode,
                receiver: document.getElementById('receiverInput').value.trim(),
                mobile: document.getElementById('mobileInput').value.trim(),
                province: document.getElementById('provinceSelect').value,
                city: document.getElementById('citySelect').value,
                county: document.getElementById('countySelect').value,
                address: document.getElementById('addressInput').value.trim()
            };

            // 验证数据
            if (Object.values(data).some(v => !v)) {
                showToast('请填写完整信息', 'error');
                return;
            }

            if (!/^1[3-9]\d{9}$/.test(data.mobile)) {
                showToast('请输入正确的手机号码', 'error');
                return;
            }

            if (!confirm('确认提交收货地址？提交后将无法修改')) {
                return;
            }

            toggleLoading(true);
            ajax({
                url: '?act=save',
                method: 'POST',
                data: data,
                success: function(res) {
                    toggleLoading(false);
                    showToast(res.message, res.status === 1 ? 'success' : 'error');
                    if (res.status === 1) {
                        setTimeout(() => {
                            window.location.href = 'yonghu.php';
                        }, 1500);
                    }
                },
                error: function() {
                    toggleLoading(false);
                    showToast('保存失败，请重试', 'error');
                }
            });
        }

        // 切换步骤
        function goToStep(step) {
            // 更新步骤指示器
            document.querySelectorAll('.step-item').forEach((el, index) => {
                el.className = `step-item${index + 1 === step ? ' active' : ''}`;
            });

            // 切换表单步骤
            document.querySelectorAll('.form-step').forEach((el, index) => {
                el.className = `form-step${index + 1 === step ? ' active' : ''}`;
            });
        }

        function prevStep() {
            goToStep(1);
        }

        // 初始化
        document.addEventListener('DOMContentLoaded', function() {
            // 初始化地址选择器
            initAddressSelector();

            // 绑定回车事件
            document.getElementById('codeInput').addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    verifyCode();
                }
            });
        });
    </script>
</body>
</html>