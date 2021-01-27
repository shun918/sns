<?php
session_start();//セッション開始

header("Content-type: text/html; charset=utf-8");

//クリックジャッキング対策
header('X-FRAME-OPTIONS: SAMEORIGIN');

//データベース接続
require_once("db.php");
$dbh = db_connect();

//usersテーブル作成
$sql = "CREATE TABLE IF NOT EXISTS users( -- usersのテーブルがないとき作成
id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
username VARCHAR(32) NOT NULL, 
mail VARCHAR(50) NOT NULL,
password VARCHAR(255) NOT NULL,
flag TINYINT(1) NOT NULL DEFAULT 1
)ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;";
$stmt = $dbh->query($sql);

//pre_memberテーブル作成
$sql = "CREATE TABLE IF NOT EXISTS pre_member( -- pre_memberのテーブルがないとき作成
id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
urltoken VARCHAR(128) NOT NULL,
mail VARCHAR(50) NOT NULL,
date DATETIME NOT NULL,
flag TINYINT(1) NOT NULL DEFAULT 0
)ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;";
$stmt = $dbh->query($sql);

//エラーメッセージの初期化
$errors = array();

if(empty($_POST)) {//POSTが空ならregistration_mail_form.phpに戻る
	header("Location: registration_mail_form.php");
	exit();
}else{
	$mailTo = isset($_POST['mail']) ? $_POST['mail'] : NULL;//POSTされたデータを変数に入れる
	
	if ($mailTo == ''){//メールアドレスが空なら実行
		$errors['mail'] = "メールが入力されていません。";
	}else{//メールアドレスが空でないなら実行
		if(!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $mailTo)){//メールアドレスの形式が正しいか判断
			$errors['mail_check'] = "メールアドレスの形式が正しくありません。";
		}
		// 重複チェック
		$stmt = $dbh->prepare('SELECT * FROM users WHERE mail=?');
		// $stmt->bindValue(1, $_POST['mail']);
		$stmt->bindValue(1, $mailTo);
		$stmt->execute();
		$result = $stmt->fetch();
		if ($result > 0) {
		  $errors['member_check'] = "このメールアドレスはすでに利用されております。";
		}
	}
}

//クロスサイトリクエストフォージェリ（CSRF）対策のトークン判定
if ($_POST['token'] != $_SESSION['token']){
	echo "不正アクセスの可能性あり";
	exit();
}

if (count($errors) === 0){//上記まででerrorsが0なら実行
	$urltoken = hash('sha256',uniqid(rand(),1));//ランダムなトークンを準備
	$url = "http://tb-220224.tech-base.net/registration_form.php"."?urltoken=".$urltoken;
	
	//ここでデータベースに登録する
	try{
		//例外処理を投げる（スロー）ようにする
		$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
		$statement = $dbh->prepare("INSERT INTO pre_member (urltoken,mail,date) VALUES (:urltoken,:mail,now() )");
		
		//プレースホルダへ実際の値を設定する
		$statement->bindValue(':urltoken', $urltoken, PDO::PARAM_STR);
		$statement->bindValue(':mail', $mailTo, PDO::PARAM_STR);
		$statement->execute();
			
		//データベース接続切断
		$dbh = null;	
		
	}catch (PDOException $e){
		print('Error:'.$e->getMessage());
		die();
	}
	
	require 'src/Exception.php';
	require 'src/PHPMailer.php';
	require 'src/SMTP.php';
	require 'setting.php';
	
	// PHPMailerのインスタンス生成
	$mail = new PHPMailer\PHPMailer\PHPMailer();

	$mail->isSMTP(); // SMTPを使うようにメーラーを設定する
	$mail->SMTPAuth = true;
	$mail->Host = MAIL_HOST; // メインのSMTPサーバー（メールホスト名）を指定
	$mail->Username = MAIL_USERNAME; // SMTPユーザー名（メールユーザー名）
	$mail->Password = MAIL_PASSWORD; // SMTPパスワード（メールパスワード）
	$mail->SMTPSecure = MAIL_ENCRPT; // TLS暗号化を有効にし、「SSL」も受け入れます
	$mail->Port = SMTP_PORT; // 接続するTCPポート

	// メール内容設定
	$mail->CharSet = "UTF-8";
	$mail->Encoding = "base64";
	$mail->setFrom(MAIL_FROM,MAIL_FROM_NAME);
	$mail->addAddress($mailTo, '受信者さん'); //受信者（送信先）を追加する
	$mail->addReplyTo('web@sample.com','返信先');
	//    $mail->addCC('xxxxxxxxxx@xxxxxxxxxx'); // CCで追加
	//    $mail->addBcc('xxxxxxxxxx@xxxxxxxxxx'); // BCCで追加
	$mail->Subject = MAIL_SUBJECT; // メールタイトル
	$mail->isHTML(true);    // HTMLフォーマットの場合はコチラを設定します
//$bodyにEOM間を格納（メール内容）
$body = <<< EOM
24時間以内に下記のURLからご登録下さい。
{$url}
EOM;

	$mail->Body  = $body; // メール本文
	// メール送信の実行
	if(!$mail->send()) {
		$errors['mail_error'] = "メールの送信に失敗しました。";
	} else {
		$_SESSION = array();//セッション変数を全て解除
		if (isset($_COOKIE["PHPSESSID"])) { //クッキーの削除
			setcookie("PHPSESSID", '', time() - 1800, '/');
		}
		session_destroy();//セッションを破棄する
		$message = "メールをお送りしました。24時間以内にメールに記載されたURLからご登録下さい。";
	}
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>メール確認画面</title>
    <meta charset="utf-8">
</head>
<body>
    <h1>メール確認画面</h1>
	<!-- エラーがなければ -->
    <?php if (count($errors) === 0): ?>
    <p><?=$message?></p>
    <p>↓このURLが記載されたメールが届きます。</p>
    <a href="<?=$url?>"><?=$url?></a>
	<!-- エラーがあれば -->
    <?php elseif(count($errors) > 0): ?>
    <?php
    foreach($errors as $value){
        echo "<p>".$value."</p>";
    }
    ?>
	<!-- 一つ前のページの戻るボタン -->
    <input type="button" value="戻る" onClick="history.back()">
    <?php endif; ?>
</body>
</html>