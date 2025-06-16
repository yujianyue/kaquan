<?php

// 卡立得PHP+mysql通用卡券系统物流领取版 V2024.12.12
// 演示地址: http://kaquan.chalide.cn
// 文件路径: inc/conn.php
// 文件大小: 863 字节
// 最后修改时间: 2024-12-17 20:57:05
// 作者: yujianyue
// 邮件: 15058593138@qq.com
// 版权所有,保留发行权和署名权

// 卡立得PHP+mysql通用卡券系统物流领取版 V2024.12.12
// 演示地址: http://kaquan.chalide.cn
// 文件路径: inc/conn.php
// 文件大小: 572 字节
// 最后修改时间: 2024-12-10 16:08:12
// 作者: yujianyue
// 邮件: 15058593138@qq.com
// 版权所有,保留发行权和署名权
// 数据库连接配置
define('DB_HOST', 'localhost');
define('DB_USER', 'kaquan_chalide');
define('DB_PASS', 'kzJM8NhACQy33xmM');
define('DB_NAME', 'kaquan_chalide');

$jstime = "20241024.12081208"; //修改参数新内容可刷新js css缓存
$pitime = date("YmdHis"); //修改参数新内容可刷新js css缓存

// 创建数据库连接
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// 设置字符集
mysqli_set_charset($conn, 'utf8mb4');

// 检查连接
if (!$conn) {
    die("连接失败: " . mysqli_connect_error());
} 