<?php
// File này hiển thị chế độ Quản trị, được nhúng vào index.php

// Đảm bảo biến $hoa_list tồn tại trước khi lặp
if (!isset($hoa_list) || !is_array($hoa_list)) {
    return; // Dừng lại nếu dữ liệu không sẵn sàng
}
?>

<!-- === GIAO DIỆN QUẢN TRỊ (Bảng CRUD) === -->
<a href="#" class="btn-add">Thêm mới</a>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Ảnh</th>
            <th>Tên Hoa</th>
            <th>Mô Tả Tóm Tắt</th>
            <th>Thao Tác</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($hoa_list as $hoa): ?>
        <tr>
            <td><?php echo $hoa['id']; ?></td>
            <td><img src="<?php echo $hoa['anh']; ?>" alt="<?php echo $hoa['ten_hoa']; ?>" class="img-thumb"></td>
            <td><?php echo $hoa['ten_hoa']; ?></td>
            <td><?php echo substr($hoa['mo_ta'], 0, 100) . '...'; ?></td>
            <td>
                <a href="#" class="action-btn btn-edit">Sửa</a>
                <a href="#" class="action-btn btn-delete">Xóa</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>