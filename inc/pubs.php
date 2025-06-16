<?php

// 卡立得PHP+mysql通用卡券系统物流领取版 V2024.12.12
// 演示地址: http://kaquan.chalide.cn
// 文件路径: inc/pubs.php
// 文件大小: 1231 字节
// 最后修改时间: 2024-12-17 20:57:05
// 作者: yujianyue
// 邮件: 15058593138@qq.com
// 版权所有,保留发行权和署名权

// 卡立得PHP+mysql通用卡券系统物流领取版 V2024.12.12
// 演示地址: http://kaquan.chalide.cn
// 文件路径: inc/pubs.php
// 文件大小: 940 字节
// 最后修改时间: 2024-12-07 06:14:14
// 作者: yujianyue
// 邮件: 15058593138@qq.com
// 版权所有,保留发行权和署名权
// 安全过滤函数
function safe_string($str) {
    return htmlspecialchars(trim($str), ENT_QUOTES);
}

// JSON响应函数
function json_response($status, $message, $data = null) {
    $response = [
        'status' => $status,
        'message' => $message
    ];
    if ($data !== null) {
        $response['data'] = $data;
    }
    header('Content-Type: application/json');
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    exit;
}

// 检查管理员登录状态
function check_admin_login() {
    session_start();
    if (!isset($_SESSION['admin_id'])) {
        header('Location: login.php');
        exit;
    }
}

// 生成随机卡券码
function generate_coupon_code($length = 12) {
    $chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $code = '';
    for ($i = 0; $i < $length; $i++) {
        $code .= $chars[rand(0, strlen($chars) - 1)];
    }
    return $code;
} 