<?php
session_start();//セッション開始

// 未ログイン状態ならトップへリダイレクト
if (!isset($_SESSION['username'])) {
  header('Location: ./homepage.php');
  exit;
}

// 退会処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {//POSTで送信されている
  if (isset($_SESSION['username']) && isset($_POST['is_delete']) && $_POST['is_delete'] === '1') {// ログイン状態で、かつ退会ボタンを押した
    require_once("db.php");//データベース接続
    $dbh = db_connect();
    
    $stmt = $dbh->prepare('DELETE FROM users WHERE username = ?');// usernameが一致するuser情報を削除することで退会とする
    $stmt->bindValue(1, $_SESSION['username']);
    $stmt->execute();
    
    session_destroy(); // セッションを破壊
    
    header('Location: ./homepage.php');//topに戻る
    exit;
  }
}
?>

<!DOCTYPE html>
<html>
  <head>
    <title>退会画面</title>
    <meta charset="utf-8">
  </head>
  <body>
    <h1>退会画面</h1>
    <p>退会しますか？</p>
    <form action="./delete.php" method="POST">
      <input type="hidden" name="is_delete" value="1">
      <input type="submit" value="退会する">
    </form>
    <p><a href="../service/homepage.php">トップに戻る</a></p>
  </body>
</html>
