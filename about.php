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
            <h1>よくある質問</h1>
        </div>
        <div class="about-block">
            <div class="about-section">
                <h1>JDUコミュニティの創造者</h1>
                <div class="about-creators">
                    <div class="about-creators-card">
                        <img src="creators_img/man.jpg" alt="プロジェクトマネージャー">
                        <h1>リクシエフ・コミルジョン</h1>
                        <p>プロジェクトマネージャー</p>
                    </div>
                    <div class="about-creators-card">
                        <img src="creators_img/islombek.jpg" alt="バックエンドデベロッパー">
                        <h1>イスロムベク・カモロフ</h1>
                        <p>バックエンドデベロッパー</p>
                    </div>
                    <div class="about-creators-card">
                        <img src="creators_img/malika.jpg" alt="デザイナー">
                        <h1>ケンジャエワ・マリカ</h1>
                        <p>デザイナー</p>
                    </div>
                    <div class="about-creators-card">
                        <img src="creators_img/jonibek.jpg" alt="フロントエンドデベロッパー">
                        <h1>ジョニベク・アブディヴァイトフ</h1>
                        <p>フロントエンドデベロッパー</p>
                    </div>
                    <div class="about-creators-card">
                        <img src="creators_img/bunyod.jpg" alt="フロントエンドデベロッパー">
                        <h1>ジュラエフ・ブニヨッド</h1>
                        <p>フロントエンドデベロッパー</p>
                    </div>
                </div>
                <h1>JDUコミュニティの目標</h1>
                <div class="about-target">「JDUコミュニティ」は、大学の学生たちが集まる場所です。私たちの目標は、学生たちがコミュニケーションを取りながら、共に成長し、学び合うことを促進することです。

                    「JDUコミュニティ」は、学生たちがアイデアを共有し、お互いをサポートする場所です。多様な視点と意見を尊重し、共に学び合うことでより豊かなキャンパスライフを築いていきましょう。

                    皆さんの積極的な参加を心より歓迎します！一緒に素晴らしいコミュニティを築いていきましょう！
                </div>
                <h1>JDUコミュニティで何をできる</h1>
                <div class="about-plus">
                    このサイトでは、フォーラムにトピックを投稿することができます。学生たちは自分の興味や専門分野について議論し、他のメンバーと意見交換を行うことができます。コメントを投稿することで、さらなるディスカッションを進め、新たな気づきを得ることができるでしょう。

                    また、日本のニュースセクションも設けています。ここでは、学生たちは日本の最新のニュースを閲覧することができ、社会や文化に対する理解を深めることができます。

                    さらに、トピックやユーザーを検索する機能も提供しています。メインページでトピック名やユーザー名を入力することで、関連する情報を簡単に見つけることができます。
                </div>
            </div>

        </div>


    </div>

    <script src="script.js"></script>
</body>

</html>