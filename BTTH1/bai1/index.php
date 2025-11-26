<?php
// File chính điều khiển logic phân quyền và nhúng các file con

// SỬA: Sử dụng __DIR__ để đảm bảo PHP tìm đúng file data.php
require_once __DIR__ . '/data.php'; 

// === PHÂN QUYỀN GIẢ LẬP ĐÃ CHỈNH SỬA ===
// Mặc định là chế độ Khách (false)
$is_admin = false; 

// 1. Kiểm tra tham số 'mode' trong URL
if (isset($_GET['mode'])) {
    // Nếu 'mode' là 'admin', bật chế độ quản trị
    if ($_GET['mode'] === 'admin') {
        $is_admin = true;
    }
    // Ngược lại, nếu 'mode' là 'guest' hoặc không hợp lệ, giữ nguyên Khách (false)
}
// Nếu không có tham số 'mode', $is_admin vẫn là false (chế độ Khách mặc định)
// =======================================

// Kiểm tra nếu $hoa_list không tồn tại
if (!isset($hoa_list) || !is_array($hoa_list)) {
    die("Lỗi: Không thể load dữ liệu từ file data.php hoặc \$hoa_list không phải là mảng.");
}

// Xác định chế độ đối diện để tạo liên kết chuyển đổi
$next_mode = $is_admin ? 'guest' : 'admin';
$next_mode_text = $is_admin ? 'Chuyển sang Khách' : 'Chuyển sang Quản trị';

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Danh sách các loài hoa - <?php echo $is_admin ? 'Quản trị' : 'Khách'; ?></title>
    <style>
        body {
            /* Giới hạn chiều rộng tùy theo chế độ */
            max-width: <?php echo $is_admin ? '1000px' : '800px'; ?>; 
            margin: 0 auto;
            padding: 20px;
            font-family: Arial, sans-serif;
        }
        h1 {
            border-bottom: 2px solid #ccc;
            padding-bottom: 10px;
            margin-bottom: 30px;
        }

        /* Thêm CSS cho nút chuyển chế độ */
        .mode-switcher {
            display: block;
            margin-bottom: 20px;
            text-align: right;
        }
        .mode-switcher a {
            padding: 8px 15px;
            background-color: #007bff; /* Màu xanh dương cho nút */
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            transition: background-color 0.3s;
        }
        .mode-switcher a:hover {
            background-color: #0056b3;
        }

        <?php if (!$is_admin): ?>
        /* ------------------------------------------------------------------- */
        /* CSS CHẾ ĐỘ NGƯỜI DÙNG KHÁCH (Dạng Bài Viết) */
        /* ------------------------------------------------------------------- */
        h2 {
            font-size: 18px; 
            font-weight: bold;
            color: black;
            margin-top: 25px; 
        }
        p {
            font-size: 14px;
            line-height: 1.6;
            text-align: justify; 
        }
        img {
            max-width: 100%; 
            height: auto;
            display: block;
            margin: 15px 0 0 0;
        }
        
        <?php else: ?>
        /* ------------------------------------------------------------------- */
        /* CSS CHẾ ĐỘ QUẢN TRỊ (Dạng Bảng CRUD) */
        /* ------------------------------------------------------------------- */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            vertical-align: top;
        }
        th {
            background-color: #f2f2f2;
        }
        .img-thumb {
            width: 80px;
            height: auto;
            display: block;
        }
        .action-btn {
            padding: 5px 10px;
            margin-right: 5px;
            text-decoration: none;
            color: white;
            border-radius: 3px;
            font-size: 12px;
        }
        .btn-edit { background-color: #ffc107; }
        .btn-delete { background-color: #dc3545; }
        .btn-add {
            display: inline-block;
            padding: 10px 15px;
            background-color: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        <?php endif; ?>
    </style>
</head>
<body>
    <h1>Danh sách các loài hoa (Chế độ: <?php echo $is_admin ? 'Quản trị' : 'Khách'; ?>)</h1>

    <div class="mode-switcher">
        <a href="<?php echo basename($_SERVER['PHP_SELF']) . '?mode=' . $next_mode; ?>">
            <?php echo $next_mode_text; ?>
        </a>
    </div>
    <?php if ($is_admin): ?>
    
    <?php require_once __DIR__ . '/admin.php'; ?>
    
    <?php else: ?>
    
    <?php require_once __DIR__ . '/guest.php'; ?>

    <?php endif; ?>

</body>
</html>