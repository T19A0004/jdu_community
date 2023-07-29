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
    // Check if the update_topic button was clicked
    if (isset($_POST['update_topic'])) {
        // Retrieve the form data
        $topic_name = $_POST['topic_name'];
        $topic_text = $_POST['topic_text'];
        $topic_theme = $_POST['topic_theme'];
        $post_id = $_POST['post_id'];

        // Fetch the existing image path from the database
        $select_topic = mysqli_query($conn, "SELECT post_image FROM topics WHERE id = $post_id");
        if (mysqli_num_rows($select_topic) > 0) {
            $topic = mysqli_fetch_assoc($select_topic);
            $topic_image = $topic['post_image'];
        }

        // Handle image upload if a new image is selected
        if ($_FILES['topic_image']['error'] === UPLOAD_ERR_OK) {
            // Remove the existing image file if it exists
            if (!empty($topic_image)) {
                unlink($topic_image);
            }

            // A new image file has been uploaded
            $temp_file = $_FILES['topic_image']['tmp_name'];
            $file_name = $_FILES['topic_image']['name'];
            $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
            $unique_file_name = uniqid() . '_' . $file_name;
            $topic_image = 'post_img/' . $unique_file_name;
            move_uploaded_file($temp_file, $topic_image);
        }

        // Use prepared statement to update the topic in the database
        $update_query = "UPDATE topics SET post_image = ?, post_title = ?, main_text = ?, main_theme = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $update_query);
        mysqli_stmt_bind_param($stmt, "ssssi", $topic_image, $topic_name, $topic_text, $topic_theme, $post_id);

        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        if ($result) {
            // Topic updated successfully, redirect to the forum or any other desired location
            header('location: forum.php');
            exit();
        } else {
            echo 'Error updating topic: ' . mysqli_error($conn);
        }
    }
} else {
    // Check if the post ID is provided in the URL
    if (isset($_GET['post_id'])) {
        $post_id = $_GET['post_id'];

        // Fetch the post data from the database
        $select_topic = mysqli_query($conn, "SELECT * FROM topics WHERE id = $post_id");
        if (mysqli_num_rows($select_topic) > 0) {
            $topic = mysqli_fetch_assoc($select_topic);

            // Pre-fill the form fields with the fetched data
            $topic_name = $topic['post_title'];
            $topic_text = $topic['main_text'];
            $topic_theme = $topic['main_theme'];
            $topic_image = isset($topic['post_image']) ? $topic['post_image'] : '';
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
    <title>Edit Topic</title>
    <!-- custom css file link -->
    <link rel="stylesheet" href="style.css">
</head>

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
                <div id="menu" class="menu">
                    <img src="menu.svg" alt="menu">
                </div>
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

            <div id="menu-box" class="menu-box">
                <ul>
                    <li><a href="forum.php">フォーラム</a></li>
                    <li><a href="news.php">ニュース</a></li>
                    <li><a href="about.php">よくある質問</a></li>
                </ul>
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
                        <div class="post-image">
                            <?php
                            if (!empty($topic_image)) {
                                echo '<img id="post-img" src="' . $topic_image . '">';
                            }

                            ?>
                        </div>

                        <div class="file-upload">
                            <label for="upload" class="box-label">ファイルをアップロード</label>
                            <input type="file" id="upload" name="topic_image" class="box" accept="image/jpg, image/jpeg, image/png">
                            <div id="preview-container" class="preview-container"></div>
                        </div>
                        <br>
                        <span>タイトル :</span>
                        <input type="text" name="topic_name" class="box" value="<?php echo $topic_name; ?>" required>
                        <span>テキスト:</span>
                        <textarea id="text" name="topic_text" rows="10" cols="30" class="box" required><?php echo $topic_text; ?></textarea>
                        <label for="theme">メインテーマを選択してください:</label>
                        <select class="box" id="theme" name="topic_theme">
                            <option value="日本語" <?php if ($topic_theme == '日本語') echo 'selected'; ?>>日本語</option>
                            <option value="勉強" <?php if ($topic_theme == '勉強') echo 'selected'; ?>>勉強</option>
                            <option value="日本文化" <?php if ($topic_theme == '日本文化') echo 'selected'; ?>>日本文化</option>
                            <option value="IT" <?php if ($topic_theme == 'IT') echo 'selected'; ?>>IT</option>
                            <option value="アニメ" <?php if ($topic_theme == 'アニメ') echo 'selected'; ?>>アニメ</option>
                        </select>
                        <input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
                    </div>
                </div>
                <input type="submit" value="トピックを更新" name="update_topic" class="btn">
                <a href="home.php" class="delete-btn">戻る</a>
            </form>

        </div>
    </div>
    <script src="script.js"></script>
    <script src="preview.js"></script>
</body>

</html>