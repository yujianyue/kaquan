<?php

// 卡立得PHP+mysql通用卡券系统物流领取版 V2024.12.12
// 演示地址: http://kaquan.chalide.cn
// 文件路径: admin/login.php
// 文件大小: 4696 字节
// 最后修改时间: 2024-12-17 20:57:05
// 作者: yujianyue
// 邮件: 15058593138@qq.com
// 版权所有,保留发行权和署名权

// 卡立得PHP+mysql通用卡券系统物流领取版 V2024.12.12
// 演示地址: http://kaquan.chalide.cn
// 文件路径: admin/login.php
// 文件大小: 4401 字节
// 最后修改时间: 2024-12-10 15:57:39
// 作者: yujianyue
// 邮件: 15058593138@qq.com
// 版权所有,保留发行权和署名权
require_once '../inc/conn.php';
require_once '../inc/pubs.php';

session_start();

// 如果已经登录，直接跳转到管理页面
if (isset($_GET['act']) && $_GET['act']=="logout") {
        $_SESSION['admin_id'] = "";
        $_SESSION['admin_username'] = "";
}

// 如果已经登录，直接跳转到管理页面
if (isset($_SESSION['admin_id']) && $_SESSION['admin_id'] <> "") {
    header('Location: mlist.php');
    exit;
}

// 处理登录请求
if (isset($_GET['act']) && $_GET['act'] == 'login') {
    $username = safe_string($_POST['username']);
    $password = md5($_POST['password']);
    
    $sql = "SELECT id FROM admin WHERE username='$username' AND password='$password'";
    $result = mysqli_query($conn, $sql);
    
    if ($row = mysqli_fetch_assoc($result)) {
        $_SESSION['admin_id'] = $row['id'];
        $_SESSION['admin_username'] = $username;
        json_response(1, '登录成功');
    } else {
        json_response(0, '用户名或密码错误');
    }
    exit;
}

// 获取网站配置
$sql = "SELECT site_name FROM site_config WHERE id=1";
$result = mysqli_query($conn, $sql);
$site_config = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?php echo $site_config['site_name']; ?> - 管理员登录</title>
    <link rel="stylesheet" href="../inc/css/css.css?t=<?php echo $jstime; ?>">
    <style>
        /* 登录页面特定样式 */
        .login-container {
            max-width: 400px;
            margin: 100px auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
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
    padding: 8px 0;
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
    </style>
</head>
<body>
<div class="page-container">
    <header class="header">
        <div class="site-title"><?php echo $site_config['site_name']; ?></div>
    </header>
    <div class="main-content">
        <div class="login-container">
            <h2 style="text-align: center;">管理员登录</h2>
            <form id="loginForm">
                <div class="form-group">
                    <label>用户名：</label>
                    <input type="text" name="username" required>
                </div>
                <div class="form-group">
                    <label>密码：</label>
                    <input type="password" name="password" required>
                </div>
                <button type="submit" class="btn-login btn-primary">登录</button>
            </form>
        </div>
    </div>
    <?php require_once '../inc/footer.php'; ?>
</div>
<script src="../inc/js/js.js"></script>
<script>
    document.getElementById('loginForm').onsubmit = function(e) {
        e.preventDefault();
        
        var formData = {
            username: this.username.value,
            password: this.password.value
        };
        
        ajax({
            url: 'login.php?act=login',
            method: 'POST',
            data: formData,
            success: function(response) {
                if (response.status === 1) {
                    window.location.href = 'mlist.php';
                } else {
                    alert(response.message);
                }
            },
            error: function() {
                alert('登录请求失败');
            }
        });
    };
</script>
</body>
</html> 