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
                <li><a href="./index.php" >all</a></li>
                <li><a href="">picture</a></li>
                <li><a href="">movie</a></li>
              </ul>
            </nav>

            <main>
              <div id="res">
                <?php if (isset($_SESSION['username'])): ?>
                  <?php $Username = $_SESSION['username']; ?>
                  <div class="upload-box">
                    <p>作品を投稿しよう！</p>
                    <button id="modal-open"><input type="submit" name="share" value="share"></button>
                    <div id="modal-share">
                      <form method="post" enctype="multipart/form-data">
                        <input type="file" name="file"><br>
                        <input type="text" name="title" placeholder="title"><br>
                        <textarea rows="10" cols="60" name="comment" placeholder="comment"></textarea><br>
                        <button><input type="submit" name="upload" value="upload"></button>
                      </form>
                      <?php require_once("upload.php"); ?>
                      <?php if (count($errors) > 0): ?> 
                        <p><?php
                          foreach($errors as $value){
                            echo "<p>".$value."</p>";
                          }
                          ?></p>
                        <?php endif;?>
                        <a class="modal-close">×</a>
                    </div>
                  </div>
                  <div class="display-box">
                    <?php
                      // 画像を取得
                      $sql = 'SELECT * FROM Files WHERE username=? ORDER BY created_at DESC';//Files テーブルから作成日時（created_at）の新しい順でデータを取得
                      $stmt = $dbh->prepare($sql);
                      $stmt->bindValue(1, $Username);
                      $stmt->execute();
                      $files = $stmt->fetchAll();
                    ?>
                    <ul class="list-unstyled">
                      <?php foreach ($files as $row): ?>
                        <li class="list-array">
                            <?php require_once("check_ext.php"); ?>
                            <div class="box">
                                <a data-target="modal<?php echo $row['id'];?>" class="modal-syncer">
                                <?php if (checkExt_img($row['name'])): ?>
                                    <img src="files/<?php echo $row['created_at'].$Username.$row['name']; ?>" width="200" height="200">
                                <?php elseif (checkExt_mv($row['name'])): ?>
                                    <video src="files/<?php echo $row['created_at'].$Username.$row['name']; ?>" width="200" height="200" controls></video>
                                <?php endif; ?>
                                </a>
                            </div>
                        
                            <!--Modal contents-->
                            <div id="modal<?php echo $row['id'];?>" class="modal-content">
                                <h1><?php echo $row['title'] ?></h1>
                                <?php if (checkExt_img($row['name'])): ?>
                                <img src="files/<?php echo $row['created_at'].$Username.$row['name']; ?>">
                                <?php elseif (checkExt_mv($row['name'])): ?>
                                <video src="files/<?php echo $row['created_at'].$Username.$row['name']; ?>" controls></video>
                                <?php endif; ?>
                                <p><?php echo $row['comment'] ?></p>
                                <a href="javascript:void(0);" onclick="var ok = confirm('削除しますか？');
                                  if (ok) location.href='deldata.php?created_at=<?= $row['created_at']?>&Username=<?= $Username?>&name=<?= $row['name']?>'">
                                  <i class="far fa-trash-alt"></i> delete</a>
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