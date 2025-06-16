<?php

// 卡立得PHP+mysql通用卡券系统物流领取版 V2024.12.12
// 演示地址: http://kaquan.chalide.cn
// 文件路径: admin/gaimi.php
// 文件大小: 6734 字节
// 最后修改时间: 2024-12-17 20:57:05
// 作者: yujianyue
// 邮件: 15058593138@qq.com
// 版权所有,保留发行权和署名权

// 卡立得PHP+mysql通用卡券系统物流领取版 V2024.12.12
// 演示地址: http://kaquan.chalide.cn
// 文件路径: admin/gaimi.php
// 文件大小: 6439 字节
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
if (isset($_GET['act']) && $_GET['act'] == 'change') {
    $old_password = md5($_POST['old_password']);
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // 验证新密码
    if (strlen($new_password) < 6) {
        json_response(0, '新密码长度不能小于6位');
    }
    
    if ($new_password !== $confirm_password) {
        json_response(0, '两次输入的新密码不一致');
    }
    
    // 验证旧密码
    $admin_id = $_SESSION['admin_id'];
    $sql = "SELECT id FROM admin WHERE id = {$admin_id} AND password = '{$old_password}'";
    if (!$db->get_one($sql)) {
        json_response(0, '旧密码错误');
    }
    
    // 更新密码
    $data = ['password' => md5($new_password)];
    if ($db->update('admin', $data, "id = {$admin_id}")) {
        // 更新成功后清除session，要求重新登录
        session_destroy();
        json_response(1, '密码修改成功，请重新登录');
    } else {
        json_response(0, '密码修改失败');
    }
    exit;
}
?>

<?php require_once '../inc/header.php'; ?>

<div class="page-title">修改密码</div>

<div class="form-container">
    <form id="passwordForm">
        <div class="form-group">
            <label>当前密码：</label>
            <input type="password" name="old_password" required>
            <div class="form-tip">请输入当前使用的密码</div>
        </div>
        
        <div class="form-group">
            <label>新密码：</label>
            <input type="password" name="new_password" required minlength="6">
            <div class="form-tip">新密码长度不能小于6位</div>
        </div>
        
        <div class="form-group">
            <label>确认新密码：</label>
            <input type="password" name="confirm_password" required>
            <div class="form-tip">请再次输入新密码</div>
        </div>
        
        <div class="form-buttons">
            <button type="submit" class="btn-primary">确认修改</button>
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
    max-width: 500px;
    margin: 0 auto;
    padding: 20px;
    background: #fff;
    border-radius: 5px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

.form-group {
    margin-bottom: 20px;
    position: relative;
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
    font-size: 14px;
}

.form-group input:focus {
    border-color: #007bff;
    outline: none;
    box-shadow: 0 0 0 2px rgba(0,123,255,0.25);
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

/* 密码强度指示器 */
.password-strength {
    height: 3px;
    margin-top: 5px;
    background: #eee;
    border-radius: 2px;
    overflow: hidden;
}

.password-strength-bar {
    height: 100%;
    width: 0;
    transition: width 0.3s, background-color 0.3s;
}

.strength-weak { background-color: #dc3545; width: 33.33%; }
.strength-medium { background-color: #ffc107; width: 66.66%; }
.strength-strong { background-color: #28a745; width: 100%; }
</style>

<script>
// 检查密码强度
function checkPasswordStrength(password) {
    let strength = 0;
    
    if (password.length >= 8) strength++;
    if (/[A-Z]/.test(password)) strength++;
    if (/[0-9]/.test(password)) strength++;
    if (/[^A-Za-z0-9]/.test(password)) strength++;
    
    return strength;
}

// 更新密码强度指示器
function updateStrengthIndicator(input) {
    const strength = checkPasswordStrength(input.value);
    const bar = input.parentElement.querySelector('.password-strength-bar');
    
    bar.className = 'password-strength-bar';
    if (strength >= 3) {
        bar.classList.add('strength-strong');
    } else if (strength >= 2) {
        bar.classList.add('strength-medium');
    } else if (strength >= 1) {
        bar.classList.add('strength-weak');
    }
}

// 添加密码强度指示器
const newPasswordInput = document.querySelector('input[name="new_password"]');
const strengthDiv = document.createElement('div');
strengthDiv.className = 'password-strength';
strengthDiv.innerHTML = '<div class="password-strength-bar"></div>';
newPasswordInput.parentElement.insertBefore(strengthDiv, newPasswordInput.nextSibling);

// 监听密码输入
newPasswordInput.addEventListener('input', function() {
    updateStrengthIndicator(this);
});

// 表单提交处理
document.getElementById('passwordForm').onsubmit = function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const newPassword = formData.get('new_password');
    const confirmPassword = formData.get('confirm_password');
    
    // 验证新密码
    if (newPassword.length < 6) {
        alert('新密码长度不能小于6位');
        return;
    }
    
    if (newPassword !== confirmPassword) {
        alert('两次输入的新密码不一致');
        return;
    }
    
    if (!confirm('确定要修改密码吗？修改后需要重新登录')) {
        return;
    }
    
    ajax({
        url: 'gaimi.php?act=change',
        method: 'POST',
        data: Object.fromEntries(formData),
        success: function(res) {
            alert(res.message);
            if (res.status === 1) {
                window.location.href = 'login.php';
            }
        },
        error: function() {
            alert('修改请求失败');
        }
    });
};
</script>

<?php require_once '../inc/footer.php'; ?> 