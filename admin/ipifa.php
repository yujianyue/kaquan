<?php

// 卡立得PHP+mysql通用卡券系统物流领取版 V2024.12.12
// 演示地址: http://kaquan.chalide.cn
// 文件路径: admin/ipifa.php
// 文件大小: 8162 字节
// 最后修改时间: 2024-12-17 20:57:05
// 作者: yujianyue
// 邮件: 15058593138@qq.com
// 版权所有,保留发行权和署名权

// 卡立得PHP+mysql通用卡券系统物流领取版 V2024.12.12
// 演示地址: http://kaquan.chalide.cn
// 文件路径: admin/ipifa.php
// 文件大小: 7867 字节
// 最后修改时间: 2024-12-07 06:26:48
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
if (isset($_GET['act']) && $_GET['act'] == 'ship') {
    $data = $_POST['data'];
    $lines = explode("\n", trim($data));
    
    $success = 0;
    $failed = 0;
    $errors = [];
    
    foreach ($lines as $line) {
        // 分割制表符分隔的数据
        $fields = explode("\t", trim($line));
        
        // 检查数据格式
        if (count($fields) !== 3) {
            $errors[] = "数据格式错误: {$line}";
            continue;
        }
        
        list($code, $express, $express_no) = array_map('trim', $fields);
        
        // 查找卡券
        $sql = "SELECT id, status FROM cards WHERE code = '" . safe_string($code) . "'";
        $card = $db->get_one($sql);
        
        if (!$card) {
            $errors[] = "卡券不存在: {$code}";
            $failed++;
            continue;
        }
        
        if ($card['status'] != 1) {
            $errors[] = "卡券状态错误: {$code}";
            $failed++;
            continue;
        }
        
        // 更新发货信息
        $update_data = [
            'express' => safe_string($express),
            'express_no' => safe_string($express_no),
            'status' => 2,
            'ship_time' => date('Y-m-d H:i:s')
        ];
        
        if ($db->update('cards', $update_data, "id = {$card['id']}")) {
            $success++;
        } else {
            $failed++;
            $errors[] = "更新失败: {$code}";
        }
    }
    
    json_response(1, "处理完成：成功 {$success} 条，失败 {$failed} 条", [
        'errors' => $errors
    ]);
    exit;
}
?>

<?php require_once '../inc/header.php'; ?>

<div class="page-title">批量发货</div>

<div class="form-container">
    <form id="shipForm">
        <div class="form-group">
            <label>发货数据：</label>
            <textarea name="data" rows="15" required placeholder="请粘贴从Excel复制的数据，格式：卡券码&#9;快递公司&#9;快递单号"></textarea>
            <div class="form-tip">
                数据格式说明：<br>
                1. 从Excel复制的数据，包含3列：卡券码、快递公司、快递单号<br>
                2. 列之间用制表符(Tab)分隔<br>
                3. 每行一条数据<br>
                4. 只能处理"已绑定未发货"状态的卡券
            </div>
        </div>
        
        <div class="form-buttons">
            <button type="submit" class="btn-primary">开始处理</button>
            <button type="button" class="btn-secondary" onclick="window.location.href='mlist.php'">返回列表</button>
        </div>
    </form>
</div>

<!-- 结果弹窗 -->
<div id="resultModal" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h3>处理结果</h3>
        <div class="result-content"></div>
        <div class="modal-buttons">
            <button onclick="document.getElementById('resultModal').style.display='none'">关闭</button>
        </div>
    </div>
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

.form-group textarea {
    width: 100%;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 3px;
    font-size: 14px;
    font-family: monospace;
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

/* 结果显示样式 */
.result-content {
    max-height: 400px;
    overflow-y: auto;
    margin: 15px 0;
    padding: 10px;
    background: #f8f9fa;
    border-radius: 3px;
}

.error-list {
    color: #dc3545;
    margin-top: 10px;
    padding-left: 20px;
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
            <div>正在处理数据，请稍候...</div>
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

// 显示结果
function showResult(message, errors) {
    const modal = document.getElementById('resultModal');
    const content = modal.querySelector('.result-content');
    
    let html = `<p>${message}</p>`;
    if (errors && errors.length > 0) {
        html += '<div class="error-list">';
        html += '<p>详细错误信息：</p>';
        html += '<ul>';
        errors.forEach(error => {
            html += `<li>${error}</li>`;
        });
        html += '</ul>';
        html += '</div>';
    }
    
    content.innerHTML = html;
    modal.style.display = 'block';
}

// 表单提交处理
document.getElementById('shipForm').onsubmit = function(e) {
    e.preventDefault();
    
    const data = this.data.value.trim();
    if (!data) {
        alert('请输入发货数据');
        return;
    }
    
    const lines = data.split('\n').filter(line => line.trim());
    if (!confirm(`确定要处理 ${lines.length} 条发货数据吗？`)) {
        return;
    }
    
    showLoading();
    
    ajax({
        url: 'ipifa.php?act=ship',
        method: 'POST',
        data: { data: data },
        success: function(res) {
            hideLoading();
            if (res.status === 1) {
                showResult(res.message, res.data.errors);
            } else {
                alert(res.message);
            }
        },
        error: function() {
            hideLoading();
            alert('处理请求失败');
        }
    });
};

// 关闭结果弹窗
document.querySelector('#resultModal .close').onclick = function() {
    document.getElementById('resultModal').style.display = 'none';
};
</script>

<?php require_once '../inc/footer.php'; ?> 