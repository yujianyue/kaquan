<?php

// 卡立得PHP+mysql通用卡券系统物流领取版 V2024.12.12
// 演示地址: http://kaquan.chalide.cn
// 文件路径: admin/mlist.php
// 文件大小: 16724 字节
// 最后修改时间: 2024-12-17 20:57:05
// 作者: yujianyue
// 邮件: 15058593138@qq.com
// 版权所有,保留发行权和署名权

// 卡立得PHP+mysql通用卡券系统物流领取版 V2024.12.12
// 演示地址: http://kaquan.chalide.cn
// 文件路径: admin/mlist.php
// 文件大小: 16428 字节
// 最后修改时间: 2024-12-10 19:29:26
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

// 处理AJAX请求
if (isset($_GET['act'])) {
    switch ($_GET['act']) {
        case 'batches':
            // 获取所有批次
            $sql = "SELECT DISTINCT batch FROM cards WHERE status >= 0 ORDER BY batch DESC";
            $batches = $db->get_all($sql);
            json_response(1, 'success', $batches);
            break;
            
        case 'list':
            // 获取查询参数
            $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
            $limit = 10; // 每页显示数量
            $offset = ($page - 1) * $limit;
            
            // 构建查询条件
            $where = "WHERE 1=1";
            if (!empty($_GET['batch'])) {
                $where .= " AND batch = '" . safe_string($_GET['batch']) . "'";
            }
            if (!empty($_GET['code'])) {
                $where .= " AND code LIKE '%" . safe_string($_GET['code']) . "%'";
            }
            if (isset($_GET['status']) && $_GET['status'] !== '') {
                $where .= " AND status = " . intval($_GET['status']);
            }
            
            // 获取总记录数
            $total = $db->get_one("SELECT COUNT(*) as count FROM cards {$where}")['count'];
            
            // 获取当前页数据
            $sql = "SELECT * FROM cards {$where} ORDER BY id DESC LIMIT {$offset}, {$limit}";
            $list = $db->get_all($sql);
            
            // 计算总页数
            $total_pages = ceil($total / $limit);
            
            json_response(1, 'success', [
                'list' => $list,
                'total' => $total,
                'total_pages' => $total_pages,
                'current_page' => $page
            ]);
            break;
            
        case 'delete':
            $id = intval($_POST['id']);
            if ($db->update('cards', ['status' => -1], "id = {$id}")) {
                json_response(1, '删除成功');
            } else {
                json_response(0, '删除失败');
            }
            break;
            
        case 'batch_delete':
            $ids = isset($_POST['ids']) ? $_POST['ids'] : [];
            if (empty($ids)) {
                json_response(0, '请选择要删除的卡券');
            }
            preg_match_all('/\d+/', $ids, $mat);
            $ids = array_map('intval', $mat[0]);
            $id_str = implode(',', $ids);
            
            if ($db->update('cards', ['status' => -1], "id IN ({$id_str})")) {
                json_response(1, '删除成功');
            } else {
                json_response(0, '删除失败');
            }
            break;
            
        case 'ship':
            $id = intval($_POST['id']);
            $data = [
                'express' => safe_string($_POST['express']),
                'express_no' => safe_string($_POST['express_no']),
                'status' => 2,
                'ship_time' => date('Y-m-d H:i:s')
            ];
            
            if ($db->update('cards', $data, "id = {$id}")) {
                json_response(1, '发货成功');
            } else {
                json_response(0, '发货失败');
            }
            break;
            
        case 'card_info':
            $id = intval($_GET['id']);
            $sql = "SELECT * FROM cards WHERE id = {$id}";
            $card = $db->get_one($sql);
            
            if (!$card) {
                json_response(0, '卡券不存在');
            }
            
            $status_text = [
                '-1' => '已删除',
                '0' => '未绑定',
                '1' => '已绑定未发货',
                '2' => '已发货'
            ][$card['status']];
            
            $card['status_text'] = $status_text;
            json_response(1, 'success', $card);
            break;
    }
    exit;
}
?>

<?php require_once '../inc/header.php'; ?>

<!-- 搜索区域 -->
<div class="search-box">
    <form id="searchForm">
        <select name="batch" id="batchSelect">
            <option value="">全部批次</option>
        </select>
        <input type="text" name="code" placeholder="卡券码">
        <select name="status">
            <option value="">全部状态</option>
            <option value="0">未绑定</option>
            <option value="1">已绑定未发货</option>
            <option value="2">已发货</option>
            <option value="-1">已删除</option>
        </select>
        <button type="submit">搜索</button>
    </form>
</div>

<!-- 工具栏 -->
<!--div class="toolbar"></div-->

<!-- 数据列表 -->
<table class="data-table">
    <thead>
        <tr>
            <th><input type="checkbox" id="checkAll"></th>
            <th>ID</th>
            <th>批次号</th>
            <th>卡券码</th>
            <th>状态</th>
            <th>收件人</th>
            <th>手机号</th>
            <th>地址</th>
            <th>快递信息</th>
            <th>生成时间</th>
            <th><button class="btn btn-danger" onclick="batchDelete()">批删</button></th>
        </tr>
    </thead>
    <tbody id="dataList"></tbody>
