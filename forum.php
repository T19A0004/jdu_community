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

// Delete post
if (isset($_POST['delete_post'])) {
    $post_id = $_POST['post_id'];
    $delete_query = "DELETE FROM `topics` WHERE id = '$post_id'";

    // Check user's role for deletion permissions
    if ($user_role == 'admin') {
        // Administrators can delete any post
        $delete_query .= " AND 1";
    } else {
        // Regular users can only delete their own posts
        $delete_query .= " AND user_id = '$user_id'";
    }

    $delete_result = mysqli_query($conn, $delete_query);

    if ($delete_result) {
        // Post deleted successfully
        header('Location: forum.php');
        exit();
    } else {
        // Failed to delete post
        echo "Failed to delete the post.";
    }
}

// Retrieve the filter values from the request parameters
$themeFilter = $_GET['theme'] ?? '';
$userFilter = $_GET['user'] ?? '';
$dateFilter = $_GET['date'] ?? '';

// Build the SQL query with the filters
$select_topics = "SELECT topics.*, user_form.name FROM `topics` INNER JOIN `user_form` ON topics.user_id = user_form.id WHERE 1=1";

if ($themeFilter) {
    $select_topics .= " AND main_theme = '$themeFilter'";
}

if ($userFilter) {
    $select_topics .= " AND user_id = '$userFilter'";
}

if ($dateFilter) {
    $select_topics .= " AND DATE(created_time) = '$dateFilter'";
}

$select_topics .= " ORDER BY created_time DESC";

// Execute the SQL query
$select_topics_query = mysqli_query($conn, $select_topics) or die('query failed');

// Check if no filters are selected
$noFiltersSelected = empty($themeFilter) && empty($userFilter) && empty($dateFilter);

// If no filters are selected, retrieve all topics
if ($noFiltersSelected) {
    $select_topics_query = mysqli_query($conn, "SELECT topics.*, user_form.name FROM `topics` INNER JOIN `user_form` ON topics.user_id = user_form.id ORDER BY created_time DESC") or die('query failed');
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ホーム</title>

    <!-- custom css file link  -->
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
                <ul>
                    <li><a href="forum.php">フォーラム</a></li>
                    <li><a href="news.php">ニュース</a></li>
                    <li><a href="about.php">よくある質問</a></li>
                </ul>
            </div>
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

            <div id="dialog-box" class="dialog-box">
                <h3><?php echo $fetch['name']; ?></h3>
                <?php if ($user_role === 'admin') : ?>
                    <a href="dashboard.php" class="dialog-btn">ダッシュボード</a>
                <?php endif; ?>
                <a href="update_profile.php" class="dialog-btn">プロフィール編集</a>
                <a href="home.php?logout=<?php echo $user_id; ?>" class="dialog-delete-btn">ログアウト</a>
            </div>

        </div>
        <div class="section-name">
            <h1>フォーラム</h1>
        </div>
        <div class="forum-page">
            <div class="forum-actions">
                <div class="add-post">
                    <a href="create_post.php">
                        <div class="add-post-btn">
                            トピックを追加
                        </div>
                    </a>
                </div>
                <div class="filter">
                    <form method="GET" action="forum.php">
                        <div class="filter-block"><label for="theme-filter">テーマでフィルター:</label>
                            <select class="box" id="theme-filter" name="theme">
                                <option value="">全てのテーマ</option>
                                <option value="日本語">日本語</option>
                                <option value="勉強">勉強</option>
                                <option value="日本文化">日本文化</option>
                                <option value="IT">IT</option>
                                <option value="アニメ">アニメ</option>
                            </select>
                        </div>
                        <div class="filter-block"><label for="user-filter">ユーザーでフィルター:</label>
                            <select class="box" id="user-filter" name="user">
                                <option value="">全てのユーザー</option>
                                <!-- Retrieve and display the list of users dynamically from the database -->
                                <?php
                                $user_query = mysqli_query($conn, "SELECT * FROM `user_form`") or die('query failed');
                                while ($user = mysqli_fetch_assoc($user_query)) {
                                    echo '<option value="' . $user['id'] . '">' . $user['name'] . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="filter-block"><label for="date-filter">日付でフィルター:</label>
                            <input class="box" type="date" id="date-filter" name="date">
                        </div>
                        <input type="submit" value="フィルター" class="btn">
                    </form>
                </div>
            </div>
            <div class="forum-posts">
                <?php
                $select_topics = mysqli_query($conn, "SELECT topics.*, user_form.name FROM `topics` INNER JOIN `user_form` ON topics.user_id = user_form.id ORDER BY created_time DESC") or die('query failed');
                if (mysqli_num_rows($select_topics) > 0) {
                    while ($topic = mysqli_fetch_assoc($select_topics_query)) {
                        echo '<div class="post-card">';
                        echo '<a href="post.php?post_id=' . $topic['id'] . '"><img class="forum-img" src="' . $topic['post_image'] . '" alt="Post Image"></a>';
                        echo '<h2>' . $topic['post_title'] . '</h2>';
                        echo '<p>テーマ: ' . $topic['main_theme'] . '</p>';
                        echo '<p>作者: ' . $topic['name'] . '</p>';
                        echo '<p>投稿日時: ' . date('Y-m-d', strtotime($topic['created_time'])) . '</p>';


                        // Display edit button based on user's role and post ownership
                        if ($user_role == 'admin' || $topic['user_id'] == $user_id) {
                            echo '<a class="btn" href="editpost.php?post_id=' . $topic['id'] . '">編集</a>';
                        }

                        // Display delete button based on user's role and post ownership
                        if ($user_role == 'admin' || $topic['user_id'] == $user_id) {
                            echo '<form method="POST" onsubmit="return confirm(\'本当に削除したいですか。?\');">';
                            echo '<input type="hidden" name="post_id" value="' . $topic['id'] . '">';
                            echo '<button type="submit" name="delete_post" class="delete-btn">削除</button>';
                            echo '</form>';
                        }


                        echo '</div>';
                    }
                } else {
                    echo 'No topics found.';
                }
                ?>
            </div>

        </div>


    </div>

    <script src="script.js"></script>
</body>

</html>