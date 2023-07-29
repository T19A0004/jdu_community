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

// Get the post ID from the query parameter
if (isset($_GET['post_id'])) {
    $post_id = $_GET['post_id'];
} else {
    // Redirect if post ID is not provided
    header('Location: forum.php');
    exit();
}

// Retrieve the post details from the database
$select_post_query = mysqli_query($conn, "SELECT topics.*, user_form.name FROM `topics` INNER JOIN `user_form` ON topics.user_id = user_form.id WHERE topics.id = '$post_id'") or die('query failed');
if (mysqli_num_rows($select_post_query) > 0) {
    $post = mysqli_fetch_assoc($select_post_query);
} else {
    // Redirect if post does not exist
    header('Location: forum.php');
    exit();
}

// Check if the user has permission to edit or delete the post
$canEdit = $user_role === 'admin' || $post['user_id'] == $user_id;

// Delete post
if (isset($_POST['delete_post'])) {
    $delete_query = "DELETE FROM `topics` WHERE id = '$post_id'";

    // Check user's role for deletion permissions
    if ($user_role == 'admin' || $post['user_id'] == $user_id) {
        $delete_result = mysqli_query($conn, $delete_query);

        if ($delete_result) {
            // Post deleted successfully
            header('Location: forum.php');
            exit();
        } else {
            // Failed to delete post
            echo "Failed to delete the post.";
        }
    } else {
        // User does not have permission to delete the post
        echo "You don't have permission to delete this post.";
    }
}

// Retrieve comments for the post
$select_comments_query = mysqli_query($conn, "SELECT comments.*, user_form.name FROM `comments` INNER JOIN `user_form` ON comments.user_id = user_form.id WHERE post_id = '$post_id' ORDER BY created_time DESC") or die('query failed');
$comments = array();
if (mysqli_num_rows($select_comments_query) > 0) {
    while ($comment = mysqli_fetch_assoc($select_comments_query)) {
        $comments[] = $comment;
    }
}

// Process the comment submission
if (isset($_POST['submit_comment'])) {
    $comment_text = $_POST['comment_text'];

    // Insert the new comment into the database
    $insert_comment_query = mysqli_query($conn, "INSERT INTO `comments` (post_id, user_id, comment_text, created_time) VALUES ('$post_id', '$user_id', '$comment_text', NOW())");

    if ($insert_comment_query) {
        // Comment added successfully
        // Refresh the page to see the new comment
        header('Location: ' . $_SERVER['PHP_SELF'] . '?post_id=' . $post_id);
        exit();
    } else {
        // Failed to add comment
        echo "Failed to add the comment.";
    }
}

// Process the comment edit submission
if (isset($_POST['edited_comment_text']) && isset($_POST['comment_id'])) {
    $edited_comment_text = $_POST['edited_comment_text'];
    $comment_id = $_POST['comment_id'];

    // Update the comment in the database
    $update_comment_query = mysqli_query($conn, "UPDATE `comments` SET comment_text = '$edited_comment_text' WHERE id = '$comment_id'");
    if ($update_comment_query) {
        // Comment updated successfully
        // Refresh the page to show the updated comment
        header('Location: ' . $_SERVER['PHP_SELF'] . '?post_id=' . $post_id);
        exit();
    } else {
        // Failed to update comment
        echo "Failed to update the comment: " . mysqli_error($conn); // Add this line for debugging
    }
}


// Process the comment deletion
if (isset($_POST['delete_comment'])) {
    $comment_id = $_POST['comment_id'];

    // Check user's role for deletion permissions
    $select_comment = mysqli_query($conn, "SELECT user_id FROM `comments` WHERE id = '$comment_id'") or die('query failed');
    if (mysqli_num_rows($select_comment) > 0) {
        $comment_data = mysqli_fetch_assoc($select_comment);
        $comment_user_id = $comment_data['user_id'];

        if ($user_role === 'admin' || $comment_user_id == $user_id) {
            // Delete related likes for the comment
            $delete_likes_query = mysqli_query($conn, "DELETE FROM `likes` WHERE comment_id = '$comment_id'");
            if (!$delete_likes_query) {
                // Failed to delete likes for the comment
                echo "Failed to delete related likes for the comment.";
            } else {
                // Delete the comment after deleting related likes
                $delete_comment_query = mysqli_query($conn, "DELETE FROM `comments` WHERE id = '$comment_id'");
                if ($delete_comment_query) {
                    // Comment deleted successfully
                    // Refresh the page to show the updated comments
                    header('Location: ' . $_SERVER['PHP_SELF'] . '?post_id=' . $post_id);
                    exit();
                } else {
                    // Failed to delete comment
                    echo "Failed to delete the comment.";
                }
            }
        } else {
            // User does not have permission to delete the comment
            echo "You don't have permission to delete this comment.";
        }
    } else {
        // Comment not found
        echo "Comment not found.";
    }
}

