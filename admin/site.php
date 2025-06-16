<?php

// 卡立得PHP+mysql通用卡券系统物流领取版 V2024.12.12
// 演示地址: http://kaquan.chalide.cn
// 文件路径: admin/site.php
// 文件大小: 5402 字节
// 最后修改时间: 2024-12-17 20:57:05
// 作者: yujianyue
// 邮件: 15058593138@qq.com
// 版权所有,保留发行权和署名权

// 卡立得PHP+mysql通用卡券系统物流领取版 V2024.12.12
// 演示地址: http://kaquan.chalide.cn
// 文件路径: admin/site.php
// 文件大小: 5108 字节
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
if (isset($_GET['act']) && $_GET['act'] == 'save') {
    $data = [
        'site_name' => safe_string($_POST['site_name']),
        'site_footer' => safe_string($_POST['site_footer']),
        'site_link' => safe_string($_POST['site_link']),
        'site_desc' => safe_string($_POST['site_desc'])
    ];
    
    if ($db->update('site_config', $data, 'id=1')) {
        json_response(1, '保存成功');
    } else {
        json_response(0, '保存失败');
    }
    exit;
}

// 获取当前配置
$config = $db->get_one("SELECT * FROM site_config WHERE id=1");
?>

<?php require_once '../inc/header.php'; ?>

<div class="page-title">网站设置</div>

<div class="form-container">
    <form id="configForm">
        <div class="form-group">
            <label>网站名称：</label>
            <input type="text" name="site_name" required value="<?php echo htmlspecialchars($config['site_name']); ?>">
            <div class="form-tip">显示在浏览器标题栏和页面顶部</div>
        </div>
        
        <div class="form-group">
            <label>底部文字：</label>
            <textarea name="site_footer" rows="3"><?php echo htmlspecialchars($config['site_footer']); ?></textarea>
            <div class="form-tip">显示在页面底部左侧的版权信息等</div>
        </div>
        
        <div class="form-group">
            <label>底部链接：</label>
            <input type="url" name="site_link" value="<?php echo htmlspecialchars($config['site_link']); ?>">
            <div class="form-tip">显示在页面底部右侧的备案链接</div>
        </div>
        
        <div class="form-group">
            <label>页面说明：</label>
            <textarea name="site_desc" rows="5"><?php echo htmlspecialchars($config['site_desc']); ?></textarea>
            <div class="form-tip">用于描述网站用途，显示在用户查询页面</div>
        </div>
        
        <div class="form-buttons">
            <button type="submit" class="btn-primary">保存设置</button>
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
}

.form-group textarea {
    resize: vertical;
    min-height: 60px;
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
    padding: 10px 30px;
    background: #007bff;
    color: white;
    border: none;
    border-radius: 3px;
    cursor: pointer;
    font-size: 16px;
}

.btn-primary:hover {
    background: #0056b3;
}

/* 成功提示样式 */
.success-tip {
    position: fixed;
    top: 20px;
    right: 20px;
    padding: 15px 25px;
    background: #28a745;
    color: white;
    border-radius: 4px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.2);
    display: none;
    z-index: 9999;
}
</style>

<div id="successTip" class="success-tip">保存成功</div>

<script>
document.getElementById('configForm').onsubmit = function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    ajax({
        url: 'site.php?act=save',
        method: 'POST',
        data: Object.fromEntries(formData),
        success: function(res) {
            if (res.status === 1) {
                // 显示成功提示
                const tip = document.getElementById('successTip');
                tip.style.display = 'block';
                setTimeout(() => {
                    tip.style.display = 'none';
                }, 2000);
                
                // 更新页面标题和顶部标题
                document.title = formData.get('site_name');
                document.querySelector('.site-title').textContent = formData.get('site_name');
                
                // 更新底部信息
                document.querySelector('.copyright').textContent = formData.get('site_footer');
                document.querySelector('.beian a').href = formData.get('site_link');
            } else {
                alert(res.message);
            }
        },
        error: function() {
            alert('保存请求失败');
        }
    });
};
</script>

<?php require_once '../inc/footer.php'; ?> 