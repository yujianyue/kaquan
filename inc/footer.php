<?php

// 卡立得PHP+mysql通用卡券系统物流领取版 V2024.12.12
// 演示地址: http://kaquan.chalide.cn
// 文件路径: inc/footer.php
// 文件大小: 787 字节
// 最后修改时间: 2024-12-17 20:57:05
// 作者: yujianyue
// 邮件: 15058593138@qq.com
// 版权所有,保留发行权和署名权

// 卡立得PHP+mysql通用卡券系统物流领取版 V2024.12.12
// 演示地址: http://kaquan.chalide.cn
// 文件路径: inc/footer.php
// 文件大小: 494 字节
// 最后修改时间: 2024-12-07 07:13:10
// 作者: yujianyue
// 邮件: 15058593138@qq.com
// 版权所有,保留发行权和署名权
$sql = "SELECT site_footer, site_link FROM site_config WHERE id=1";
$result = mysqli_query($conn, $sql);
$config = mysqli_fetch_assoc($result);
?>
    </div>
    <footer class="footer">
        <div class="copyright"><?php echo $config['site_footer']; ?></div>
        <div class="beian"><a href="<?php echo $config['site_link']; ?>" target="_blank">备案信息</a></div>
    </footer>
</div>
<script src="../inc/js/js.js?t=<?php echo $jstime; ?>"></script>
</body>
</html> 