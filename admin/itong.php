<?php

// 卡立得PHP+mysql通用卡券系统物流领取版 V2024.12.12
// 演示地址: http://kaquan.chalide.cn
// 文件路径: admin/itong.php
// 文件大小: 7043 字节
// 最后修改时间: 2024-12-17 20:57:05
// 作者: yujianyue
// 邮件: 15058593138@qq.com
// 版权所有,保留发行权和署名权

// 卡立得PHP+mysql通用卡券系统物流领取版 V2024.12.12
// 演示地址: http://kaquan.chalide.cn
// 文件路径: admin/itong.php
// 文件大小: 6748 字节
// 最后修改时间: 2024-12-10 19:51:09
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

// 获取统计数据
$stats = [
    // 总卡券数
    'total' => $db->get_one("SELECT COUNT(*) as count FROM cards WHERE status >= 0")['count'],
    
    // 各状态数量
    'status' => [
        'unused' => $db->get_one("SELECT COUNT(*) as count FROM cards WHERE status = 0")['count'],
        'bound' => $db->get_one("SELECT COUNT(*) as count FROM cards WHERE status = 1")['count'],
        'shipped' => $db->get_one("SELECT COUNT(*) as count FROM cards WHERE status = 2")['count'],
        'deleted' => $db->get_one("SELECT COUNT(*) as count FROM cards WHERE status = -1")['count']
    ],
    
    // 批次统计
    'batches' => $db->get_all("SELECT batch, status, COUNT(*) as count FROM cards WHERE status >= 0 GROUP BY batch,status ORDER BY batch DESC"),
    
    // 最近7天每日发货数量
    'daily_ships' => $db->get_all("SELECT DATE(ship_time) as date, COUNT(*) as count 
        FROM cards WHERE status = 2 
        AND ship_time >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
        GROUP BY DATE(ship_time) ORDER BY date DESC"),
    
    // 最近发货记录
    'recent_ships' => $db->get_all("SELECT * FROM cards WHERE status = 2 ORDER BY ship_time DESC LIMIT 10")
];
?>

<?php require_once '../inc/header.php'; ?>

<div class="stats-container">
    <!-- 概览卡片 -->
    <div class="stats-cards">
        <div class="stat-card">
            <div class="stat-title">总卡券数</div>
            <div class="stat-value"><?php echo number_format($stats['total']); ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-title">未使用</div>
            <div class="stat-value"><?php echo number_format($stats['status']['unused']); ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-title">已绑定</div>
            <div class="stat-value"><?php echo number_format($stats['status']['bound']); ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-title">已发货</div>
            <div class="stat-value"><?php echo number_format($stats['status']['shipped']); ?></div>
        </div>
    </div>
    
    <!-- 批次统计 -->
    <div class="stats-section">
        <h3>批次统计</h3>
        <div class="stats-table">
            <table>
                <thead>
                    <tr>
                        <th>批次号</th>
                        <th>状态</th>
                        <th>卡券数量</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($stats['batches'] as $batch): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($batch['batch']); ?></td>
                        <td><?php echo htmlspecialchars($batch['status']); ?></td>
                        <td><?php echo number_format($batch['count']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- 每日发货统计 -->
    <!--div class="stats-section">
        <h3>最近7天发货统计</h3>
        <div class="stats-chart">
            <canvas id="shipChart"></canvas>
        </div>
    </div-->
    
    <!-- 最近发货记录 -->
    <div class="stats-section">
        <h3>最近发货记录</h3>
        <div class="stats-table">
            <table>
                <thead>
                    <tr>
                        <th>卡券码</th>
                        <th>收件人</th>
                        <th>快递信息</th>
                        <th>发货时间</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($stats['recent_ships'] as $ship): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($ship['code']); ?></td>
                        <td><?php echo htmlspecialchars($ship['receiver']); ?></td>
                        <td><?php echo htmlspecialchars($ship['express'] . ': ' . $ship['express_no']); ?></td>
                        <td><?php echo $ship['ship_time']; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
.stats-container {
    padding: 20px;
}

/* 统计卡片样式 */
.stats-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: white;
    padding: 20px;
    border-radius: 5px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    text-align: center;
}

.stat-title {
    color: #666;
    font-size: 14px;
    margin-bottom: 10px;
}

.stat-value {
    font-size: 24px;
    font-weight: bold;
    color: #007bff;
}

/* 统计区块样式 */
.stats-section {
    background: white;
    padding: 20px;
    border-radius: 5px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    margin-bottom: 20px;
}

.stats-section h3 {
    margin: 0 0 20px 0;
    padding-bottom: 10px;
    border-bottom: 1px solid #eee;
}

/* 表格样式 */
.stats-table {
    overflow-x: auto;
}

.stats-table table {
    width: 100%;
    border-collapse: collapse;
}

.stats-table th,
.stats-table td {
    padding: 10px;
    border: 1px solid #eee;
    text-align: left;
}

.stats-table th {
    background: #f8f9fa;
}

/* 图表样式 */
.stats-chart {
    height: 300px;
    margin-top: 20px;
}
</style>

<!-- 引入Chart.js -->
<!--script src="https://cdn.jsdelivr.net/npm/chart.js"></script-->

<!--script>
// 准备图表数据
const chartData = <?php echo json_encode($stats['daily_ships']); ?>;
const dates = chartData.map(item => item.date);
const counts = chartData.map(item => item.count);

// 创建图表
const ctx = document.getElementById('shipChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: dates,
        datasets: [{
            label: '发货数量',
            data: counts,
            borderColor: '#007bff',
            backgroundColor: 'rgba(0, 123, 255, 0.1)',
            tension: 0.1,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});
</script-->

<?php require_once '../inc/footer.php'; ?> 