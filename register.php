<?php
include 'config.php';

if (isset($_POST['submit'])) {
   $name = mysqli_real_escape_string($conn, $_POST['name']);
   $surname = mysqli_real_escape_string($conn, $_POST['surname']);
   $email = mysqli_real_escape_string($conn, $_POST['email']);
   $password = mysqli_real_escape_string($conn, $_POST['password']);
   $confirm_password = mysqli_real_escape_string($conn, $_POST['cpassword']);
   $gender = mysqli_real_escape_string($conn, $_POST['gender']);
   $image = $_FILES['image']['name'];
   $image_size = $_FILES['image']['size'];
   $image_tmp_name = $_FILES['image']['tmp_name'];
   $image_ext = pathinfo($image, PATHINFO_EXTENSION);

   // Check if the user uploaded an image
   if (!empty($image) && $image_size > 0) {
      $unique_image_name = uniqid() . '_' . $image;
      $image_folder = 'uploaded_img/' . $unique_image_name;
   } else {
      // If no image was uploaded, use the default image
      $unique_image_name = 'default-avatar.png';
      $image_folder = 'uploaded_img/' . $unique_image_name;
   }

   $select = mysqli_query($conn, "SELECT * FROM `user_form` WHERE email = '$email'") or die('query failed');

   if (mysqli_num_rows($select) > 0) {
      $message[] = 'ユーザーは既に存在します';
   } else {
      // Password validation using regular expression
      if (strlen($password) < 6 || !preg_match('/[A-Za-z]/', $password) || !preg_match('/\d/', $password)) {
         $message[] = 'パスワードは6文字以上の英数字を含む必要があります！';
      } elseif ($password != $confirm_password) {
         $message[] = 'パスワードが一致しません！';
      } elseif ($image_size > 2000000) {
         $message[] = '画像のサイズが大きすぎます！';
      } else {
         $hashed_password = password_hash($password, PASSWORD_DEFAULT);
         $insert = mysqli_query($conn, "INSERT INTO `user_form` (name, surname, email, password, gender, image) VALUES ('$name', '$surname', '$email', '$hashed_password', '$gender', '$unique_image_name')") or die('query failed');

         if ($insert) {
            move_uploaded_file($image_tmp_name, $image_folder);
            $message[] = '登録が成功しました！';
            header('location: login.php');
            exit();
         } else {
            $message[] = '登録に失敗しました！';
         }
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
   <title>登録</title>

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
            <h3>登録</h3>
            <?php
            if (isset($message)) {
               foreach ($message as $msg) {
                  echo '<div class="message">' . $msg . '</div>';
               }
            }
            ?>
            <!-- New container for the preview image -->
            <div id="preview-container"></div>
            <input type="text" name="name" placeholder="名前を入力" class="box" required value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
            <input type="text" name="surname" placeholder="名字を入力" class="box" required value="<?php echo isset($_POST['surname']) ? htmlspecialchars($_POST['surname']) : ''; ?>">
            <select name="gender" class="box" required>
               <option value="" disabled <?php echo !isset($_POST['gender']) ? 'selected' : ''; ?>>性別を選択してください</option>
               <option value="male" <?php echo (isset($_POST['gender']) && $_POST['gender'] === 'male') ? 'selected' : ''; ?>>男性</option>
               <option value="female" <?php echo (isset($_POST['gender']) && $_POST['gender'] === 'female') ? 'selected' : ''; ?>>女性</option>
            </select>
            <input type="email" name="email" placeholder="メールを入力" class="box" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            <input type="password" name="password" placeholder="パスワードを入力" class="box" required>
            <input type="password" name="cpassword" placeholder="パスワードを確認" class="box" required>
            <div class="file-upload">
               <input type="file" id="upload" name="image" class="box" accept="image/jpg, image/jpeg, image/png">
               <label for="upload">ファイルを選択</label>
            </div>
            <input type="submit" name="submit" value="登録" class="btn">
            <p>既にアカウントをお持ちですか？<a href="login.php">こちらからログイン</a>してください。</p>
         </form>

      </div>
   </div>
   <script src="preview.js"></script>
</body>

</html>