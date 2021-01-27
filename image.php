<?php
    session_start(); // セッション開始

    //データベース接続
    require_once("db.php");
    $dbh = db_connect();

    $filename = $_SESSION['name'];
?>

<h1>画像表示</h1>
<img src="images/<?php echo $filename; ?>" width="300" height="300">
<a href="upload.php">画像アップロード</a>