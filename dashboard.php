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

// Check if the user is not an admin
if ($user_role !== 'admin') {
    header('location:home.php'); // Redirect to the desired page for non-admin users
    exit();
}

// Get the count of registered users
$user_count_query = mysqli_query($conn, "SELECT COUNT(*) AS total_users FROM `user_form`") or die('query failed');
$user_count_result = mysqli_fetch_assoc($user_count_query);
$total_users = $user_count_result['total_users'];

// Get the count of posts
$post_count_query = mysqli_query($conn, "SELECT COUNT(*) AS total_posts FROM `topics`") or die('query failed');
$post_count_result = mysqli_fetch_assoc($post_count_query);
$total_posts = $post_count_result['total_posts'];

//Get the count of likes
$like_count_query = mysqli_query($conn, "SELECT COUNT(*) AS total_likes FROM `likes`") or die('query failed');
$like_count_result = mysqli_fetch_assoc($like_count_query);
$total_likes = $like_count_result['total_likes'];

//Get the count of comments
$comment_count_query = mysqli_query($conn, "SELECT COUNT(*) AS total_comments FROM `comments`") or die('query failed');
$comment_count_result = mysqli_fetch_assoc($comment_count_query);
$total_comments = $comment_count_result['total_comments'];
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
    <script>
        var dashboardData = {
            totalUsers: <?php echo $total_users; ?>,
            totalPosts: <?php echo $total_posts; ?>,
            totalLikes: <?php echo $total_likes; ?>,
            totalComments: <?php echo $total_comments; ?>
        };
    </script>
    <!-- For graph -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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
        <div class="section-name">
            <h1>ダッシュボード</h1>
        </div>
        <div class="dashboard-info">
            <div class="dashboard-stats">
                <div class="dashboard-stat">
                    <h2>ユーザー数</h2>
                    <p><?php echo $total_users; ?></p>
                </div>
                <div class="dashboard-stat">
                    <h2>ポスト数</h2>
                    <p><?php echo $total_posts; ?></p>
                </div>
                <div class="dashboard-stat">
                    <h2>いいね数</h2>
                    <p><?php echo $total_likes; ?></p>
                </div>
                <div class="dashboard-stat">
                    <h2>コメント数</h2>
                    <p><?php echo $total_comments; ?></p>
                </div>

            </div>
            <div class="dashboard-graph-block">
                <div class="dashboard-graph-container">
                    <canvas id="dashboard-graph" height="200"></canvas>
                </div>
            </div>
        </div>




    </div>

    <script src="script.js"></script>
    <script>
        // Access the data from PHP
        var totalUsers = dashboardData.totalUsers;
        var totalPosts = dashboardData.totalPosts;
        var totalLikes = dashboardData.totalLikes;
        var totalComments = dashboardData.totalComments;

        // Get the canvas element where the chart will be displayed
        var ctx = document.getElementById('dashboard-graph').getContext('2d');

        // Create the chart using Chart.js
        var dashboardChart = new Chart(ctx, {
            type: 'bar', // You can choose the chart type (bar, line, pie, etc.)
            data: {
                labels: ['ユーザー数', 'ポスト数', 'いいね数', 'コメント数'],
                datasets: [{
                    label: 'コミュニティ情報',
                    data: [totalUsers, totalPosts, totalLikes, totalComments],
                    backgroundColor: ['rgba(54, 162, 235, 0.6)', 'rgba(255, 206, 86, 0.6)', 'rgba(255, 99, 132, 0.6)', 'rgba(75, 255, 192, 0.6)'],
                    borderColor: ['rgba(54, 162, 235, 1)', 'rgba(255, 206, 86, 1)', 'rgba(255, 99, 132, 1)', 'rgba(75, 255, 192, 1)'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>

</body>

</html>