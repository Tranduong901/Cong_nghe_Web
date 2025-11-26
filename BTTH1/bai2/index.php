<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bài Thi Trắc Nghiệm</title>
    <style>
        /* CSS cơ bản để trình bày bài thi */
        body { font-family: Arial, sans-serif; background-color: #f4f4f9; color: #333; line-height: 1.6; margin: 0; padding: 20px; }
        .container { background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); max-width: 800px; margin: 20px auto; }
        h1 { text-align: center; color: #007bff; margin-bottom: 30px; }
        .question-block { margin-bottom: 25px; padding: 15px; border: 1px solid #ddd; border-radius: 6px; background-color: #fafafa; }
        .question-text { font-weight: bold; margin-bottom: 10px; font-size: 1.1em; }
        .option-label { display: block; margin: 8px 0; cursor: pointer; padding: 5px; border-radius: 4px; }
        input[type="radio"], input[type="checkbox"] { margin-right: 10px; }
        input[type="submit"] { display: block; width: 100%; padding: 12px; background-color: #28a745; color: white; border: none; border-radius: 6px; font-size: 1.1em; cursor: pointer; margin-top: 20px; }
        input[type="submit"]:hover { background-color: #218838; }
        .results { margin-top: 30px; padding: 20px; border-top: 2px solid #007bff; background-color: #e9f7ff; border-radius: 6px; }
        .correct-answer { background-color: #d4edda; border-left: 5px solid #28a745; } /* Đáp án đúng */
        .wrong-answer { background-color: #f8d7da; border-left: 5px solid #dc3545; } /* Đáp án sai/Người dùng chọn sai */
        .selected-answer { border: 2px solid #007bff; } /* Người dùng chọn */
    </style>
</head>
<body>

<?php
// Tên tệp dữ liệu
$quiz_file = 'Quiz.txt';
$questions = [];
$correct_answers = [];
$score = 0;
$total_questions = 0;

## --- Hàm Phân Tích Cú Pháp Tệp ---
function parseQuizFile($file) {
    $questions = [];
    $raw_content = file_get_contents($file);
    if ($raw_content === false) {
        return [];
    }

    // Tách các câu hỏi bằng cách tìm kiếm 1 hoặc nhiều dòng trống liên tiếp
    $raw_questions = preg_split("/\n\s*\n/", $raw_content);

    foreach ($raw_questions as $raw_q) {
        $raw_q = trim($raw_q);
        if (empty($raw_q)) continue;

        // Tách dòng thành các phần
        $lines = explode("\n", $raw_q);

        // Lấy câu hỏi (dòng đầu tiên)
        $question_text = trim(array_shift($lines));

        // Lấy đáp án và câu trả lời
        $options = [];
        $answer = '';
        $is_multiple_choice = false;

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            // Kiểm tra và lấy đáp án đúng
            if (preg_match('/^ANSWER:\s*([A-D, ]+)/i', $line, $matches)) {
                // Hỗ trợ nhiều đáp án (ví dụ: A, C, D)
                $answer = strtoupper(str_replace(' ', '', $matches[1]));
                if (strpos($answer, ',') !== false) {
                    $is_multiple_choice = true;
                }
            }
            // Lấy các tùy chọn (ví dụ: A. TextView)
            else if (preg_match('/^([A-D])\.\s*(.*)/i', $line, $matches)) {
                $options[$matches[1]] = trim($matches[2]);
            }
        }

        if ($question_text && !empty($options) && $answer) {
            $questions[] = [
                'text' => $question_text,
                'options' => $options,
                'answer' => $answer,
                'multiple_choice' => $is_multiple_choice
            ];
        }
    }
    return $questions;
}
## ---------------------------------

$questions = parseQuizFile($quiz_file);
$total_questions = count($questions);

// Lưu trữ đáp án đúng vào một mảng riêng để dễ dàng kiểm tra
foreach ($questions as $index => $q) {
    // Tên trường form sẽ là q_0, q_1, q_2...
    $correct_answers['q_' . $index] = $q['answer'];
}

// Kiểm tra xem form đã được submit chưa
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_answers = $_POST;
    $score = 0;

    echo '<div class="container">';
    echo '<h1>Kết Quả Bài Thi</h1>';

    // Bắt đầu tính điểm và hiển thị kết quả chi tiết
    foreach ($questions as $index => $q) {
        $name = 'q_' . $index;
        $correct_ans = $q['answer'];
        $is_multiple = $q['multiple_choice'];

        // Lấy câu trả lời của người dùng.
        // Đối với trắc nghiệm 1 lựa chọn: $_POST['q_X'] là chuỗi ('A'/'B'/'C'/'D')
        // Đối với trắc nghiệm nhiều lựa chọn: $_POST['q_X'] là mảng ([A, C, D])
        $user_ans = isset($user_answers[$name]) ? $user_answers[$name] : null;

        $is_correct = false;
        $user_ans_display = ''; // Chuỗi đáp án người dùng chọn để hiển thị

        if ($is_multiple) {
            // Xử lý nhiều đáp án (checkbox)
            if (is_array($user_ans)) {
                // Sắp xếp và chuyển thành chuỗi để so sánh dễ dàng hơn
                sort($user_ans);
                $user_ans_str = implode(',', $user_ans);

                // Sắp xếp đáp án đúng để so sánh
                $correct_ans_arr = explode(',', $correct_ans);
                sort($correct_ans_arr);
                $correct_ans_str = implode(',', $correct_ans_arr);

                $is_correct = ($user_ans_str === $correct_ans_str);
                $user_ans_display = $user_ans_str;
            }
        } else {
            // Xử lý một đáp án (radio)
            $is_correct = (strtoupper($user_ans) === $correct_ans);
            $user_ans_display = $user_ans;
        }

        if ($is_correct) {
            $score++;
        }

        // --- Hiển thị câu hỏi kèm kết quả ---
        $class_result = $is_correct ? 'correct-answer' : 'wrong-answer';
        echo '<div class="question-block ' . $class_result . '">';
        echo '<div class="question-text">Câu ' . ($index + 1) . ': ' . $q['text'] . '</div>';

        // Hiển thị các tùy chọn với màu sắc và đánh dấu
        foreach ($q['options'] as $key => $option_text) {
            $is_correct_option = strpos($correct_ans, $key) !== false;

            // Xác định xem người dùng có chọn tùy chọn này không
            $is_user_selected = false;
            if ($is_multiple && is_array($user_ans)) {
                $is_user_selected = in_array($key, $user_ans);
            } elseif (!$is_multiple && $user_ans == $key) {
                $is_user_selected = true;
            }

            $option_class = '';
            if ($is_user_selected && !$is_correct) {
                // Người dùng chọn nhưng là sai
                $option_class = 'wrong-answer';
            } elseif ($is_correct_option) {
                // Đáp án đúng
                $option_class = 'correct-answer';
            } elseif ($is_user_selected) {
                 // Người dùng chọn và là đúng, đã được đánh dấu ở $is_correct_option
                 $option_class = 'correct-answer selected-answer';
            }


            echo '<div class="option-label ' . $option_class . '">';
            echo $key . '. ' . $option_text;
            echo '</div>';
        }

        echo '<p><strong>Đáp án đúng:</strong> ' . $correct_ans . '</p>';
        if (!$is_correct) {
            echo '<p><strong>Đáp án bạn chọn:</strong> ' . ($user_ans_display ? $user_ans_display : 'Chưa trả lời') . '</p>';
        }
        echo '</div>'; // end question-block
    }

    echo '<div class="results">';
    echo '<h2>Điểm Của Bạn: ' . $score . ' / ' . $total_questions . '</h2>';
    echo '</div>';
    echo '</div>'; // end container

} else {
    // --- Hiển Thị Form Bài Thi Lần Đầu ---
    if (empty($questions)) {
        echo '<div class="container"><h1>Không tìm thấy câu hỏi nào hoặc tệp tin bị lỗi!</h1></div>';
        exit;
    }

    echo '<div class="container">';
    echo '<h1>Bài Thi Trắc Nghiệm</h1>';
    // Form sẽ gửi dữ liệu đến chính tệp này để xử lý
    echo '<form method="post" action="index.php">';

    foreach ($questions as $index => $q) {
        $name = 'q_' . $index;
        $input_type = $q['multiple_choice'] ? 'checkbox' : 'radio';

        echo '<div class="question-block">';
        echo '<div class="question-text">Câu ' . ($index + 1) . ': ' . $q['text'] . '</div>';

        foreach ($q['options'] as $key => $option_text) {
            echo '<label class="option-label">';
            // Tên form là 'q_X', Value là 'A'/'B'/'C'/'D'
            // Đối với checkbox, tên form phải là một mảng: name="q_X[]"
            $input_name = $input_type == 'checkbox' ? $name . '[]' : $name;

            echo '<input type="' . $input_type . '" name="' . $input_name . '" value="' . $key . '">';
            echo $key . '. ' . $option_text;
            echo '</label>';
        }
        echo '</div>';
    }

    echo '<input type="submit" value="Nộp Bài">';
    echo '</form>';
    echo '</div>'; // end container
}
?>

</body>
</html>