<?php
require_once("db.php");

function getGood($p_id, $p_name){
	try {
		$dbh = db_connect();
		$stmt = $dbh->prepare('SELECT * FROM good WHERE post_id = :p_id AND name = :p_name');
		$stmt->bindValue(':p_id', $p_id);
		$stmt->bindValue(':p_name', $p_name);
		$stmt->execute();
		
		if($stmt){
			return $stmt->fetchAll();
		}else{
			return false;
		}
	} catch (Exception $e) {
		error_log('エラー発生：'.$e->getMessage());
	}
}
function isGood($u_id, $p_id, $p_name){
	try {
		$dbh = db_connect();
		$stmt = $dbh->prepare('SELECT * FROM good WHERE post_id = :p_id AND user_id = :u_id AND name = :p_name');
		$stmt->bindValue(':p_id', $p_id);
		$stmt->bindValue(':u_id', $_SESSION['username']);
		$stmt->bindValue(':p_name', $p_name);
		$stmt->execute();

		if($stmt->rowCount()){
			return true;
		}else{
			return false;
		}

	} catch (Exception $e) {
		error_log('エラー発生:' . $e->getMessage());
	}
}
?>