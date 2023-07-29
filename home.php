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
      <div class="search-block">
         <div class="search-box">
            <h1>JDUコミュニティへようこそ</h1>
            <p>つながろう、共に楽しもう！</p>
            <div class="search">
               <form action="home.php" method="GET">
                  <input class="search-input" type="text" name="search_query" placeholder="ユーザー名、ポスト名" required>
                  <button type="submit" class="search-btn">検索</button>
               </form>
            </div>
         </div>
         <?php
         if (isset($_GET['search_query'])) {
            $search_query = $_GET['search_query'];

            // Search for topics matching the search query
            $select_topics = mysqli_query($conn, "SELECT * FROM `topics` WHERE 
         post_title LIKE '%$search_query%' OR main_text LIKE '%$search_query%'") or die('topic query failed');

            // Search for users matching the search query
            $select_users = mysqli_query($conn, "SELECT * FROM `user_form` WHERE 
         name LIKE '%$search_query%' OR surname LIKE '%$search_query%'") or die('user query failed');

            echo '<div class="search-result">';
            // Display the search results
            echo '<h2>検索結果: ' . htmlspecialchars($_GET['search_query']) . '</h2>';

            // Display topic results
            if (mysqli_num_rows($select_topics) > 0) {
               echo '<h3>ポスト:</h3>';
               while ($topic = mysqli_fetch_assoc($select_topics)) {
                  // Add anchor link to the topic post
                  echo '<a href="post.php?post_id=' . $topic['id'] . '">';
                  echo '<div class="search-result-card">';
                  echo '<img class="search-result-image" src="' . $topic['post_image'] . '">';
                  echo '<h2 class="search-result-text">' . $topic['post_title'] . '</h2>';
                  echo '</div>';
                  echo '</a>';
               }
            }

            // Display user results
            if (mysqli_num_rows($select_users) > 0) {
               echo '<h3>ユーザー:</h3>';
               while ($user = mysqli_fetch_assoc($select_users)) {
                  // Add anchor link to the user profile
                  echo '<a href="profile.php?user_id=' . $user['id'] . '">';
                  echo '<div class="search-result-card">';
                  echo '<img class="search-result-image" src="' . ($user['image'] ? 'uploaded_img/' . $user['image'] : 'images/default-avatar.png') . '">';
                  echo '<h2 class="search-result-text">' . $user['name'] . ' ' . $user['surname'] . '</h2>';
                  echo '</div>';
                  echo '</a>';
               }
            }


            // If no topics or users found
            if (mysqli_num_rows($select_topics) === 0 && mysqli_num_rows($select_users) === 0) {
               echo '<p>No results found.</p>';
            }
            echo '</div>';
         }
         ?>

      </div>
      <div class="test"></div>
   </div>

   <script src="search.js"></script>
   <script src="script.js"></script>
</body>

</html>