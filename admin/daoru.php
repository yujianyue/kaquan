<?php

// 卡立得PHP+mysql通用卡券系统物流领取版 V2024.12.12
// 演示地址: http://kaquan.chalide.cn
// 文件路径: admin/daoru.php
// 文件大小: 7401 字节
// 最后修改时间: 2024-12-17 20:57:05
// 作者: yujianyue
// 邮件: 15058593138@qq.com
// 版权所有,保留发行权和署名权

// 卡立得PHP+mysql通用卡券系统物流领取版 V2024.12.12
// 演示地址: http://kaquan.chalide.cn
// 文件路径: admin/daoru.php
// 文件大小: 7106 字节
// 最后修改时间: 2024-12-07 08:11:29
// 作者: yujianyue
// 邮件: 15058593138@qq.com
// 版权所有,保留发行权和署名权
require_once '../inc/conn.php';
require_once '../inc/pubs.php';
require_once '../inc/sqls.php';

// 检查登录状态
check_admin_login();

// 初始化数据库操作类
$db = new Database($conn);

// 处理AJAX请求
if (isset($_GET['act']) && $_GET['act'] == 'import') {
    $batch = safe_string($_POST['batch']);
    $codes = explode("\n", trim($_POST['codes']));
    
    // 过滤空行和重复行
    $codes = array_unique(array_filter($codes, function($code) {
        return trim($code) !== '';
    }));
    
    if (empty($codes)) {
        json_response(0, '请输入有效的卡券码');
    }
    
    if (count($codes) > 10000) {
        json_response(0, '单次最多导入10000个卡券码');
    }
    
    // 检查格式
    foreach ($codes as $code) {
        if (!preg_match('/^[A-Z0-9]{8,32}$/', trim($code))) {
            json_response(0, '卡券码格式错误：' . $code);
        }
    }
    
    // 检查是否已存在
    $existing_codes = [];
    foreach ($codes as $code) {
        $code = trim($code);
        $sql = "SELECT code FROM cards WHERE code = '{$code}'";
        if ($db->get_one($sql)) {
            $existing_codes[] = $code;
        }
    }
    
    if (!empty($existing_codes)) {
        json_response(0, '以下卡券码已存在：' . implode(', ', $existing_codes));
    }
    
    // 开始导入
    $success = 0;
    $failed = 0;
    
    foreach ($codes as $code) {
        $code = trim($code);
        $data = [
            'batch' => $batch,
            'code' => $code,
            'status' => 0,
            'create_time' => date('Y-m-d H:i:s')
        ];
        
        if ($db->insert('cards', $data)) {
            $success++;
        } else {
            $failed++;
        }
    }
    
    json_response(1, "成功导入{$success}张卡券，失败{$failed}张");
    exit;
}
?>

<?php require_once '../inc/header.php'; ?>

<div class="page-title">批量导入卡券</div>

<div class="form-container">
    <form id="importForm">
        <div class="form-group">
            <label>批次号：</label>
            <input type="text" name="batch" required placeholder="请输入批次号"  value="D<?php echo $pitime;?>">
            <div class="form-tip">用于区分不同批次的卡券</div>
        </div>
        
        <div class="form-group">
            <label>卡券码：</label>
            <textarea name="codes" rows="15" required placeholder="请输入卡券，每行一个"></textarea>
            <div class="form-tip">
                格式要求：<br>
                1. 每行一个卡券码<br>
                2. 卡券码只能包含大写字母和数字<br>
                3. 长度在8-32位之间<br>
                4. 单次最多导入10000个
            </div>
        </div>
        
        <div class="form-buttons">
            <button type="submit" class="btn-primary">开始导入</button>
            <button type="button" class="btn-secondary" onclick="window.location.href='mlist.php'">返回列表</button>
        </div>
    </form>
</div>

<style>
.page-title {
    font-size: 24px;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 1px solid #eee;
}

.form-container {
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
    background: #fff;
    border-radius: 5px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
}

.form-group input,
.form-group textarea {
    width: 100%;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 3px;
    font-size: 14px;
    font-family: monospace;
}

.form-group textarea {
    resize: vertical;
    min-height: 200px;
}

.form-tip {
    margin-top: 5px;
    color: #666;
    font-size: 12px;
    line-height: 1.6;
}

.form-buttons {
    margin-top: 30px;
    text-align: center;
}

.btn-primary,
.btn-secondary {
    padding: 10px 20px;
    border: none;
    border-radius: 3px;
    cursor: pointer;
    font-size: 16px;
    margin: 0 5px;
}

.btn-primary {
    background: #007bff;
    color: white;
}

.btn-secondary {
    background: #6c757d;
    color: white;
}

.btn-primary:hover {
    background: #0056b3;
}

.btn-secondary:hover {
    background: #5a6268;
}

/* 加载中遮罩 */
.loading-mask {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255,255,255,0.8);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
}

.loading-content {
    text-align: center;
}

.loading-spinner {
    border: 4px solid #f3f3f3;
    border-top: 4px solid #3498db;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    animation: spin 1s linear infinite;
    margin: 0 auto 10px;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>

<script>
// 显示加载中遮罩
function showLoading() {
    const mask = document.createElement('div');
    mask.className = 'loading-mask';
    mask.innerHTML = `
        <div class="loading-content">
            <div class="loading-spinner"></div>
            <div>正在导入数据，请稍候...</div>
        </div>
    `;
    document.body.appendChild(mask);
}

// 隐藏加载中遮罩
function hideLoading() {
    const mask = document.querySelector('.loading-mask');
    if (mask) {
        mask.remove();
    }
}

// 表单提交处理
document.getElementById('importForm').onsubmit = function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const codes = formData.get('codes').trim().split('\n');
    const validCodes = codes.filter(code => code.trim() !== '');
    
    if (validCodes.length === 0) {
        alert('请输入卡券码');
        return;
    }
    
    if (validCodes.length > 10000) {
        alert('单次最多导入10000个卡券码');
        return;
    }
    
    // 检查格式
    const invalidCodes = validCodes.filter(code => !(/^[A-Z0-9]{8,32}$/.test(code.trim())));
    if (invalidCodes.length > 0) {
        alert('以下卡券码格式错误：\n' + invalidCodes.join('\n'));
        return;
    }
    
    if (!confirm(`确定要导入 ${validCodes.length} 个卡券码吗？`)) {
        return;
    }
    
    showLoading();
    
    ajax({
        url: 'daoru.php?act=import',
        method: 'POST',
        data: Object.fromEntries(formData),
        success: function(res) {
            hideLoading();
            alert(res.message);
            if (res.status === 1) {
                window.location.href = 'mlist.php';
            }
        },
        error: function() {
            hideLoading();
            alert('导入请求失败');
        }
    });
};
</script>

<?php require_once '../inc/footer.php'; ?> 