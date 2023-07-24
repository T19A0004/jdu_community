<?php
include 'config.php';
session_start();
$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
   header('location:login.php');
   exit();
}

if (isset($_POST['update_profile'])) {
   $update_name = mysqli_real_escape_string($conn, $_POST['update_name']);
   $update_surname = mysqli_real_escape_string($conn, $_POST['update_surname']);
   $update_email = mysqli_real_escape_string($conn, $_POST['update_email']);
   $update_gender = mysqli_real_escape_string($conn, $_POST['update_gender']);

   mysqli_query($conn, "UPDATE `user_form` SET name = '$update_name', surname = '$update_surname', email = '$update_email', gender = '$update_gender' WHERE id = '$user_id'") or die('query failed');

   $update_image = $_FILES['update_image']['name'];
   $update_image_size = $_FILES['update_image']['size'];
   $update_image_tmp_name = $_FILES['update_image']['tmp_name'];
   $update_image_ext = pathinfo($update_image, PATHINFO_EXTENSION);
   $unique_update_image_name = uniqid() . '_' . $update_image;
   $update_image_folder = 'uploaded_img/' . $unique_update_image_name;

   if (!empty($update_image)) {
      if ($update_image_size > 2000000) {
         $message[] = '画像のサイズが大きすぎます！';
      } else {
         $image_update_query = mysqli_query($conn, "UPDATE `user_form` SET image = '$unique_update_image_name' WHERE id = '$user_id'") or die('query failed');
         if ($image_update_query) {
            move_uploaded_file($update_image_tmp_name, $update_image_folder);
         }
         $message[] = '画像が正常に更新されました！';
      }
   }

   $old_pass = $_POST['old_pass'];
   $new_pass = $_POST['new_pass'];
   $confirm_pass = $_POST['confirm_pass'];

   if (!empty($new_pass) || !empty($confirm_pass)) {
      $select = mysqli_query($conn, "SELECT * FROM `user_form` WHERE id = '$user_id'") or die('query failed');
      if (mysqli_num_rows($select) > 0) {
         $fetch = mysqli_fetch_assoc($select);
         $old_password_hash = $fetch['password'];

         if (password_verify($old_pass, $old_password_hash)) {
            if ($new_pass != $confirm_pass) {
               $message[] = '新しいパスワードが一致しません！';
            } else {
               $new_password_hash = password_hash($new_pass, PASSWORD_DEFAULT);
               mysqli_query($conn, "UPDATE `user_form` SET password = '$new_password_hash' WHERE id = '$user_id'") or die('query failed');
               $message[] = 'パスワードが正常に更新されました！';
            }
         } else {
            $message[] = '古いパスワードが一致しません！';
         }
      } else {
         $message[] = 'ユーザーが見つかりませんでした！';
      }
   }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>プロフィール編集</title>

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
      <div class="update-profile">

         <?php
         $select = mysqli_query($conn, "SELECT * FROM `user_form` WHERE id = '$user_id'") or die('query failed');
         if (mysqli_num_rows($select) > 0) {
            $fetch = mysqli_fetch_assoc($select);
         }
         ?>

         <form action="" method="post" enctype="multipart/form-data">
            <?php
            if ($fetch['image'] == '') {
               echo '<img class="forum-img" src="images/default-avatar.png">';
            } else {
               echo '<img class="forum-img" src="uploaded_img/' . $fetch['image'] . '">';
            }
            if (isset($message)) {
               foreach ($message as $msg) {
                  echo '<div class="message">' . $msg . '</div>';
               }
            }
            ?>
            <div class="flex">
               <div class="inputBox">
                  <span>名前 :</span>
                  <input type="text" name="update_name" value="<?php echo $fetch['name']; ?>" class="box">
                  <span>名字 :</span>
                  <input type="text" name="update_surname" value="<?php echo $fetch['surname']; ?>" class="box">
                  <span>メール :</span>
                  <input type="email" name="update_email" value="<?php echo $fetch['email']; ?>" class="box">
                  <span>アバター :</span>
                  <div class="file-upload">
                     <input type="file" id="upload" name="update_image" class="box" accept="image/jpg, image/jpeg, image/png">
                     <label for="upload">ファイルをアップロード</label>
                  </div>
               </div>
               <div class="inputBox">
                  <input type="hidden" name="old_pass" value="<?php echo $fetch['password']; ?>">
                  <span>古いパスワード :</span>
                  <input type="password" name="old_pass" placeholder="以前のパスワードを入力してください" class="box">
                  <span>新しいパスワード :</span>
                  <input type="password" name="new_pass" placeholder="新しいパスワードを入力してください" class="box">
                  <span>パスワード認証 :</span>
                  <input type="password" name="confirm_pass" placeholder="新しいパスワードを確認してください" class="box">
                  <span>性別 :</span>
                  <select name="update_gender" class="box">
                     <option value="" disabled <?php if (empty($fetch['gender'])) echo 'selected'; ?>>性別を選択してください</option>
                     <option value="male" <?php if ($fetch['gender'] === 'male') echo 'selected'; ?>>男性</option>
                     <option value="female" <?php if ($fetch['gender'] === 'female') echo 'selected'; ?>>女性</option>
                  </select>
               </div>
            </div>
            <input type="submit" value="編集" name="update_profile" class="btn">
            <a href="home.php" class="delete-btn">戻る</a>
         </form>

      </div>
   </div>
</body>

</html>