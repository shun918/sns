<?php
session_start();//セッション開始
//データベース接続
require_once("db.php");
$dbh = db_connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {//POSTで送信されている
  if(isset($_POST['username']) && isset($_POST['password'])){// usernameとpasswordが定義されている
    if(!empty($_POST['username']) && !empty($_POST['password'])){// usernameとpasswordが空白でない
      $stmt = $dbh->prepare('SELECT * FROM users WHERE username=?');//usernameとpasswordが一致するユーザー情報がある時
      $stmt->bindValue(1, $_POST['username']);
      $stmt->execute();
      $result = $stmt->fetch(PDO::FETCH_ASSOC);
      if(password_verify($_POST['password'], $result['password'])){// 一致するユーザー情報があり、正しくログインできた時
        $_SESSION['username'] = $_POST['username'];
        header('Location:homepage.php');
        exit;
      }else{// 一致するユーザー情報がなく、正しくログインできなかった時
        $_SESSION['login_message'] = '送信データが正しくありません';
        header('Location:'.$_SERVER['PHP_SELF']);
        exit;
      }
    }
  }
}
?>

<!DOCTYPE html>
<html>
  <head>
    <title>ログイン</title>
    <meta charset="utf-8">
  </head>
  <body>
    <h1>ログイン</h1>
    <form action="" method="POST">
      <?php
        if (isset($_SESSION['login_message'])) {
          echo($_SESSION['login_message']);
        }
      ?>
      <p>
        ユーザーネーム: <input type="text" name="username">
      </p>

      <p>
        パスワード: <input type="password" name="password">
      </p>

      <input type="submit" name="submit" value="送信">

    </form>

    <p><a href="../service/homepage.php">トップに戻る</a></p>

  </body>
</html>

<?php
// セッションの初期化 
$_SESSION['login_message'] = '';
?>