// Process the like submission or retraction
if (isset($_POST['like_comment']) && isset($_POST['comment_id'])) {
    $comment_id = $_POST['comment_id'];

    // Check if the user has already liked this comment
    $liked = false;
    if (isset($user_id)) {
        $check_like_query = mysqli_query($conn, "SELECT id FROM `likes` WHERE comment_id = '$comment_id' AND user_id = '$user_id'");
        if (mysqli_num_rows($check_like_query) > 0) {
            $liked = true;
        }
    }

    if ($liked) {
        // User has already liked the comment, so retract the like
        $delete_like_query = mysqli_query($conn, "DELETE FROM `likes` WHERE comment_id = '$comment_id' AND user_id = '$user_id'");

        if ($delete_like_query) {
            // Like retracted successfully
            // Refresh the page to update the like count
            header('Location: ' . $_SERVER['PHP_SELF'] . '?post_id=' . $post_id);
            exit();
        } else {
            // Failed to retract like
            echo "Failed to retract the like.";
        }
    } else {
        // User has not liked the comment, so add the like
        $insert_like_query = mysqli_query($conn, "INSERT INTO `likes` (comment_id, user_id) VALUES ('$comment_id', '$user_id')");

        if ($insert_like_query) {
            // Like added successfully
            // Refresh the page to update the like count
            header('Location: ' . $_SERVER['PHP_SELF'] . '?post_id=' . $post_id);
            exit();
        } else {
            // Failed to add like
            echo "Failed to add the like.";
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
    <title>ポスト</title>

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
                <div id="menu" class="menu">
                    <img src="menu.svg" alt="menu">
                </div>
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
            <h1>ポスト</h1>
        </div>
        <div class="main-post-title">
            <h1><?php echo $post['post_title']; ?></h1>
        </div>
        <div class="main-post">
            <div class="main-post-card">
                <div class="main-post-content">
                    <img class="main-post-img" src="<?php echo $post['post_image']; ?>" alt="Post Image">
                    <p><?php echo $post['main_text']; ?></p>
                </div>
                <div class="main-post-info">
                    <p>メインテーマ: <?php echo $post['main_theme']; ?></p>
                    <p>作者: <?php echo $post['name']; ?></p>
                    <p>投稿日時: <?php echo date('Y-m-d', strtotime($post['created_time'])); ?></p>
                    <?php if ($canEdit) : ?>
                        <div class="post-actions">
                            <form method="POST" onsubmit="return confirm('Are you sure you want to delete this post?');">
                                <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                                <button type="submit" name="delete_post" class="delete-btn">削除</button>
                            </form>
                            <a class="btn" href="editpost.php?post_id=<?php echo $post['id']; ?>">編集</a>
                        </div>
                    <?php endif; ?>
                </div>

            </div>
        </div>
        <div class="comments-section">
            <!-- Comment submission form -->
            <div class="comment-form">
                <h1>コメント</h1>
                <form method="POST" action="<?php echo $_SERVER['PHP_SELF'] . '?post_id=' . $post_id; ?>">
                    <textarea name="comment_text" rows="4" class="box" placeholder="コメントを入力..." required></textarea>
                    <button type="submit" name="submit_comment">コメントを投稿</button>
                </form>
            </div>
            <!-- Display existing comments -->
            <?php foreach ($comments as $comment) : ?>
                <div class="comment">
                    <?php
                    // Retrieve the avatar for the comment's author
                    $author_id = $comment['user_id'];
                    $select_author = mysqli_query($conn, "SELECT image FROM `user_form` WHERE id = '$author_id'") or die('query failed');
                    $author_fetch = mysqli_fetch_assoc($select_author);
                    $avatar_url = $author_fetch['image'] ? "uploaded_img/{$author_fetch['image']}" : "images/default-avatar.png";

                    // Add the link to the comment author's profile
                    $profile_url = "profile.php?user_id=" . $author_id;

                    // Get the number of likes for the comment
                    $comment_id = $comment['id'];
                    $likes_count_query = mysqli_query($conn, "SELECT COUNT(id) as count FROM `likes` WHERE comment_id = '$comment_id'");
                    $likes_count = 0;
                    if ($likes_count_result = mysqli_fetch_assoc($likes_count_query)) {
                        $likes_count = $likes_count_result['count'];
                    }

                    $comment_author_id = $comment['user_id'];
                    $is_comment_author = $comment_author_id === $user_id;

                    // Check if the user has already liked this comment
                    $liked = false;
                    if (isset($user_id)) {
                        $check_like_query = mysqli_query($conn, "SELECT id FROM `likes` WHERE comment_id = '$comment_id' AND user_id = '$user_id'");
                        if (mysqli_num_rows($check_like_query) > 0) {
                            $liked = true;
                        }
                    }
                    ?>

                    <div class="comment-header">
                        <div class="comment-author">
                            <!-- Wrap the avatar image with a hyperlink to the author's profile -->
                            <a href="<?php echo $profile_url; ?>">
                                <img id="avatar" src="<?php echo $avatar_url; ?>" alt="User Avatar">
                            </a>
                            <span class="comment-author-name"><?php echo $comment['name']; ?></span>
                        </div>
                        <span class="comment-time"><?php echo date('Y-m-d H:i:s', strtotime($comment['created_time'])); ?></span>
                    </div>
                    <div class="comment-content">
                        <p class="comment-text <?php echo $comment['user_id'] === $user_id ? 'editable' : ''; ?>"><?php echo $comment['comment_text']; ?></p>
                        <?php if ($comment['user_id'] === $user_id) : ?>
                            <!-- Show the edit form for the user's own comments -->
                            <form class="edit-comment-form" style="display: none;" action="<?php echo $_SERVER['PHP_SELF'] . '?post_id=' . $post_id; ?>" method="POST">
                                <textarea class="box" name="edited_comment_text" rows="4"><?php echo $comment['comment_text']; ?></textarea>
                                <input type="hidden" name="comment_id" value="<?php echo $comment['id']; ?>">
                                <button type="submit" class="save-btn">保存</button>
                                <button type="button" class="cancel-btn">キャンセル</button>
                            </form>
                        <?php endif; ?>
                        <?php if ($comment['user_id'] === $user_id) : ?>
                            <!-- Show edit form for the user's own comments -->
                            <div class="comment-edit-delete">
                                <button class="comment-edit-btn" data-comment-id="<?php echo $comment['id']; ?>"><i class="fa-solid fa-pen-to-square" style="color: #007BFF;"></i></button>
                                <form method="POST" action="<?php echo $_SERVER['PHP_SELF'] . '?post_id=' . $post_id; ?>" onsubmit="return confirm('本当にコメントを削除したいですか?');">
                                    <input type="hidden" name="comment_id" value="<?php echo $comment['id']; ?>">
                                    <button type="submit" name="delete_comment" class="delete-btn"><i class="fa-regular fa-trash-can" style="color: #ff0000;"></i></button>
                                </form>
                            </div>
                        <?php endif; ?>
                        <?php if (!$is_comment_author) : ?>
                            <!-- Display 'Like' button only if the logged-in user is not the author of the comment -->
                            <div class="comment-actions">
                                <form method="POST" class="like-form">
                                    <input type="hidden" name="comment_id" value="<?php echo $comment_id; ?>">
                                    <button type="submit" name="like_comment" class="like-btn <?php echo $liked ? 'liked' : ''; ?>">
                                        <?php echo $liked ? '💖' : '🤍'; ?>
                                    </button>
                                    <span class="likes-count"><?php echo $likes_count; ?></span>
                                </form>
                            </div>
                        <?php endif; ?>

                    </div>
                </div>
            <?php endforeach; ?>

        </div>
    </div>
    <script src="https://kit.fontawesome.com/9504827264.js" crossorigin="anonymous"></script>
    <script src="editcomment.js"></script>
    <script src="script.js"></script>
</body>

</html>