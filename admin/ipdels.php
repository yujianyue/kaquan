<?php

// 卡立得PHP+mysql通用卡券系统物流领取版 V2024.12.12
// 演示地址: http://kaquan.chalide.cn
// 文件路径: admin/ipdels.php
// 文件大小: 6005 字节
// 最后修改时间: 2024-12-17 20:57:05
// 作者: yujianyue
// 邮件: 15058593138@qq.com
// 版权所有,保留发行权和署名权

// 卡立得PHP+mysql通用卡券系统物流领取版 V2024.12.12
// 演示地址: http://kaquan.chalide.cn
// 文件路径: admin/ipdels.php
// 文件大小: 5709 字节
// 最后修改时间: 2024-12-10 19:33:10
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
if (isset($_GET['act'])) {
    switch ($_GET['act']) {
        case 'batches':
            // 获取所有批次
            $sql = "SELECT DISTINCT batch FROM cards WHERE status >= 0 ORDER BY batch DESC";
            $batches = $db->get_all($sql);
            json_response(1, 'success', $batches);
            break;
            
        case 'delete':
            $batch = safe_string($_POST['batch']);
            $status = isset($_POST['status']) ? intval($_POST['status']) : '';
            
            if (empty($batch)) {
                json_response(0, '请选择批次');
            }
            
            // 构建条件
            $where = "batch = '{$batch}'";
            if ($status !== '') {
                $where .= " AND status = {$status}";
            }
            
            // 先获取数量
            $count = $db->get_one("SELECT COUNT(*) as count FROM cards WHERE {$where}")['count'];
            if ($count === 0) {
                json_response(0, '没有符合条件的卡券');
            }
            
            // 执行删除
            if ($db->update('cards', ['status' => -1], $where)) {
                json_response(1, "成功删除 {$count} 张卡券");
            } else {
                json_response(0, '删除失败');
            }
            break;
    }
    exit;
}
?>

<?php require_once '../inc/header.php'; ?>

<div class="form-container">
    <div class="warning-box">
        <h3>⚠️ 警告</h3>
        <p>此操作将批量删除卡券，删除后不可恢复，请谨慎操作！</p>
    </div>

    <form id="deleteForm">
        <div class="form-group">
            <label>选择批次：</label>
            <select name="batch" id="batchSelect" required>
                <option value="">请选择批次</option>
            </select>
        </div>
        
        <div class="form-group">
            <label>选择状态：</label>
            <select name="status">
                <option value="">全部状态</option>
                <option value="0">未绑定</option>
                <option value="1">已绑定未发货</option>
                <option value="2">已发货</option>
            </select>
            <div class="form-tip">不选择状态则删除该批次下的所有卡券</div>
        </div>
        
        <div class="form-buttons">
            <button type="submit" class="btn-danger">确认删除</button>
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
    margin: 20px auto;
    padding: 20px;
    background: white;
    border-radius: 5px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

.warning-box {
    background: #fff3cd;
    border: 1px solid #ffeeba;
    color: #856404;
    padding: 15px;
    border-radius: 5px;
    margin-bottom: 20px;
}

.warning-box h3 {
    margin: 0 0 10px 0;
}

.warning-box p {
    margin: 0;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
}

.form-group select {
    width: 100%;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 3px;
    font-size: 14px;
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

.btn-danger,
.btn-secondary {
    padding: 10px 20px;
    border: none;
    border-radius: 3px;
    cursor: pointer;
    font-size: 16px;
    margin: 0 5px;
}

.btn-danger {
    background: #dc3545;
    color: white;
}

.btn-secondary {
    background: #6c757d;
    color: white;
}

.btn-danger:hover {
    background: #c82333;
}

.btn-secondary:hover {
    background: #5a6268;
}
</style>

<script>
// 加载批次选项
function loadBatches() {
    ajax({
        url: '?act=batches',
        method: 'GET',
        success: function(res) {
            if (res.status === 1) {
                const select = document.getElementById('batchSelect');
                res.data.forEach(item => {
                    const option = document.createElement('option');
                    option.value = item.batch;
                    option.textContent = item.batch;
                    select.appendChild(option);
                });
            }
        }
    });
}

// 表单提交处理
document.getElementById('deleteForm').onsubmit = function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const batch = formData.get('batch');
    const status = formData.get('status');
    
    let message = `确定要删除批次 ${batch} `;
    message += status === '' ? '的所有卡券' : `中状态为 ${this.status.options[this.status.selectedIndex].text} 的卡券`;
    message += '吗？\n此操作不可恢复！';
    
    if (!confirm(message)) {
        return;
    }
    
    ajax({
        url: '?act=delete',
        method: 'POST',
        data: Object.fromEntries(formData),
        success: function(res) {
            alert(res.message);
            if (res.status === 1) {
                window.location.href = 'mlist.php';
            }
        }
    });
};

// 页面加载完成后初始化
document.addEventListener('DOMContentLoaded', loadBatches);
</script>

<?php require_once '../inc/footer.php'; ?> 