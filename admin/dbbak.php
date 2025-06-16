<?php

// 卡立得PHP+mysql通用卡券系统物流领取版 V2024.12.12
// 演示地址: http://kaquan.chalide.cn
// 文件路径: admin/dbbak.php
// 文件大小: 6449 字节
// 最后修改时间: 2024-12-17 20:57:05
// 作者: yujianyue
// 邮件: 15058593138@qq.com
// 版权所有,保留发行权和署名权

// 卡立得PHP+mysql通用卡券系统物流领取版 V2024.12.12
// 演示地址: http://kaquan.chalide.cn
// 文件路径: admin/dbbak.php
// 文件大小: 6154 字节
// 最后修改时间: 2024-12-10 19:32:53
// 作者: yujianyue
// 邮件: 15058593138@qq.com
// 版权所有,保留发行权和署名权
require_once '../inc/conn.php';
require_once '../inc/pubs.php';
require_once '../inc/sqls.php';

// 检查登录状态
check_admin_login();

// 处理备份请求
if (isset($_GET['act']) && $_GET['act'] == 'backup') {
    // 设置超时时间
    set_time_limit(0);
    
    // 创建备份目录
    $backup_dir = '../backup';
    if (!is_dir($backup_dir)) {
        mkdir($backup_dir, 0777, true);
    }
    
    // 生成备份文件名
    $filename = 'backup_' . date('YmdHis') . '.sql';
    $filepath = $backup_dir . '/' . $filename;
    
    // 开始备份
    try {
        $fp = fopen($filepath, 'w');
        
        // 写入文件头
        fwrite($fp, "-- 数据库备份\n");
        fwrite($fp, "-- 时间：" . date('Y-m-d H:i:s') . "\n");
        fwrite($fp, "-- 数据库：" . DB_NAME . "\n\n");
        
        // 获取所有表
        $tables = mysqli_query($conn, 'SHOW TABLES');
        while ($table = mysqli_fetch_array($tables)) {
            $table_name = $table[0];
            
            // 写入表结构
            $create_table = mysqli_fetch_array(mysqli_query($conn, "SHOW CREATE TABLE `{$table_name}`"));
            fwrite($fp, "DROP TABLE IF EXISTS `{$table_name}`;\n");
            fwrite($fp, $create_table[1] . ";\n\n");
            
            // 写入表数据
            $result = mysqli_query($conn, "SELECT * FROM `{$table_name}`");
            while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
                $fields = array_map(function($value) use ($conn) {
                    return $value === null ? 'NULL' : "'" . mysqli_real_escape_string($conn, $value) . "'";
                }, $row);
                
                fwrite($fp, "INSERT INTO `{$table_name}` VALUES (" . implode(',', $fields) . ");\n");
            }
            fwrite($fp, "\n");
        }
        
        fclose($fp);
        
        // 创建ZIP文件
        $zip = new ZipArchive();
        $zipname = substr($filename, 0, -4) . '.zip';
        $zippath = $backup_dir . '/' . $zipname;
        
        if ($zip->open($zippath, ZipArchive::CREATE) === TRUE) {
            $zip->addFile($filepath, $filename);
            $zip->close();
            
            // 删除SQL文件
            unlink($filepath);
            
            // 下载ZIP文件
            header('Content-Type: application/zip');
            header('Content-Disposition: attachment; filename=' . $zipname);
            header('Content-Length: ' . filesize($zippath));
            readfile($zippath);
            
            // 删除ZIP文件
            unlink($zippath);
            exit;
        } else {
            throw new Exception('创建ZIP文件失败');
        }
    } catch (Exception $e) {
        if (file_exists($filepath)) {
            unlink($filepath);
        }
        if (isset($zippath) && file_exists($zippath)) {
            unlink($zippath);
        }
        json_response(0, '备份失败：' . $e->getMessage());
    }
    exit;
}
?>

<?php require_once '../inc/header.php'; ?>

<div class="backup-container">
    <div class="backup-info">
        <h3>备份说明</h3>
        <ul>
            <li>备份文件包含完整的数据库结构和数据</li>
            <li>备份文件会自动压缩为ZIP格式</li>
            <li>备份过程可能需要几分钟，请耐心等待</li>
            <li>建议定期备份数据库以防数据丢失</li>
        </ul>
    </div>
    
    <div class="backup-action">
        <button id="startBackup" class="btn-primary">开始备份</button>
    </div>
</div>

<style>
  
.page-title {
    font-size: 24px;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 1px solid #eee;
}
  
.backup-container {
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
}

.backup-info {
    background: white;
    padding: 20px;
    border-radius: 5px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    margin-bottom: 30px;
}

.backup-info h3 {
    margin: 0 0 15px 0;
    padding-bottom: 10px;
    border-bottom: 1px solid #eee;
}

.backup-info ul {
    margin: 0;
    padding-left: 20px;
    color: #666;
    line-height: 1.6;
}

.backup-action {
    text-align: center;
}

.btn-primary {
    padding: 12px 30px;
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

/* 加载中遮罩 */
.loading-mask {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255,255,255,0.8);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
}

.loading-content {
    text-align: center;
}

.loading-spinner {
    border: 4px solid #f3f3f3;
    border-top: 4px solid #3498db;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    animation: spin 1s linear infinite;
    margin: 0 auto 10px;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>

<script>
// 显示加载中遮罩
function showLoading() {
    const mask = document.createElement('div');
    mask.className = 'loading-mask';
    mask.innerHTML = `
        <div class="loading-content">
            <div class="loading-spinner"></div>
            <div>正在备份数据库，请勿关闭页面...</div>
        </div>
    `;
    document.body.appendChild(mask);
}

// 隐藏加载中遮罩
function hideLoading() {
    const mask = document.querySelector('.loading-mask');
    if (mask) {
        mask.remove();
    }
}

// 开始备份
document.getElementById('startBackup').onclick = function() {
    if (!confirm('确定要开始备份数据库吗？')) {
        return;
    }
    
    showLoading();
    
    // 创建一个隐藏的iframe来处理下载
    const iframe = document.createElement('iframe');
    iframe.style.display = 'none';
    document.body.appendChild(iframe);
    
    iframe.onload = function() {
        hideLoading();
        document.body.removeChild(iframe);
    };
    
    iframe.src = 'dbbak.php?act=backup';
};
</script>

<?php require_once '../inc/footer.php'; ?> 