<?php
session_start();

header("Content-type: text/html; charset=utf-8");

//クロスサイトリクエストフォージェリ（CSRF）対策のトークン判定
if ($_POST['token'] != $_SESSION['token']){
	echo "不正アクセスの可能性あり";
	exit();
}

//クリックジャッキング対策
header('X-FRAME-OPTIONS: SAMEORIGIN');

//データベース接続
require_once("db.php");
$dbh = db_connect();

//エラーメッセージの初期化
$errors = array();

if(empty($_POST)) {
	header("Location: registration_mail_form.php");
	exit();
}

$mailTo = $_SESSION['mail'];
$username = $_SESSION['username'];

//パスワードのハッシュ化
$password_hash =  password_hash($_SESSION['password'], PASSWORD_DEFAULT);

//ここでデータベースに登録する
try{
	//例外処理を投げる（スロー）ようにする
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
	//トランザクション開始
	$dbh->beginTransaction();
	
	//usersテーブルに本登録する
	$statement = $dbh->prepare("INSERT INTO users (username,mail,password) VALUES (:username,:mail,:password_hash)");
	//プレースホルダへ実際の値を設定する
	$statement->bindValue(':username', $username, PDO::PARAM_STR);
	$statement->bindValue(':mail', $mailTo, PDO::PARAM_STR);
	$statement->bindValue(':password_hash', $password_hash, PDO::PARAM_STR);
	$statement->execute();
		
	//pre_memberのflagを1にする
	$statement = $dbh->prepare("UPDATE pre_member SET flag=1 WHERE mail=(:mail)");
	//プレースホルダへ実際の値を設定する
	$statement->bindValue(':mail', $mailTo, PDO::PARAM_STR);
	$statement->execute();
	
	// トランザクション完了（コミット）
	$dbh->commit();
		
	//データベース接続切断
	$dbh = null;
	
	//セッション変数を全て解除
	$_SESSION = array();
	
	//セッションクッキーの削除・sessionidとの関係を探れ。つまりはじめのsesssionidを名前でやる
	if (isset($_COOKIE["PHPSESSID"])) {
    		setcookie("PHPSESSID", '', time() - 1800, '/');
	}
	
 	//セッションを破棄する
 	session_destroy();
	 
	/*
	* メール送信処理
	* 登録されたメールアドレスへメールをお送りする。
	* 今回はメール送信はしないためコメント
	*/

	// require 'src/Exception.php';
	// require 'src/PHPMailer.php';
	// require 'src/SMTP.php';
	// require 'setting.php';
	
	// // PHPMailerのインスタンス生成
	// $mail = new PHPMailer\PHPMailer\PHPMailer();

	// $mail->isSMTP(); // SMTPを使うようにメーラーを設定する
	// $mail->SMTPAuth = true;
	// $mail->Host = MAIL_HOST; // メインのSMTPサーバー（メールホスト名）を指定
	// $mail->Username = MAIL_USERNAME; // SMTPユーザー名（メールユーザー名）
	// $mail->Password = MAIL_PASSWORD; // SMTPパスワード（メールパスワード）
	// $mail->SMTPSecure = MAIL_ENCRPT; // TLS暗号化を有効にし、「SSL」も受け入れます
	// $mail->Port = SMTP_PORT; // 接続するTCPポート

	// // メール内容設定
	// $mail->CharSet = "UTF-8";
	// $mail->Encoding = "base64";
	// $mail->setFrom(MAIL_FROM,MAIL_FROM_NAME);
	// $mail->addAddress($mailTo, '受信者さん'); //受信者（送信先）を追加する
	// $mail->addReplyTo('web@sample.com','返信先');
	// //    $mail->addCC('xxxxxxxxxx@xxxxxxxxxx'); // CCで追加
	// //    $mail->addBcc('xxxxxxxxxx@xxxxxxxxxx'); // BCCで追加
	// $mail->Subject = MAIL_SUBJECT2; // メールタイトル
	// $mail->isHTML(true);    // HTMLフォーマットの場合はコチラを設定します
	// $body = '登録が完了しました。';

	// $mail->Body  = $body; // メール本文
	// // メール送信の実行
	// if(!$mail->send()) {
	// 	$errors['mail_error'] = "メールの送信に失敗しました。";
	// } else {
	// 	$message = "登録が完了しました。";
	// }
	
}catch (PDOException $e){
	//トランザクション取り消し（ロールバック）
	$dbh->rollBack();
	$errors['error'] = "もう一度やりなおして下さい。";
	print('Error:'.$e->getMessage());
}

?>

<!DOCTYPE html>
<html>
<head>
<title>会員登録完了画面</title>
<meta charset="utf-8">
</head>
<body>
 
<!-- エラーがなければ -->
<?php if (count($errors) === 0): ?>
<h1>会員登録完了画面</h1>
<p>登録完了いたしました。ログイン画面からどうぞ。</p>
<p><a href="./login.php">ログイン画面</a></p>

<!-- エラーがあれば -->
<?php elseif(count($errors) > 0): ?>
<?php
foreach($errors as $value){
	echo "<p>".$value."</p>";
}
?>

<?php endif; ?>
 
</body>
</html>