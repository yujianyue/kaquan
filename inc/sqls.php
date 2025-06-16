<?php

// 卡立得PHP+mysql通用卡券系统物流领取版 V2024.12.12
// 演示地址: http://kaquan.chalide.cn
// 文件路径: inc/sqls.php
// 文件大小: 1532 字节
// 最后修改时间: 2024-12-17 20:57:05
// 作者: yujianyue
// 邮件: 15058593138@qq.com
// 版权所有,保留发行权和署名权

// 卡立得PHP+mysql通用卡券系统物流领取版 V2024.12.12
// 演示地址: http://kaquan.chalide.cn
// 文件路径: inc/sqls.php
// 文件大小: 1240 字节
// 最后修改时间: 2024-12-07 06:14:14
// 作者: yujianyue
// 邮件: 15058593138@qq.com
// 版权所有,保留发行权和署名权
class Database {
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    // 查询单条记录
    public function get_one($sql) {
        $result = mysqli_query($this->conn, $sql);
        return mysqli_fetch_assoc($result);
    }
    
    // 查询多条记录
    public function get_all($sql) {
        $result = mysqli_query($this->conn, $sql);
        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
        return $data;
    }
    
    // 执行插入操作
    public function insert($table, $data) {
        $fields = implode(',', array_keys($data));
        $values = "'" . implode("','", array_values($data)) . "'";
        $sql = "INSERT INTO {$table} ({$fields}) VALUES ({$values})";
        return mysqli_query($this->conn, $sql);
    }
    
    // 执行更新操作
    public function update($table, $data, $where) {
        $set = [];
        foreach ($data as $key => $value) {
            $set[] = "{$key}='{$value}'";
        }
        $sql = "UPDATE {$table} SET " . implode(',', $set) . " WHERE {$where}";
        return mysqli_query($this->conn, $sql);
    }
} 