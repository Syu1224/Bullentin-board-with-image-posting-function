<!DOCTYPE html>
<html lang ="ja">
<head>
<meta charset="utf-8" />
<title>mission5-1</title>
</head>
<body>

    
    <?php
    date_default_timezone_set('Asia/Tokyo');
    // DB接続設定
    $dsn = 'データベース名';
    $user = 'ユーザー名';
    $password = 'パスワード';
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
    // DB内にテーブルを作成
    $sql = "CREATE TABLE IF NOT EXISTS mission5_1"
    ." ("
    . "id INT AUTO_INCREMENT PRIMARY KEY,"
    . "name CHAR(32),"
    . "comment TEXT,"
    . "date DATETIME,"
    . "pass varchar(50)"
    .");";
    $stmt = $pdo->query($sql);
    
     if( !empty($_POST["name"]) && !empty($_POST["comment"]) && !empty($_POST["password"]) ){
        if(empty($_POST["editnumber"])){
        //通常の新規投稿 
        //入力された名前の取得
        $name = $_POST["name"];
        //入力されたコメントの取得    
        $comment = $_POST["comment"];
        //投稿時間に関する設定
        date_default_timezone_set('Asia/Tokyo');
        $date = date("Y-m-d H:i:s");
        //パスワードの取得
        $pass = $_POST["password"];
        //入力完了の表示    
        echo $comment."（送信内容）を受け付けました。"."<br>";
        
        $newcomment = $pdo->prepare("INSERT INTO mission5_1(id, name, comment, date, pass) VALUES (:id,:name,:comment,:date,:pass)");
		$newcomment->bindParam(":id", $id,PDO::PARAM_INT);
		$newcomment->bindParam(":name", $name,PDO::PARAM_STR);
		$newcomment->bindParam(":comment", $comment,PDO::PARAM_STR);
        $newcomment->bindParam(":date", $date,PDO::PARAM_STR);
        $newcomment->bindParam(":pass", $pass,PDO::PARAM_STR);
		$newcomment->execute();

        }//編集送信
        else{
            $editnumber = $_POST["editnumber"];
             //入力された名前の取得
            $name = $_POST["name"];
            //入力されたコメントの取得    
            $comment = $_POST["comment"];
            //パスワードの取得
            $pass = $_POST["password"];
            //入力完了の表示    
            echo $comment."（編集）を受け付けました。"."<br>";
            $sql = 'UPDATE mission5_1 SET name=:name,comment=:comment,pass=:pass WHERE id=:id';
			$edit = $pdo->prepare($sql);
            $edit->bindParam(':id', $editnumber, PDO::PARAM_INT);
			$edit->bindParam(':name', $name, PDO::PARAM_STR);
			$edit->bindParam(':comment', $comment, PDO::PARAM_STR);
            $edit->bindParam(':pass', $pass, PDO::PARAM_STR);
			$edit->execute();
        }
            
    }
    //削除用分岐
    else if( !empty($_POST["deletenumber"]) && !empty($_POST["password"])){
        $deletenumber = $_POST["deletenumber"];
        $pass = $_POST["password"];
        //DB内で選択
        $sql = 'SELECT * FROM mission5_1 WHERE id =:id ';
   	    $select = $pdo->prepare($sql);
        $select->bindParam(':id', $deletenumber, PDO::PARAM_INT);
   	    $select ->execute();
        //passと一致したら削除
        $lines = $select->fetchAll(); 
        foreach ($lines as $line){
         if($line['pass']==$pass){
          $sql = 'delete from mission5_1 where id=:id';
          $delete = $pdo->prepare($sql);
          $delete->bindParam(':id', $deletenumber, PDO::PARAM_INT);
          $delete->execute();
         }
    }
}//編集用分岐
    else if( !empty($_POST["editnumber"]) && !empty($_POST["password"]) ){
        $editnumber = $_POST["editnumber"];
        $pass = $_POST["password"];
        //DB内で選択
        $sql = 'SELECT * FROM mission5_1 WHERE id=:id ';
        $edit = $pdo->prepare($sql);       
        $edit->bindParam(':id', $editnumber, PDO::PARAM_INT);
        $edit->execute();
        //passと一致したら
        $lines = $edit->fetchAll();
        foreach ($lines as $line){
        if($line['pass']==$pass){
        $editname=$line['name'];
        $editcomment=$line['comment'];
        $editnumber=$line['id'];
        }
    }
}
    ?>
    
    <!--新規・編集送信用フォーム-->
    <form action="" method="post">
        <input type="text" name="name" placeholder="名前" value="<?php if(!empty($editname)){echo $editname;}?>">
        <br>
        <input type="text" name="comment" placeholder="コメント" value="<?php if(!empty($editcomment)){echo $editcomment;}?>">
        <br>
        <input type="text" name="password" placeholder="パスワード">
        <br>
        <input type="submit" name="submit"><input type ="hidden" name="editnumber" placeholder ="編集番号" value="<?php if(!empty($editnumber)){echo $editnumber;} ?>">
    </form>
    
    <!--削除用フォーム-->
    <form action="" method="post">
        <input type="number" name="deletenumber" placeholder="削除番号">
        <br>
        <input type="text" name="password" placeholder="パスワード">
        <br>
        <input type="submit" name="submit" value="削除">
        <br>
    </form>
    
    <!--編集用フォーム-->
    <form action="" method="post">
        <input type="number" name="editnumber" placeholder="編集番号">
        <br>
        <input type="text" name="password" placeholder="パスワード">
        <br>
        <input type="submit" name="submit" value="編集">
        <br>
    </form>
  <h3>投稿一覧</h3>
  <?php
  $sql = 'SELECT * FROM mission5_1';
  $stmt = $pdo->query($sql);
  $result = $stmt->fetchAll();
  foreach($result as $line){
     echo $line['id'].': ';
     echo $line['name'].' ';
     echo $line['comment'].' ';
     echo $line['date'].'<br>';
     echo "<hr>";
  }
    ?>
</body>
</html>