</table>

<!-- 分页区域 -->
<div class="pagination" id="pagination"></div>

<!-- 发货弹窗 -->
<div id="shipModal" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h3>发货信息</h3>
        <div class="card-info">
            <p><strong>卡券码：</strong><span id="cardCode"></span></p>
            <p><strong>当前状态：</strong><span id="cardStatus"></span></p>
            <p><strong>收件人：</strong><span id="cardReceiver"></span></p>
            <p><strong>联系电话：</strong><span id="cardMobile"></span></p>
            <p><strong>收货地址：</strong><span id="cardAddress"></span></p>
        </div>
        <form id="shipForm">
            <input type="hidden" name="id">
            <div class="form-group">
                <label>快递公司：</label>
                <input type="text" name="express" required>
            </div>
            <div class="form-group">
                <label>快递单号：</label>
                <input type="text" name="express_no" required>
            </div>
            <div class="modal-buttons">
                <button type="submit" class="modal-buttons">确认发货</button>
                <button type="button" class="modal-buttons cancel">取消</button>
            </div>
        </form>
    </div>
</div>

<style>
/* 搜索框样式 */
.search-box {
    margin-bottom: 20px;
    padding: 15px;
    background: #f5f5f5;
    border-radius: 5px;
}

.search-box input,
.search-box select {
    padding: 8px;
    margin-right: 10px;
    border: 1px solid #ddd;
    border-radius: 3px;
}

.search-box button {
    padding: 8px 20px;
    background: #007bff;
    color: white;
    border: none;
    border-radius: 3px;
    cursor: pointer;
}

/* 工具栏样式 */
.toolbar {
    margin: 10px 0;
}

.btn-danger {
    background: #dc3545;
    color: white;
}

.btn-danger:hover {
    background: #c82333;
}
  
.form-group{display:flex;align-items:center;margin-bottom:10px;}
label{flex:0 0 0 128px;text-align:right;margin-right:5px;}
input{flex:2;padding:8px;border:1px solid #ccc;border-radius:4px;font-size:14px;box-sizing:border-box;}  
  
/* 模态框按钮样式 */
.modal-buttons {
    margin: 10px 20px;
    text-align: right;
}

.modal-buttons button {
    margin-left: 0px;
    padding: 8px 15px;
    border: none;
    border-radius: 3px;
    cursor: pointer;
}

.modal-buttons button[type="submit"] {
    background-color: #007BFF; /* 确认按钮颜色 */
    color: white;
}

.modal-buttons button[type="submit"]:hover {
    background-color: #0056b3; /* 确认按钮悬停颜色 */
}

.modal-buttons.cancel {
    background-color: #ccc; /* 取消按钮颜色 */
    color: #333;
}

.modal-buttons.cancel:hover {
    background-color: #aaa; /* 取消按钮悬停颜色 */
}

/* 数据表格样式 */
.data-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
}

.data-table th,
.data-table td {
    padding: 6px;
    border: 1px solid #ddd;
    text-align: left;
}

.data-table th {
    background: #f5f5f5;
}

.data-table tr:hover {
    background: #f9f9f9;
}

/* 操作按钮样式 */
.btn {
    padding: 5px 10px;
    margin: 0 3px;
    border: none;
    border-radius: 3px;
    cursor: pointer;
}

.btn-ship {
    background: #28a745;
    color: white;
}

.btn-delete {
    background: #dc3545;
    color: white;
}

/* 卡券信息样式 */
.card-info {
    background: #f8f9fa;
    padding: 15px;
    margin-bottom: 20px;
    border-radius: 3px;
}

.card-info p {
    margin: 5px 0;
}

.card-info strong {
    display: inline-block;
    width: 80px;
}
</style>

<script>
let currentPage = 1;
let currentParams = new URLSearchParams();

// 加载批次选项
function loadBatches() {
    ajax({
        url: '?act=batches',
        method: 'GET',
        success: function(res) {
            if (res.status === 1) {
                const select = document.getElementById('batchSelect');
                res.data.forEach(item => {
                    const option = document.createElement('option');
                    option.value = item.batch;
                    option.textContent = item.batch;
                    select.appendChild(option);
                });
            }
        }
    });
}

// 加载数据列表
function loadList(page) {
    const formData = new FormData(document.getElementById('searchForm'));
    currentParams = new URLSearchParams(formData);
    currentParams.set('page', page);
    
    ajax({
        url: '?act=list&' + currentParams.toString(),
        success: function(res) {
            if (res.status === 1) {
                renderList(res.data.list);
                renderPagination(res.data.total_pages, res.data.current_page);
                currentPage = res.data.current_page;
            }
        }
    });
}

