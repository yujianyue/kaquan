<?php

// 卡立得PHP+mysql通用卡券系统物流领取版 V2024.12.12
// 演示地址: http://kaquan.chalide.cn
// 文件路径: inc/header.php
// 文件大小: 987 字节
// 最后修改时间: 2024-12-17 20:57:05
// 作者: yujianyue
// 邮件: 15058593138@qq.com
// 版权所有,保留发行权和署名权

// 卡立得PHP+mysql通用卡券系统物流领取版 V2024.12.12
// 演示地址: http://kaquan.chalide.cn
// 文件路径: inc/header.php
// 文件大小: 694 字节
// 最后修改时间: 2024-12-07 07:13:02
// 作者: yujianyue
// 邮件: 15058593138@qq.com
// 版权所有,保留发行权和署名权
require_once 'conn.php';
require_once 'pubs.php';

// 获取网站配置
$sql = "SELECT site_name FROM site_config WHERE id=1";
$result = mysqli_query($conn, $sql);
$site_config = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?php echo $site_config['site_name']; ?></title>
    <link rel="stylesheet" href="../inc/css/css.css?t=<?php echo $jstime; ?>">
</head>
<body>
<div class="page-container">
    <header class="header">
        <div class="site-title"><?php echo $site_config['site_name']; ?></div>
        <?php require_once 'menu.php'; echo output_menu(); ?>
    </header>
    <div class="main-content"> 