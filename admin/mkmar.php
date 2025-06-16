<?php

// 卡立得PHP+mysql通用卡券系统物流领取版 V2024.12.12
// 演示地址: http://kaquan.chalide.cn
// 文件路径: admin/mkmar.php
// 文件大小: 6311 字节
// 最后修改时间: 2024-12-17 20:57:05
// 作者: yujianyue
// 邮件: 15058593138@qq.com
// 版权所有,保留发行权和署名权

// 卡立得PHP+mysql通用卡券系统物流领取版 V2024.12.12
// 演示地址: http://kaquan.chalide.cn
// 文件路径: admin/mkmar.php
// 文件大小: 6016 字节
// 最后修改时间: 2024-12-07 08:10:27
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
if (isset($_GET['act']) && $_GET['act'] == 'generate') {
    $batch = safe_string($_POST['batch']);
    $count = intval($_POST['count']);
    $length = intval($_POST['length']);
    
    if ($count <= 0 || $count > 10000) {
        json_response(0, '生成数量必须在1-10000之间');
    }
    
    if ($length < 8 || $length > 32) {
        json_response(0, '卡券码长度必须在8-32之间');
    }
    
    // 开始生成卡券
    $success = 0;
    $failed = 0;
    $codes = [];
    
    // 生成指定数量的卡券码
    for ($i = 0; $i < $count; $i++) {
        do {
            $code = generate_coupon_code($length);
        } while (in_array($code, $codes)); // 确保不重复
        
        $codes[] = $code;
        
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
    
    json_response(1, "成功生成{$success}张卡券，失败{$failed}张");
    exit;
}
?>

<?php require_once '../inc/header.php'; ?>

<div class="page-title">生成新卡券</div>

<div class="form-container">
    <form id="generateForm">
        <div class="form-group">
            <label>批次号：</label>
            <input type="text" name="batch" required placeholder="请输入批次号" value="M<?php echo $pitime;?>">
            <div class="form-tip">用于区分不同批次的卡券</div>
        </div>
        
        <div class="form-group">
            <label>生成数量：</label>
            <input type="number" name="count" required min="1" max="10000" value="100">
            <div class="form-tip">单次最多可生成10000张卡券</div>
        </div>
        
        <div class="form-group">
            <label>卡券码长度：</label>
            <input type="number" name="length" required min="8" max="32" value="12">
            <div class="form-tip">卡券码长度范围：8-32位</div>
        </div>
        
        <div class="form-buttons">
            <button type="submit" class="btn-primary">开始生成</button>
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
    max-width: 600px;
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

.form-group input {
    width: 100%;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 3px;
}

.form-tip {
    margin-top: 5px;
    color: #666;
    font-size: 12px;
}

.form-buttons {
    margin-top: 30px;
    text-align: center;
}

.btn-primary {
    padding: 10px 20px;
    background: #007bff;
    color: white;
    border: none;
    border-radius: 3px;
    cursor: pointer;
    margin-right: 10px;
}

.btn-secondary {
    padding: 10px 20px;
    background: #6c757d;
    color: white;
    border: none;
    border-radius: 3px;
    cursor: pointer;
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
            <div>正在生成卡券，请稍候...</div>
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
document.getElementById('generateForm').onsubmit = function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const count = parseInt(formData.get('count'));
    
    if (count > 10000) {
        alert('单次最多生成10000张卡券');
        return;
    }
    
    if (!confirm(`确定要生成 ${count} 张卡券吗？`)) {
        return;
    }
    
    showLoading();
    
    ajax({
        url: 'mkmar.php?act=generate',
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
            alert('生成请求失败');
        }
    });
};
</script>

<?php require_once '../inc/footer.php'; ?> 