<?php

// 卡立得PHP+mysql通用卡券系统物流领取版 V2024.12.12
// 演示地址: http://kaquan.chalide.cn
// 文件路径: user.php
// 文件大小: 14611 字节
// 最后修改时间: 2024-12-17 20:57:05
// 作者: yujianyue
// 邮件: 15058593138@qq.com
// 版权所有,保留发行权和署名权

// 卡立得PHP+mysql通用卡券系统物流领取版 V2024.12.12
// 演示地址: http://kaquan.chalide.cn
// 文件路径: user.php
// 文件大小: 14322 字节
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
if (isset($_GET['act']) && $_GET['act'] == 'query') {
    $code = safe_string($_POST['code']);
    
    // 查询卡券
    $sql = "SELECT * FROM cards WHERE code = '{$code}'";
    $card = $db->get_one($sql);
    
    if (!$card) {
        json_response(0, '卡券不存在');
    }
    
    // 获取状态文本
    $status_text = [
        '-1' => '已作废',
        '0' => '未使用',
        '1' => '已绑定待发货',
        '2' => '已发货'
    ][$card['status']];
    
    // 组装地址
    $address = $card['status'] >= 1 ? implode(' ', [
        $card['province'],
        $card['city'],
        $card['county'],
        $card['address']
    ]) : '';
    
    // 组装快递信息
    $express = $card['status'] == 2 ? [
        'company' => $card['express'],
        'number' => $card['express_no'],
        'time' => $card['ship_time']
    ] : null;
    
    json_response(1, 'success', [
        'status' => $status_text,
        'receiver' => $card['receiver'],
        'mobile' => $card['mobile'],
        'address' => $address,
        'express' => $express
    ]);
    exit;
}