// 渲染数据列表
function renderList(list) {
    const tbody = document.getElementById('dataList');
    let html = '';
    
    list.forEach(item => {
        const status = {
            '-1': '已删除',
            '0': '未绑定',
            '1': '已绑定未发货',
            '2': '已发货'
        }[item.status];
        
        const address = [item.province, item.city, item.county, item.address].filter(Boolean).join(' ');
        const express = item.express ? `${item.express}: ${item.express_no}` : '-';
        
        html += `
            <tr>
                <td><input type="checkbox" class="row-check" value="${item.id}"></td>
                <td>${item.id}</td>
                <td>${item.batch}</td>
                <td>${item.code}</td>
                <td>${status}</td>
                <td>${item.receiver || '-'}</td>
                <td>${item.mobile || '-'}</td>
                <td>${address || '-'}</td>
                <td>${express}</td>
                <td>${item.create_time}</td>
                <td>
                    ${item.status >= 2 ? `<button class="btn btn-ship" onclick="showShipModal(${item.id})">详情</button>` : ''}
                    ${item.status == 1 ? `<button class="btn btn-ship" onclick="showShipModal(${item.id})">发货</button>` : ''}
                    ${item.status == 0 ? `<button class="btn btn-delete" onclick="deleteItem(${item.id})">删除</button>` : ''}
                </td>
            </tr>
        `;
    });
    
    tbody.innerHTML = html;
}

// 渲染分页
function renderPagination(total, current) {
    const pagination = document.getElementById('pagination');
    let html = '';
    
    // 起始页
    html += `<a href="javascript:;" onclick="loadList(1)" ${current === 1 ? 'class="disabled"' : ''}>首页</a>`;
    
    // 上一页
    html += `<a href="javascript:;" onclick="loadList(${current - 1})" ${current === 1 ? 'class="disabled"' : ''}>上一页</a>`;
    
    // 页码下拉框
    html += '<select onchange="loadList(this.value)">';
    for (let i = 1; i <= total; i++) {
        html += `<option value="${i}" ${i === current ? 'selected' : ''}>${i}</option>`;
    }
    html += '</select>';
    
    // 下一页
    html += `<a href="javascript:;" onclick="loadList(${current + 1})" ${current === total ? 'class="disabled"' : ''}>下一页</a>`;
    
    // 最后页
    html += `<a href="javascript:;" onclick="loadList(${total})" ${current === total ? 'class="disabled"' : ''}>末页</a>`;
    
    pagination.innerHTML = html;
}

// 显示发货弹窗
function showShipModal(id) {
    ajax({
        url: '?act=card_info&id=' + id,
        success: function(res) {
            if (res.status === 1) {
                const data = res.data;
                document.getElementById('cardCode').textContent = data.code;
                document.getElementById('cardStatus').textContent = data.status_text;
                document.getElementById('cardReceiver').textContent = data.receiver;
                document.getElementById('cardMobile').textContent = data.mobile;
                document.getElementById('cardAddress').textContent = 
                    [data.province, data.city, data.county, data.address].filter(Boolean).join(' ');
                
                document.querySelector('#shipForm input[name="id"]').value = id;
                document.getElementById('shipModal').style.display = 'block';
            } else {
                alert(res.message);
            }
        }
    });
}

// 删除卡券
function deleteItem(id) {
    if (!confirm('确定要删除这张卡券吗？')) {
        return;
    }
    
    ajax({
        url: '?act=delete',
        method: 'POST',
        data: { id: id },
        success: function(res) {
            alert(res.message);
            if (res.status === 1) {
                loadList(currentPage);
            }
        }
    });
}

// 批量删除
function batchDelete() {
    const checks = document.getElementsByClassName('row-check');
    const ids = [];
    for (let check of checks) {
        if (check.checked) {
            ids.push(check.value);
        }
    }
    
    if (ids.length === 0) {
        alert('请选择要删除的卡券');
        return;
    }
    
    if (!confirm(`确定要删除选中的 ${ids.length} 张卡券吗？`)) {
        return;
    }
    
    ajax({
        url: '?act=batch_delete',
        method: 'POST',
        data: { ids: ids },
        success: function(res) {
            alert(res.message);
            if (res.status === 1) {
                loadList(currentPage);
            }
        }
    });
}

// 全选/取消全选
document.getElementById('checkAll').onchange = function() {
    const checks = document.getElementsByClassName('row-check');
    for (let check of checks) {
        check.checked = this.checked;
    }
};

// 绑定事件
document.addEventListener('DOMContentLoaded', function() {
    // 加载批次选项
    loadBatches();
    
    // 搜索表单提交
    document.getElementById('searchForm').onsubmit = function(e) {
        e.preventDefault();
        loadList(1);
    };
    
    // 发货表单提交
    document.getElementById('shipForm').onsubmit = function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        ajax({
            url: '?act=ship',
            method: 'POST',
            data: Object.fromEntries(formData),
            success: function(res) {
                if (res.status === 1) {
                    alert(res.message);
                    document.getElementById('shipModal').style.display = 'none';
                    loadList(currentPage);
                } else {
                    alert(res.message);
                }
            }
        });
    };
    
    // 关闭弹窗
    document.querySelector('#shipModal .close').onclick = function() {
        document.getElementById('shipModal').style.display = 'none';
    };
    
    document.querySelector('#shipModal .cancel').onclick = function() {
        document.getElementById('shipModal').style.display = 'none';
    };
    
    // 初始加载数据
    loadList(1);
});
</script>

<?php require_once '../inc/footer.php'; ?>