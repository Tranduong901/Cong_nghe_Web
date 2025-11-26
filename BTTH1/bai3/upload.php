<?php
// Tên tệp tin CSV cần đọc. Đảm bảo tệp này nằm cùng thư mục với script PHP.
$csv_file = '65HTTT_Danh_sach_diem_danh.csv';
$data = []; // Mảng chứa toàn bộ dữ liệu CSV

// Kiểm tra xem tệp tin có tồn tại không
if (!file_exists($csv_file)) {
    $error_message = "Lỗi: Không tìm thấy tệp tin CSV: " . htmlspecialchars($csv_file);
} else {
    // Mở tệp tin để đọc. 'r' là chế độ chỉ đọc.
    // Dùng fgetcsv là phương pháp mạnh mẽ và chuẩn nhất để xử lý CSV.
    if (($handle = fopen($csv_file, "r")) !== FALSE) {
        // Đọc từng dòng CSV, với dấu phân cách là dấu phẩy (','), bao bọc là dấu ngoặc kép ('"')
        while (($row = fgetcsv($handle, 1000, ",", '"')) !== FALSE) {
            // Loại bỏ các dòng trống hoặc không có dữ liệu
            if (count(array_filter($row)) > 0) {
                $data[] = $row;
            }
        }
        fclose($handle); // Đóng tệp tin sau khi đọc xong

        if (empty($data)) {
            $error_message = "Cảnh báo: Tệp tin CSV trống hoặc không có dữ liệu hợp lệ.";
        } else {
            // Tách Tiêu đề (Header) khỏi Dữ liệu (Body)
            $headers = array_shift($data);
        }
    } else {
        $error_message = "Lỗi: Không thể mở tệp tin " . htmlspecialchars($csv_file) . " để đọc.";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hiển Thị Danh Sách Tài Khoản</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f4f4f4; }
        .container { max-width: 1200px; margin: 0 auto; background-color: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); }
        h1 { color: #007bff; text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #007bff; color: white; position: sticky; top: 0; }
        tbody tr:nth-child(even) { background-color: #f9f9f9; }
        .error { color: red; font-weight: bold; text-align: center; padding: 15px; border: 1px solid red; background-color: #ffe6e6; border-radius: 5px; }
        .db-info { margin-top: 30px; padding: 20px; background-color: #e6f7ff; border: 1px solid #91d5ff; border-radius: 5px; }
    </style>
</head>
<body>

<div class="container">
    <h1>Danh Sách Tài Khoản</h1>

    <?php
    if (isset($error_message)) {
        echo "<p class='error'>{$error_message}</p>";
    } elseif (!empty($data)) {
        // --- Hiển thị Bảng Dữ Liệu ---
        echo "<table>";

        // Tiêu đề (Header)
        echo "<thead><tr>";
        foreach ($headers as $header) {
            echo "<th>" . htmlspecialchars(trim($header)) . "</th>";
        }
        echo "</tr></thead>";

        // Dữ liệu (Body)
        echo "<tbody>";
        foreach ($data as $row) {
            echo "<tr>";
            // Lặp qua các cột dựa trên số lượng tiêu đề để tránh lỗi
            for ($i = 0; $i < count($headers); $i++) {
                $cell_content = isset($row[$i]) ? $row[$i] : '';
                echo "<td>" . htmlspecialchars(trim($cell_content)) . "</td>";
            }
            echo "</tr>";
        }
        echo "</tbody>";
        echo "</table>";

    }
    ?>
</div>

</body>
</html>