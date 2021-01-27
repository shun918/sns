<?php
//データベース接続
require_once("db.php");
$dbh = db_connect();

require_once("check_ext.php");

//Filesテーブル作成
$sql = "CREATE TABLE IF NOT EXISTS Files( -- Filesのテーブルがないとき作成
id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
name VARCHAR(256) NOT NULL,
title char(32), -- カラム名:title 32文字以内
comment TEXT, -- カラム名:comment text型
username VARCHAR(32) NOT NULL, 
created_at datetime
)ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;";
$stmt = $dbh->query($sql);

//エラーメッセージの初期化
$errors = array();

// if (isset($_POST['upload'])) { //送信ボタンが押された場合
if ($_SERVER['REQUEST_METHOD'] == 'POST') { //POSTされた場合
    if(isset($_POST["title"])){//$POST[]に値が入ってる時のみ（以下同じ）値が入ってない時のエラーメッセージ対策
        $title = $_POST["title"];//タイトル
    }
    if(mb_strlen($title) > 33){
        $errors['title'] = 'タイトルは32文字以内で入力してください';
    }
    if(isset($_POST["comment"])){
        $comment = $_POST["comment"];//コメント
    }
    if (!empty($_FILES['file']['name']) && !empty($title) && !empty($comment) ) { //POSTされるものがあるとき
        $filename = $_FILES['file']['name'];
        $now = date("Y-m-d H:i:s");
        $user = $_SESSION['username'];
        if (checkExt($filename)) {//ファイルの拡張子をチェック
            move_uploaded_file($_FILES['file']['tmp_name'], 'files/'.$now.$user.$filename);//ファイルをテンポラリから保存場所へ移動
            $stmt = $dbh->prepare("INSERT INTO Files(name, username, created_at, title, comment) VALUES (:name, :username, :created_at, :title, :comment)");
            $stmt->bindValue(':name', $filename, PDO::PARAM_STR);
            $stmt->bindValue(':created_at', $now, PDO::PARAM_STR);
            $stmt->bindValue(':username', $user, PDO::PARAM_STR);
            $stmt -> bindParam(':title', $title, PDO::PARAM_STR);
            $stmt -> bindParam(':comment', $comment, PDO::PARAM_STR);
            $stmt->execute();
        } else {
            $errors['nofile'] = '許可された拡張子ファイルではありません';
        }
    }else{
        $errors['comment'] = "titleまたはcommentを入力してください";
    }
}
?>