// 获取网站配置
$config = $db->get_one("SELECT site_name, site_desc FROM site_config WHERE id=1");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $config['site_name']; ?> - 卡券查询</title>
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
            margin: 0;
            padding: 20px;
        }

        .container {
            width: 61vw;
            min-width: 360px;
            min-height: 88vh;
            margin: 0 auto;
        }

        .card {
            background: white;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            overflow: hidden;
            margin-bottom: 20px;
        }

        .card-header {
            background: var(--primary);
            color: white;
            padding: 20px;
            text-align: center;
            position: relative;
        }

        .card-title {
            font-size: 24px;
            font-weight: 600;
            margin: 0;
        }

        .card-body {
            padding: 30px;
        }

        .site-desc {
            background: rgba(255, 255, 255, 0.8);
            padding: 20px;
            border-radius: var(--radius);
            margin-bottom: 30px;
            line-height: 1.8;
            color: var(--dark);
        }

        .search-box {
            display: flex;
            gap: 10px;
            margin-bottom: 30px;
        }

        .search-input {
            flex: 1;
            padding: 12px 16px;
            border: 2px solid var(--light);
            border-radius: var(--radius);
            font-size: 16px;
            transition: var(--transition);
        }

        .search-input:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }

        .search-btn {
            padding: 12px 24px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: var(--radius);
            font-size: 16px;
            cursor: pointer;
            transition: var(--transition);
        }

        .search-btn:hover {
            background: #2980b9;
            transform: translateY(-1px);
        }

        .result-box {
            display: none;
            animation: slideDown 0.3s ease-out;
        }

        .result-item {
            padding: 15px;
            border-bottom: 1px solid var(--light);
        }

        .result-item:last-child {
            border-bottom: none;
        }

        .result-label {
            font-weight: 500;
            color: #666;
            margin-bottom: 5px;
        }

        .result-value {
            color: var(--dark);
        }

        .status-tag {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
            color: white;
        }

        .status-unused { background: var(--dark); }
        .status-bound { background: var(--warning); color: var(--dark); }
        .status-shipped { background: var(--success); }
        .status-invalid { background: var(--danger); }

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
            width: 40px;
            height: 40px;
            border: 3px solid var(--light);
            border-top: 3px solid var(--primary);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        /* 提示消息 */
        .toast {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 12px 24px;
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

        .toast.success::before { background: var(--success); }
        .toast.error::before { background: var(--danger); }

        /* 动画 */
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* 响应式设计 */
        @media (max-width: 640px) {
            body {
                padding: 15px;
            }

            .search-box {
                flex-direction: column;
            }

            .search-btn {
                width: 100%;
            }

            .card-body {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- 网站说明 -->
        <div class="card">
            <div class="card-header">
                <h1 class="card-title"><?php echo $config['site_name']; ?></h1>
            </div>
            <div class="card-body">
                <!-- 查询表单 -->
                <div class="search-box">
                    <input type="text" class="search-input" id="codeInput" 
                           placeholder="请输入卡券码" maxlength="32">
                    <button class="search-btn" onclick="queryCard()">
                        <i class="fas fa-search"></i> 查询
                    </button>
                </div>
                <div class="site-desc">
                    <?php echo nl2br(htmlspecialchars($config['site_desc'])); ?>
                </div>
                <!-- 查询结果 -->
                <div class="result-box" id="resultBox">
                    <div class="result-item">
                        <div class="result-label">卡券状态</div>
                        <div class="result-value" id="statusValue"></div>
                    </div>

                    <div class="result-item" id="receiverBox" style="display:none">
                        <div class="result-label">收件人</div>
                        <div class="result-value" id="receiverValue"></div>
                    </div>

                    <div class="result-item" id="mobileBox" style="display:none">
                        <div class="result-label">联系电话</div>
                        <div class="result-value" id="mobileValue"></div>
                    </div>

                    <div class="result-item" id="addressBox" style="display:none">
                        <div class="result-label">收货地址</div>
                        <div class="result-value" id="addressValue"></div>
                    </div>

                    <div class="result-item" id="expressBox" style="display:none">
                        <div class="result-label">快递信息</div>
                        <div class="result-value" id="expressValue"></div>
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
    <script>
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

        // 查询卡券
        function queryCard() {
            const code = document.getElementById('codeInput').value.trim();
            if (!code) {
                showToast('请输入卡券码', 'error');
                return;
            }

            toggleLoading(true);
            ajax({
                url: '?act=query',
                method: 'POST',
                data: { code },
                success: function(res) {
                    toggleLoading(false);
                    if (res.status === 1) {
                        showResult(res.data);
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

        // 显示查询结果
        function showResult(data) {
            // 显示状态
            const statusClass = {
                '未使用': 'status-unused',
                '已绑定待发货': 'status-bound',
                '已发货': 'status-shipped',
                '已作废': 'status-invalid'
            }[data.status];
            
            document.getElementById('statusValue').innerHTML = 
                `<span class="status-tag ${statusClass}">${data.status}</span>`;
            
            // 显示收件信息
            const receiverBox = document.getElementById('receiverBox');
            const mobileBox = document.getElementById('mobileBox');
            const addressBox = document.getElementById('addressBox');
            const expressBox = document.getElementById('expressBox');
            
            if (data.receiver) {
                receiverBox.style.display = 'block';
                document.getElementById('receiverValue').textContent = data.receiver;
                
                mobileBox.style.display = 'block';
                document.getElementById('mobileValue').textContent = data.mobile;
                
                addressBox.style.display = 'block';
                document.getElementById('addressValue').textContent = data.address;
            } else {
                receiverBox.style.display = 'none';
                mobileBox.style.display = 'none';
                addressBox.style.display = 'none';
            }
            
            // 显示快递信息
            if (data.express) {
                expressBox.style.display = 'block';
                document.getElementById('expressValue').innerHTML = 
                    `${data.express.company}: ${data.express.number}<br>` +
                    `发货时间: ${data.express.time}`;
            } else {
                expressBox.style.display = 'none';
            }
            
            // 显示结果区域
            document.getElementById('resultBox').style.display = 'block';
        }

        // 绑定回车事件
        document.getElementById('codeInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                queryCard();
            }
        });
    </script>
</body>
</html>