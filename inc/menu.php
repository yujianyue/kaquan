<?php

// 卡立得PHP+mysql通用卡券系统物流领取版 V2024.12.12
// 演示地址: http://kaquan.chalide.cn
// 文件路径: inc/menu.php
// 文件大小: 1849 字节
// 最后修改时间: 2024-12-17 20:57:05
// 作者: yujianyue
// 邮件: 15058593138@qq.com
// 版权所有,保留发行权和署名权

// 卡立得PHP+mysql通用卡券系统物流领取版 V2024.12.12
// 演示地址: http://kaquan.chalide.cn
// 文件路径: inc/menu.php
// 文件大小: 1557 字节
// 最后修改时间: 2024-12-07 08:08:04
// 作者: yujianyue
// 邮件: 15058593138@qq.com
// 版权所有,保留发行权和署名权
$menus = [
    [
        'title' => '卡券管理',
        'children' => [
            ['title' => '卡券列表', 'url' => 'mlist.php'],
            ['title' => '生成卡券', 'url' => 'mkmar.php'],
            ['title' => '批量导入', 'url' => 'daoru.php'],
            ['title' => '数据导出', 'url' => 'mdown.php'],
            ['title' => '批次删除', 'url' => 'ipdels.php'],
            ['title' => '批量发货', 'url' => 'ipifa.php']
        ]
    ],
    [
        'title' => '系统管理',
        'children' => [
            ['title' => '网站设置', 'url' => 'site.php'],
            ['title' => '数据统计', 'url' => 'itong.php'],
            ['title' => '数据备份', 'url' => 'dbbak.php']
        ]
    ],
    [
        'title' => '账号相关',
        'children' => [
            ['title' => '修改密码', 'url' => 'gaimi.php'],
            ['title' => '退出登录', 'url' => 'login.php?act=logout']
        ]
    ]
];

// 输出菜单HTML
function output_menu() {
    global $menus;
    $html = '<div class="nav-menu">';
    foreach ($menus as $menu) {
        $html .= '<div class="menu-item">';
        $html .= '<span class="menu-title">' . $menu['title'] . '</span>';
        $html .= '<div class="submenu">';
        foreach ($menu['children'] as $child) {
            $html .= '<a href="' . $child['url'] . '">' . $child['title'] . '</a>';
        }
        $html .= '</div>';
        $html .= '</div>';
    }
    $html .= '</div>';
    return $html;
}
?> 