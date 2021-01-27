<?php
//データベース情報
define('DSN', 'mysql:host=*****;dbname=*****;charset=*****');
define('DB_USER', '******');
define('DB_PASS', '******');

function db_connect(){
	try {//データベース接続
		$dbh = new PDO(DSN, DB_USER, DB_PASS, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
		return $dbh;
	} catch (PDOException $e) {//データベース接続失敗時
		//echo $e->getMessage();
		$_SESSION['register_message'] = 'データベース接続に失敗しました';
		header('Location:'.$_SERVER['PHP_SELF']);
		exit;
	}
}
?>