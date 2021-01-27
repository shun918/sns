<?php
session_start();//セッション開始

// 未ログイン状態ならトップへリダイレクト
if (!isset($_SESSION['username'])) {
  header('Location: ./homepage.php');
  exit;
}
?>

<!DOCTYPE html>
<html>
  <head>
    <title>会員一覧</title>
    <meta charset="utf-8">
  </head>
  <body>
    <h1>会員一覧（最新100件）</h1>

    <?php
      //データベース接続
      require_once("db.php");
      $dbh = db_connect();

      //100までのユーザー情報を降順に取り出す
      $stmt = $dbh->prepare('SELECT * FROM users ORDER BY id DESC LIMIT 100');
      $stmt->execute();

      $result = $stmt->fetchAll();
      $count = count($result);
      if ($count) {//countがある場合userlistのtable作成
        $html = '<table>';
        $html .= '<tr><th>ID</th><th>Username<th></tr>'; // 項目名
        for ($i=0; $i<$count; $i++) {//各行にユーザー情報を
          $html .= '<tr><td>'.$result[$i]['id'].'</td><td>'.$result[$i]['username'].'</td></tr>';
        }
        $html .= '</table>';
      }

      echo $html;//作成されたtableを表示
    ?>

    <p><a href="../service/homepage.php">トップに戻る</a></p>

  </body>
</html>
