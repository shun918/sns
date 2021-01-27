<?php
//データベース接続
require_once("db.php");
$dbh = db_connect();

require_once("check_ext.php");

//共通変数・関数ファイルを読込み
require('function.php');
?>

<?php
session_start(); // セッション開始
?>
 
<!DOCTYPE html>
<html lang="ja">
<head>
  <title>トップ</title>
  <meta charset="utf-8">
  <link rel="stylesheet" href="styles.css">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
</head>
<body>
    <div class="wrapper">
        <header>
          <span class="logo-title">TITLE</span>
        </header>
        <div class="main-wrapper">
            <nav>
              <ul class="request">
                <li><a href="./main.php" >all</a></li>
                <li><a href="">picture</a></li>
                <li><a href="">movie</a></li>
              </ul>
            </nav>

            <main>
              <div id="res">
                <?php if (isset($_SESSION['username'])): ?>
                  <div class="display-box">
                    <?php
                    // 画像を取得
                    $sql = 'SELECT * FROM Files ORDER BY created_at DESC';//Files テーブルから作成日時（created_at）の新しい順でデータを取得
                    $stmt = $dbh->prepare($sql);
                    $stmt->execute();
                    $files = $stmt->fetchAll();
                    ?>
                    <ul class="list-unstyled">
                    <?php foreach ($files as $row): ?>
                        <li class="list-array">
                            <div class="box">
                                <a data-target="modal<?php echo $row['id'];?>" class="modal-syncer">
                                <?php if (checkExt_img($row['name'])): ?>
                                    <img src="files/<?php echo $row['created_at'].$row['username'].$row['name']; ?>" width="200" height="200">
                                <?php elseif (checkExt_mv($row['name'])): ?>
                                    <video src="files/<?php echo $row['created_at'].$row['username'].$row['name']; ?>" width="200" height="200" controls></video>
                                <?php endif; ?>
                                </a>
                            </div>
                        
                            <!--Modal contents-->
                            <div id="modal<?php echo $row['id'];?>" class="modal-content">
                                <h1><?php echo $row['title'] ?></h1>
                                <?php if (checkExt_img($row['name'])): ?>
                                <img src="files/<?php echo $row['created_at'].$row['username'].$row['name']; ?>">
                                <?php elseif (checkExt_mv($row['name'])): ?>
                                <video src="files/<?php echo $row['created_at'].$row['username'].$row['name']; ?>" controls></video>
                                <?php endif; ?>
                                <p><?php echo $row['comment'] ?></p>
                                <div class="post" data-postid="<?php echo $row['username'] ?>" data-name="<?php echo $row['name'] ?>">
                                  <div class="btn-good">
                                  	<!-- 自分がいいねした投稿にはハートのスタイルを常に保持する -->
                                    <i class="fa-thumbs-up fa-lg
                                      <?php
                                        if(isGood($_SESSION['username'],$row['username'],$row['name'])){
                                          echo ' active fas';
                                        }else{
                                          echo ' far';
                                      }; ?>">
                                    </i>
                                    <span><?php echo count(getGood($row['username'],$row['name'])); ?></span>
                                  </div>
                                </div>
                                <a class="modal-close">×</a>
                            </div>
                        </li>
                    <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>
              </div>
            </main>

            <aside>
              <?php if (!isset($_SESSION['username'])): ?>
                <p><a href="./registration_mail_form.php">新規会員登録</a></p>
                <p><a href="./login.php">ログイン</a></p>
              <?php endif; ?>
              <?php if (isset($_SESSION['username'])): ?>
                <?php echo('<p>ようこそ'.$_SESSION['username'].'さん</p>'); ?>
                <p><a href="./logout.php">ログアウト</a><p>
                <p><a href="./list.php">会員一覧</a></p>
                <p><a href="./delete.php">退会</a></p>
                <p><a href="./homepage.php">マイページ</a></p>
              <?php endif; ?>
            </aside>
        </div>
      </div>
    <footer>
      <div id="js-page-top" class="page-top">
        トップへもどる
      </div>
    </footer>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
  <script src="./main.js"></script>
  </body>
</html>