<?php
include 'config.php';
session_start();

if (isset($_POST['submit'])) {

   $email = mysqli_real_escape_string($conn, $_POST['email']);
   $password = mysqli_real_escape_string($conn, $_POST['password']);

   $select = mysqli_query($conn, "SELECT * FROM `user_form` WHERE email = '$email'") or die('query failed');

   if (mysqli_num_rows($select) > 0) {
      $row = mysqli_fetch_assoc($select);
      $stored_password = $row['password'];

      if (password_verify($password, $stored_password)) {
         $_SESSION['user_id'] = $row['id'];
         header('location:home.php');
      } else {
         $message[] = '誤ったメールアドレスまたはパスワード！';
      }
   } else {
      $message[] = '誤ったメールアドレスまたはパスワード！';
   }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>ログイン</title>

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
      <div class="form-container">

         <form action="" method="post" enctype="multipart/form-data">
            <h3>ログイン</h3>
            <input type="email" name="email" placeholder="メールを入力" class="box" required>
            <input type="password" name="password" placeholder="パスワードを入力" class="box" required>
            <input type="submit" name="submit" value="ログイン" class="btn">
            <p>アカウントをお持ちでない場合は、<a href="register.php">こちらから登録</a>してください。</p>
         </form>

      </div>
   </div>



</body>

</html>