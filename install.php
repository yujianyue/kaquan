<?php

// 卡立得PHP+mysql通用卡券系统物流领取版 V2024.12.12
// 演示地址: http://kaquan.chalide.cn
// 文件路径: install.php
// 文件大小: 2568 字节
// 最后修改时间: 2024-12-17 20:57:05
// 作者: yujianyue
// 邮件: 15058593138@qq.com
// 版权所有,保留发行权和署名权

// 卡立得PHP+mysql通用卡券系统物流领取版 V2024.12.12
// 演示地址: http://kaquan.chalide.cn
// 文件路径: install.php
// 文件大小: 2277 字节
// 最后修改时间: 2024-12-10 16:02:45
// 作者: yujianyue
// 邮件: 15058593138@qq.com
// 版权所有,保留发行权和署名权
require_once 'inc/conn.php';

// 创建数据表的SQL语句
$sql_tables = [
    // 管理员表
    "CREATE TABLE IF NOT EXISTS admin (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(32) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",
    
    // 卡券表
    "CREATE TABLE IF NOT EXISTS cards (
        id INT AUTO_INCREMENT PRIMARY KEY,
        batch VARCHAR(50) NOT NULL,
        code VARCHAR(50) NOT NULL UNIQUE,
        status TINYINT DEFAULT 0,
        wx_id VARCHAR(50),
        province VARCHAR(50),
        city VARCHAR(50),
        county VARCHAR(50),
        address TEXT,
        receiver VARCHAR(50),
        mobile VARCHAR(20),
        express VARCHAR(50),
        express_no VARCHAR(50),
        create_time DATETIME DEFAULT CURRENT_TIMESTAMP,
        bind_time DATETIME,
        ship_time DATETIME
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",
    
    // 网站配置表
    "CREATE TABLE IF NOT EXISTS site_config (
        id INT AUTO_INCREMENT PRIMARY KEY,
        site_name VARCHAR(100) NOT NULL,
        site_footer TEXT,
        site_link VARCHAR(255),
        site_desc TEXT
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;"
];

// 执行建表
$success = true;
foreach ($sql_tables as $sql) {
    if (!mysqli_query($conn, $sql)) {
        $success = false;
        echo "创建表失败: " . mysqli_error($conn) . "<br>";
    }
}

// 插入默认管理员账号
if ($success) {
    $default_admin = "INSERT INTO admin (username, password) VALUES ('admin', '" . md5('123456') . "')";
    if (mysqli_query($conn, $default_admin)) {
        echo "默认管理员账号创建成功<br>";
        echo "用户名: admin<br>";
        echo "密码: 123456<br>";
    }
    
    // 插入默认网站配置
    $default_config = "INSERT INTO site_config (site_name, site_footer, site_link, site_desc) 
                      VALUES ('卡券管理系统', '版权所有 © 2024', 'https://example.com', '这是一个卡券管理系统')";
    if (mysqli_query($conn, $default_config)) {
        echo "默认网站配置创建成功<br>";
    }
}

echo $success ? "数据库安装完成" : "数据库安装失败"; 