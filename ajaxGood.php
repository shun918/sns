<?php
//共通変数・関数ファイルを読込み
require('function.php');

require_once("db.php");
$dbh = db_connect();

session_start(); // セッション開始

//goodテーブル作成
$sql = "CREATE TABLE IF NOT EXISTS good( -- goodのテーブルがないとき作成
post_id VARCHAR(32) NOT NULL,
user_id VARCHAR(32) NOT NULL,
name VARCHAR(256) NOT NULL,
created_date datetime
)ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;";
$stmt = $dbh->query($sql);

// postがある場合
if(isset($_POST['postId']) && isset($_POST['name'])){
    $p_id = $_POST['postId'];
    $p_name = $_POST['name'];

    try{
        // goodテーブルから投稿IDとユーザーIDが一致したレコードを取得するSQL文
        $stmt = $dbh->prepare('SELECT * FROM good WHERE post_id = :p_id AND user_id = :u_id AND name = :p_name');
        $stmt->bindValue(':p_id', $p_id);
        $stmt->bindValue(':u_id', $_SESSION['username']);
        $stmt->bindValue(':p_name', $p_name);
        $stmt->execute();
        $resultCount = $stmt->rowCount();
        // レコードが1件でもある場合
        if(!empty($resultCount)){
            // レコードを削除する
            $stmt = $dbh->prepare('DELETE FROM good WHERE post_id = :p_id AND user_id = :u_id AND name = :p_name');
            $stmt->bindValue(':p_id', $p_id);
            $stmt->bindValue(':u_id', $_SESSION['username']);
            $stmt->bindValue(':p_name', $p_name);
            $stmt->execute();
            
            echo count(getGood($p_id, $p_name));
        }else{
            // レコードを挿入する
            $stmt = $dbh->prepare('INSERT INTO good (post_id, user_id, name, created_date) VALUES (:p_id, :u_id, :p_name, :created_date)');
            $stmt->bindValue(':p_id', $p_id, PDO::PARAM_STR);
            $stmt->bindValue(':u_id', $_SESSION['username'], PDO::PARAM_STR);
            $stmt->bindValue(':p_name', $p_name, PDO::PARAM_STR);
            $stmt->bindValue(':created_date', date('Y-m-d H:i:s'), PDO::PARAM_STR);
            $stmt->execute();

            echo count(getGood($p_id, $p_name));
        }
    }catch(Exception $e){
        error_log('エラー発生：'.$e->getMessage());
    }
}
?>