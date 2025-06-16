<?php

// 卡立得PHP+mysql通用卡券系统物流领取版 V2024.12.12
// 演示地址: http://kaquan.chalide.cn
// 文件路径: index.php
// 文件大小: 629 字节
// 最后修改时间: 2024-12-17 20:57:04
// 作者: yujianyue
// 邮件: 15058593138@qq.com
// 版权所有,保留发行权和署名权

// 卡立得PHP+mysql通用卡券系统物流领取版 V2024.12.12
// 演示地址: http://kaquan.chalide.cn
// 文件路径: index.php
// 文件大小: 341 字节
// 最后修改时间: 2024-12-10 16:02:45
// 作者: yujianyue
// 邮件: 15058593138@qq.com
// 版权所有,保留发行权和署名权
require_once 'inc/conn.php';
require_once 'inc/pubs.php';

// 检查是否已登录
session_start();
if (isset($_SESSION['admin_id'])) {
    // 已登录，跳转到管理列表
    header('Location: admin/mlist.php');
} else {
    // 未登录，跳转到登录页面
    header('Location: admin/login.php');
}
exit;
?> 