<?php
include 'config.php';
session_start();
$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
    header('location:login.php');
    exit();
}

if (isset($_GET['logout'])) {
    unset($_SESSION['user_id']);
    session_destroy();
    header('location:login.php');
    exit();
}

// Get user role
$user_role = '';
$select_user = mysqli_query($conn, "SELECT role FROM `user_form` WHERE id = '$user_id'") or die('query failed');
if (mysqli_num_rows($select_user) > 0) {
    $fetch_user = mysqli_fetch_assoc($select_user);
    $user_role = $fetch_user['role'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if the create_topic button was clicked
    if (isset($_POST['create_topic'])) {
        // Retrieve the form data
        $topic_name = mysqli_real_escape_string($conn, $_POST['topic_name']);
        $topic_text = mysqli_real_escape_string($conn, $_POST['topic_text']);
        $topic_theme = mysqli_real_escape_string($conn, $_POST['topic_theme']);

        // Handle image upload if applicable
        $topic_image = '';
        if ($_FILES['topic_image']['error'] === UPLOAD_ERR_OK) {
            $temp_file = $_FILES['topic_image']['tmp_name'];
            $file_name = $_FILES['topic_image']['name'];
            $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
            $unique_file_name = uniqid() . '_' . $file_name;
            $topic_image = 'post_img/' . $unique_file_name;
            move_uploaded_file($temp_file, $topic_image);
        }

        // Insert the topic into the database
        $insert_query = "INSERT INTO topics (user_id, post_image, post_title, main_text, main_theme, created_time) VALUES ('$user_id', '$topic_image', '$topic_name', '$topic_text', '$topic_theme', NOW())";
        $result = mysqli_query($conn, $insert_query);
        if ($result) {
            // Topic created successfully, redirect to the forum or any other desired location
            header('location: forum.php');
            exit();
        } else {
            echo 'Error creating topic: ' . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Topic</title>
</head>
<!-- custom css file link  -->
<link rel="stylesheet" href="style.css">

<body>
    <div class="circles">
        <div class="circle"></div>
        <div class="circle"></div>
        <div class="circle"></div>
        <div class="circle"></div>
    </div>
    <div class="container">
        <div class="profile">
            <a href="home.php"><img class="logo" src="logo.svg" alt="logo"></a>
            <div class="nav">
                <ul>
                    <li><a href="forum.php">フォーラム</a></li>
                    <li><a href="news.php">ニュース</a></li>
                    <li><a href="about.php">よくある質問</a></li>
                </ul>
            </div>
            <div class="add-post">
                <?php
                $select = mysqli_query($conn, "SELECT * FROM `user_form` WHERE id = '$user_id'") or die('query failed');
                if (mysqli_num_rows($select) > 0) {
                    $fetch = mysqli_fetch_assoc($select);
                }
                if ($fetch['image'] == '') {
                    echo '<img id="avatar" src="images/default-avatar.png">';
                } else {
                    echo '<img id="avatar" src="uploaded_img/' . $fetch['image'] . '">';
                }
                ?>
            </div>

            <div id="dialog-box" class="dialog-box">
                <h3><?php echo $fetch['name']; ?></h3>
                <?php if ($user_role === 'admin') : ?>
                    <a href="dashboard.php" class="dialog-btn">ダッシュボード</a>
                <?php endif; ?>
                <a href="update_profile.php" class="dialog-btn">プロフィール編集</a>
                <a href="home.php?logout=<?php echo $user_id; ?>" class="dialog-delete-btn">ログアウト</a>
            </div>

        </div>
        <div class="update-profile">
            <form action="" method="post" enctype="multipart/form-data">
                <div class="flex">
                    <div class="postBox">
                        <div class="file-upload">
                            <input type="file" id="upload" name="topic_image" class="box" accept="image/jpg, image/jpeg, image/png">
                            <label for="upload">イメージをアップロード</label>
                        </div><br>
                        <span>タイトル :</span>
                        <input type="text" name="topic_name" class="box" required>
                        <span>テキスト:</span>
                        <textarea id="text" name="topic_text" rows="10" cols="30" class="box" required></textarea>
                        <label for="theme">メインテーマを選択してください:</label>
                        <select class="box" id="theme" name="topic_theme">
                            <option value="日本語">日本語</option>
                            <option value="勉強">勉強</option>
                            <option value="日本文化">日本文化</option>
                            <option value="IT">IT</option>
                            <option value="アニメ">アニメ</option>
                        </select>
                    </div>
                </div>
                <input type="submit" value="トピックを追加" name="create_topic" class="btn">
                <a href="home.php" class="delete-btn">戻る</a>
            </form>
        </div>
    </div>
    <script src="script.js"></script>
</body>

</html>