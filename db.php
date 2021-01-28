<?php
//データベース情報
define('DSN', 'mysql:host=us-cdbr-east-03.cleardb.com;dbname=heroku_e212e29796a67ac;charset=utf8');
define('DB_USER', 'b5526f85564637');
define('DB_PASS', '71d84f06');

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