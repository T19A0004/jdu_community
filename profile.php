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

$users_id = $_GET['user_id'];

// Retrieve user information from user_form table
$select_profile = mysqli_query($conn, "SELECT * FROM `user_form` WHERE id = '$users_id'") or die('query failed');
if (mysqli_num_rows($select_profile) > 0) {
    $user_data = mysqli_fetch_assoc($select_profile);
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
        <div class="section-name">
            <h1>Profile</h1>
        </div>
        <div class="profile-page">
            <div class="profile-page-block <?php echo $gender_class; ?>">
                <div class="profile-page-img">
                    <?php
                    if ($user_data['image'] == '') {
                        echo '<img src="images/default-avatar.png" alt="User Avatar">';
                    } else {
                        echo '<img src="uploaded_img/' . $user_data['image'] . '" alt="User Avatar">';
                    }
                    ?>
                </div>
                <div class="profile-page-info">
                    <h2><?php echo $user_data['name'] . ' ' . $user_data['surname']; ?></h2>
                    <!-- Display other user information here -->
                    <!-- For example: Email, Date of Birth, Address, etc. -->
                    <!-- You can access the information from the $user_data array -->

                </div>
            </div>
        </div>
    </div>

    <script src="script.js"></script>
</body>

</html>