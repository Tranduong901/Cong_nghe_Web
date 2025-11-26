<?php
// File này hiển thị chế độ Khách, được nhúng vào index.php

// Đảm bảo biến $hoa_list tồn tại trước khi lặp
if (!isset($hoa_list) || !is_array($hoa_list)) {
    return; // Dừng lại nếu dữ liệu không sẵn sàng
}
?>

<!-- === GIAO DIỆN NGƯỜI DÙNG KHÁCH (Bài Viết) === -->
<?php foreach ($hoa_list as $hoa): ?>
    
<h2><?php echo $hoa['ten_hoa']; ?></h2>
<p><?php echo $hoa['mo_ta']; ?></p>

<img src="<?php echo $hoa['anh']; ?>" alt="<?php echo $hoa['ten_hoa']; ?>">

<hr>

<?php endforeach; ?>