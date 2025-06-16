<?php

// 卡立得PHP+mysql通用卡券系统物流领取版 V2024.12.12
// 演示地址: http://kaquan.chalide.cn
// 文件路径: admin/mdown.php
// 文件大小: 7104 字节
// 最后修改时间: 2024-12-17 20:57:05
// 作者: yujianyue
// 邮件: 15058593138@qq.com
// 版权所有,保留发行权和署名权

// 卡立得PHP+mysql通用卡券系统物流领取版 V2024.12.12
// 演示地址: http://kaquan.chalide.cn
// 文件路径: admin/mdown.php
// 文件大小: 6809 字节
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

// 处理导出请求
if (isset($_GET['act']) && $_GET['act'] == 'export') {
    $batch = safe_string($_GET['batch']);
    $status = isset($_GET['status']) ? intval($_GET['status']) : '';
    
    // 构建查询条件
    $where = "WHERE 1=1";
    if ($batch) {
        $where .= " AND batch = '{$batch}'";
    }
    if ($status !== '') {
        $where .= " AND status = {$status}";
    }
    
    // 获取数据
    $sql = "SELECT * FROM cards {$where} ORDER BY id ASC";
    $list = $db->get_all($sql);
    
    if (empty($list)) {
        json_response(0, '没有找到符合条件的数据');
    }
    
    // 设置CSV文件头
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="cards_' . date('YmdHis') . '.csv"');
    
    // 创建文件句柄
    $output = fopen('php://output', 'w');
    
    // 写入UTF-8 BOM
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    
    // 写入表头
    fputcsv($output, [
        '批次号',
        '卡券码',
        '状态',
        '微信ID',
        '收件人',
        '手机号',
        '省份',
        '城市',
        '区县',
        '详细地址',
        '快递公司',
        '快递单号',
        '生成时间',
        '绑定时间',
        '发货时间'
    ]);
    
    // 写入数据
    foreach ($list as $row) {
        $status_text = [
            '-1' => '已删除',
            '0' => '未绑定',
            '1' => '已绑定未发货',
            '2' => '已发货'
        ][$row['status']];
        
        fputcsv($output, [
            $row['batch'],
            $row['code'],
            $status_text,
            $row['wx_id'],
            $row['receiver'],
            $row['mobile'],
            $row['province'],
            $row['city'],
            $row['county'],
            $row['address'],
            $row['express'],
            $row['express_no'],
            $row['create_time'],
            $row['bind_time'],
            $row['ship_time']
        ]);
    }
    
    fclose($output);
    exit;
}

// 获取所有批次号
$batches = $db->get_all("SELECT DISTINCT batch FROM cards ORDER BY batch DESC");
?>

<?php require_once '../inc/header.php'; ?>

<div class="page-title">数据导出</div>

<div class="form-container">
    <form id="exportForm">
        <div class="form-group">
            <label>选择批次：</label>
            <select name="batch">
                <option value="">全部批次</option>
                <?php foreach ($batches as $batch): ?>
                <option value="<?php echo htmlspecialchars($batch['batch']); ?>">
                    <?php echo htmlspecialchars($batch['batch']); ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label>选择状态：</label>
            <select name="status">
                <option value="">全部状态</option>
                <option value="0">未绑定</option>
                <option value="1">已绑定未发货</option>
                <option value="2">已发货</option>
                <option value="-1">已删除</option>
            </select>
        </div>
        
        <div class="form-buttons">
            <button type="submit" class="btn-primary">导出数据</button>
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

.form-group select {
    width: 100%;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 3px;
    font-size: 14px;
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
            <div>正在准备导出数据，请稍候...</div>
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
document.getElementById('exportForm').onsubmit = function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const params = new URLSearchParams(formData);
    
    showLoading();
    
    // 创建一个隐藏的iframe来处理下载
    const iframe = document.createElement('iframe');
    iframe.style.display = 'none';
    document.body.appendChild(iframe);
    
    iframe.onload = function() {
        hideLoading();
        document.body.removeChild(iframe);
    };
    
    iframe.src = 'mdown.php?act=export&' + params.toString();
};
</script>

<?php require_once '../inc/footer.php'; ?> 