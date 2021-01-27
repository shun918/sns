<?php 
//データベース接続
require_once("db.php");
$dbh = db_connect();

$date=$_GET['created_at'];
$Username=$_GET['Username'];
$Name=$_GET['name'];
unlink('files/'.$date.$Username.$Name);
$sql = 'DELETE FROM Files where created_at = :created_at';
$stmt = $dbh->prepare($sql);
$stmt->bindValue(':created_at', $date, PDO::PARAM_STR);
$stmt->execute();

unset($dbh);
header('Location:homepage.php');
exit();
?>