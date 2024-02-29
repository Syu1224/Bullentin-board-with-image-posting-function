<!DOCTYPE html>
<html lang ="ja">
<head>
<meta charset="utf-8" />
<title>画像投稿機能付き掲示板</title>
<style>
.post-image {
    width: 450px; /* 幅を300ピクセルに指定 */
    height: auto; /* 高さを自動調整 */
}
* {
    box-sizing: border-box;
    margin: 0px;
    padding: 0px;
}
input[type="text"]:focus {
  outline: none;
}
body {
    color: #2b546a;
    background-color: #fafafa;
    font-family:"Yu Gothic", "游ゴシック", YuGothic, "游ゴシック体";
}
h2 {
    font-size: 24px;
    margin: 0;
}
.content {
    position: relative;
    margin: 10px auto;
    background-color: #fff;
    border: 1px solid #d1d1d1;
    max-width: 600px;
    padding: 30px;
    border-radius: 5px;
}
.control {
    margin-bottom: 2em;
}
.input /*入力フォームのレイアウト*/{
    border: none;
    border-bottom: 1px solid #d1d1d1;
    font-size: 1.2em;
    width: 100%;
    padding: 8px;
}
.inputnum{
    width:18%;
}
.right{
    text-align: right;
}
.btn1 /*ボタンのレイアウト*/{
    width: 20%;
    background-color: rgba(32, 152, 243, 0.9);
    border: none;
    color: #fff;
    padding: 15px;
    border-radius: 5px;
    box-shadow: 0 5px 2px rgba(0, 0, 0, .4);
    cursor: pointer;
}
.btn1:hover /*カーソルをボタンに持って行ったとき少し色が変わる*/ {
    background-color: rgba(32, 152, 243, 1.0);
}
.btn1:active /*ボタンを押した時に動く*/{
    position: relative;
    top: 5px;
    box-shadow: none;
}
.btn2 /*ボタンのレイアウト*/{
    width: 20%;
    background-color: rgba(226, 4, 27, 0.9);
    border: none;
    color: #fff;
    padding: 15px;
    border-radius: 5px;
    box-shadow: 0 5px 2px rgba(0, 0, 0, .4);
    cursor: pointer;
}
.btn2:hover /*カーソルをボタンに持って行ったとき少し色が変わる*/ {
    background-color: rgba(226, 4, 27, 1.0);
}
.btn2:active /*ボタンを押した時に動く*/{
    position: relative;
    top: 5px;
    box-shadow: none;
}
.btn3 /*ボタンのレイアウト*/{
    width: 20%;
    background-color: rgba(248, 181, 0, 0.9);
    border: none;
    color: #fff;
    padding: 15px;
    border-radius: 5px;
    box-shadow: 0 5px 2px rgba(0, 0, 0, .4);
    cursor: pointer;
}
.btn3:hover /*カーソルをボタンに持って行ったとき少し色が変わる*/ {
    background-color: rgba(248, 181, 0, 1.0);
}
.btn3:active /*ボタンを押した時に動く*/{
    position: relative;
    top: 5px;
    box-shadow: none;
}
.error {
    color: #d60e0e;
    font-size: 80%;
}
</style>
</head>
<body>

    <div class="content">    

    <?php
    session_start();
    $session_name = $_SESSION["loginname"];

    //ログインしていない場合ログインページへ
    if(empty($_SESSION["loginname"])){
        header("location: mission6-2 login.php");
        exit();
    }
    
    //ログインユーザーの表示
    echo $session_name."でログイン中<br>";
    
    
    $directory = 'uploads';
    // ディレクトリが存在しない場合は作成する
    if (!is_dir($directory)) {
    mkdir($directory, 0777, true);
} 
    chmod("uploads", 0777);
    
    date_default_timezone_set('Asia/Tokyo');
    // DB接続設定
    $dsn = 'データベース';
    $user = 'ユーザー名';
    $password = 'パスワード';
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
    // DB内にテーブルを作成
    $sql = "CREATE TABLE IF NOT EXISTS mission6_2"
    ." ("
    . "id INT AUTO_INCREMENT PRIMARY KEY,"
    . "image_path varchar(50),"
    . "name CHAR(32),"
    . "comment TEXT,"
    . "date DATETIME,"
    . "pass varchar(50),"
    . "animals varchar(50)"
    .");";
    $stmt = $pdo->query($sql);
    
     if( !empty($_POST["name"]) && !empty($_POST["comment"]) && !empty($_POST["password"]) && !empty($_POST["animals"])){
        if(empty($_POST["editnumber"])){
            //通常の新規投稿
        
            // 画像の処理
            $image = $_FILES['image'];
            $image_name = $image['name'];
            $image_tmp_name = $image['tmp_name'];
            $image_size = $image['size'];
            $image_error = $image['error'];
            $err_msgs = array();
            

            // 一時ファイルから画像を保存するディレクトリへ移動
            $target_dir = "uploads/";
            $target_file = $target_dir . date('YmdHis') . basename($image_name);
            // パスをDBに保存する
            $image_path = $target_file;

            // ファイルサイズが1MB未満か
            if ($image_size > 1048576 || $image_error == 2) {
                array_push($err_msgs, 'ファイルサイズは1MB未満にしてください。');
            }

            // 拡張子は画像もしくは動画形式か
            $allow_ext = array('jpg', 'jpeg', 'png', 'mp4', 'avi', 'mov');
            $file_ext = pathinfo(basename($image_name), PATHINFO_EXTENSION);

            if (!in_array(strtolower($file_ext), $allow_ext)) {
                array_push($err_msgs, '画像もしくは動画ファイルを添付してください。');
            }

            // エラーがなければアップロード
            if (count($err_msgs) === 0) {
                if (is_uploaded_file($image_tmp_name)) {
                    if (move_uploaded_file($image_tmp_name, $target_file)) {
                        // 入力された名前の取得
                        $name = $_POST["name"];
                        // 入力されたコメントの取得    
                        $comment = $_POST["comment"];
                        // 投稿時間に関する設定
                        date_default_timezone_set('Asia/Tokyo');
                        $date = date("Y-m-d H:i:s");
                        // パスワードの取得
                        $pass = $_POST["password"];
                        
                        $animals = $_POST["animals"];
                        
                        // 入力完了の表示    
                        echo "投稿を受け付けました。<br>";

                        $newcomment = $pdo->prepare("INSERT INTO mission6_2(image_path, name, comment, date, pass, animals) VALUES (:image_path,:name,:comment,:date,:pass,:animals)");
                        $newcomment->bindParam(":image_path", $image_path, PDO::PARAM_STR);
                        $newcomment->bindParam(":name", $name, PDO::PARAM_STR);
                        $newcomment->bindParam(":comment", $comment, PDO::PARAM_STR);
                        $newcomment->bindParam(":date", $date, PDO::PARAM_STR);
                        $newcomment->bindParam(":pass", $pass, PDO::PARAM_STR);
                        $newcomment->bindParam(":animals", $animals, PDO::PARAM_STR);
                        $newcomment->execute();
                    } else {
                        echo '<p class="error">ファイルのアップロードに失敗しました。</p>';
                    }
                }
            }else{
                // エラーメッセージがあれば表示
                foreach ($err_msgs as $err_msg) {
                    echo "<p class='error'>".$err_msg."</p>";
                }
                
            }
        }//編集送信
        else{
            $editnumber = $_POST["editnumber"];
            // 画像の処理
            $image = $_FILES['image'];
            $image_name = $image['name'];
            $image_tmp_name = $image['tmp_name'];
            $image_size = $image['size'];
            $image_error = $image['error'];
            $err_msgs = array();

            // 一時ファイルから画像を保存するディレクトリへ移動
            $target_dir = "uploads/";
            $target_file = $target_dir . date('YmdHis') . basename($image_name);
            // パスをDBに保存する
            $image_path = $target_file;

            // ファイルサイズが1MB未満か
            if ($image_size > 1048576 || $image_error == 2) {
                array_push($err_msgs, 'ファイルサイズは1MB未満にしてください。');
            }

            // 拡張は画像形式か
            $allow_ext = array('jpg', 'jpeg', 'png', 'mp4', 'avi', 'mov');
            $file_ext = pathinfo(basename($image_name), PATHINFO_EXTENSION);

            if (!in_array(strtolower($file_ext), $allow_ext)) {
            array_push($err_msgs, '画像もしくは動画ファイルを添付してください。');
            }

            // エラーがなければアップロード
            if (count($err_msgs) === 0) {
                if (is_uploaded_file($image_tmp_name)) {
                    if (move_uploaded_file($image_tmp_name, $target_file)) {
                        // 入力された名前の取得
                        $name = $_POST["name"];
                        // 入力されたコメントの取得    
                        $comment = $_POST["comment"];
                        // 投稿時間に関する設定
                        date_default_timezone_set('Asia/Tokyo');
                        $date = date("Y-m-d H:i:s");
                        // パスワードの取得
                        $pass = $_POST["password"];
                        //入力完了の表示    
                        echo "編集を受け付けました。"."<br>";
                        $sql = 'UPDATE mission6_2 SET image_path=:image_path,name=:name,comment=:comment,pass=:pass,animals=:animals WHERE id=:id';
			            $edit = $pdo->prepare($sql);
                        $edit->bindParam(':id', $editnumber, PDO::PARAM_INT);
                        $edit->bindParam(':image_path', $image_path, PDO::PARAM_STR);
			            $edit->bindParam(':name', $name, PDO::PARAM_STR);
			            $edit->bindParam(':comment', $comment, PDO::PARAM_STR);
                        $edit->bindParam(':pass', $pass, PDO::PARAM_STR);
                        $edit->bindParam(":animals", $animals, PDO::PARAM_STR);
			            $edit->execute();
                    }
                }     
            }else{
                // エラーメッセージがあれば表示
                foreach ($err_msgs as $err_msg) {
                    echo "<p class='error'>".$err_msg."</p><br>";
                }
                
            }
        }
    }   //削除用分岐
    else if( !empty($_POST["deletenumber"]) && !empty($_POST["password"])){
        $deletenumber = $_POST["deletenumber"];
        $pass = $_POST["password"];
        //DB内で選択
        $sql = 'SELECT * FROM mission6_2 WHERE id =:id ';
   	    $select = $pdo->prepare($sql);
        $select->bindParam(':id', $deletenumber, PDO::PARAM_INT);
   	    $select ->execute();
        //passと一致したら削除
        $lines = $select->fetchAll(); 
        foreach ($lines as $line){
            if($line['pass']==$pass){
                $sql = 'delete from mission6_2 where id=:id';
                $delete = $pdo->prepare($sql);
                $delete->bindParam(':id', $deletenumber, PDO::PARAM_INT);
                $delete->execute();
            }
        }
    }//編集用分岐
    else if( !empty($_POST["edit_searchnumber"]) && !empty($_POST["password"]) ){
        $editnumber = $_POST["edit_searchnumber"];
        $pass = $_POST["password"];
        //DB内で選択
        $sql = 'SELECT * FROM mission6_2 WHERE id=:id ';
        $edit = $pdo->prepare($sql);       
        $edit->bindParam(':id', $editnumber, PDO::PARAM_INT);
        $edit->execute();
        //passと一致したら
        $lines = $edit->fetchAll();
        foreach ($lines as $line){
            if($line['pass']==$pass){
                $editimage_path=$line['image_path'];
                $editname=$line['name'];
                $editcomment=$line['comment'];
                $editnumber=$line['id'];
            }
        }
    }
    ?>
    
    <!--新規・編集送信用フォーム-->
    <form action="mission6-2 board.php" method="post" enctype="multipart/form-data">
        <div class="control">
        <?php if (!empty($_POST["edit_searchnumber"]) && !empty($_POST["password"])){ echo '<img src="'. $editimage_path .'"alt="画像" class="post-image"><br><font color="red">投稿を編集する場合、新しくファイルを選択してください。</font>';} ?>
        <input type="hidden" name="MAX_FILE_SIZE" value="1048576">
        <br>
        </div>
        
        <div class="control">
        <input type="file" name="image" >
        <br>
        </div>
        
        <div class="control">
        <input type="text" name="name" placeholder="名前" class="input" value="<?php if(!empty($editname)){echo $editname;}else{echo $session_name;}?>">
        <br>
        </div>
        
        <div class="control">
        <textarea name="comment" placeholder="コメント" class="input" cols="40" rows="4"><?php if(!empty($editcomment)){echo $editcomment;}?></textarea>
        <br>
        </div>
        
        
        <div class="control">
        <input type="text" name="password" class="input" placeholder="パスワード">
        <br>
        </div>
        
        <div class="control">
        種類(複数の場合にはその他、もしくはどちらか一方を選択)<select name='animals'>
        <option value='dog'>犬</option>
        <option value='cat'>猫</option>
        <option value='hamster'>ハムスター</option>
        <option value='turtle'>亀</option>
        <option value='rabbit'>うさぎ</option>
        <option value='bird'>鳥</option>
        <option value='others'>その他</option>
        </select>
        <br>
        </div>
        
        <div class="control">
            <div class="right">
        <input type="submit" name="submit" class="btn1"><input type ="hidden" name="editnumber" placeholder ="編集番号" value="<?php if(!empty($editnumber)){echo $editnumber;} ?>">
            </div>
        </div>
    </form>
    
    <!--削除用フォーム-->
    <form action="mission6-2 board.php" method="post">
        <div class="control">
        <input type="number" name="deletenumber" class="inputnum" placeholder="削除番号">
        <br>
        </div>
        
        <div class="control">
        <input type="text" name="password" class="input" placeholder="パスワード">
        <br>
        </div>
        
        <div class="control">
            <div class="right">
        <input type="submit" name="submit" class="btn2" value="削除">
            </div>
        <br>
        </div>
    </form>
    
    <!--編集用フォーム-->
    <form action="mission6-2 board.php" method="post">
        <div class="control">
        <input type="number" name="edit_searchnumber" class="inputnum" placeholder="編集検索番号">
        <br>
        </div>
        
        <div class="control">
        <input type="text" name="password" class="input" placeholder="パスワード">
        <br>
        </div>
        
        <div class="control">
            <div class="right">
        <input type="submit" name="submit" class="btn3" value="編集">
            </div>
        <br>
        </div>
    </form>
    
    
  <h2>投稿一覧</h2>
  <form action="" method="post">
      表示したい種類を選択してください<br>
    <select name='display' id='display' onchange="viewChange();">
        <option value='---'>---</option>
        <option value='all'>全て</option>
        <option value='dog'>犬</option>
        <option value='cat'>猫</option>
        <option value='hamster'>ハムスター</option>
        <option value='turtle'>亀</option>
        <option value='rabbit'>うさぎ</option>
        <option value='bird'>鳥</option>
        </select>
    </form>
  <div id="all" style="display:none;">
    <?php
    $sql = 'SELECT * FROM mission6_2';
    $stmt = $pdo->query($sql);
    $result = $stmt->fetchAll();
    foreach($result as $line){
        echo $line['id'].'：';
        echo $line['name'].'<br>';
        echo "<img src='{$line['image_path']}' alt='' class='post-image'>".'<br>';
        echo $line['comment'].'<br>';
        echo $line['date'].'<br>';
        echo "<hr>";
    }
    ?>
  </div>
  
  <div id="dog" style="display:none;">
    <?php
    $animals = 'dog';
    $sql = 'SELECT * FROM mission6_2';
    $stmt = $pdo->query($sql);
    $result = $stmt->fetchAll();
    foreach($result as $line){
        if($animals == $line[6]){
        echo $line['id'].'：';
        echo $line['name'].'<br>';
        echo "<img src='{$line['image_path']}' alt='' class='post-image'>".'<br>';
        echo $line['comment'].'<br>';
        echo $line['date'].'<br>';
        echo "<hr>";
        }
    }
    ?> 
  </div>

  <div id="cat" style="display:none;">
    <?php
    $animals = 'cat';
    $sql = 'SELECT * FROM mission6_2';
    $stmt = $pdo->query($sql);
    $result = $stmt->fetchAll();
    foreach($result as $line){
        if($animals == $line[6]){
        echo $line['id'].'：';
        echo $line['name'].'<br>';
        echo "<img src='{$line['image_path']}' alt='' class='post-image'>".'<br>';
        echo $line['comment'].'<br>';
        echo $line['date'].'<br>';
        echo "<hr>";
        }
    }
    ?> 
  </div>
  
  <div id="hamster" style="display:none;">
    <?php
    $animals = 'hamster';
    $sql = 'SELECT * FROM mission6_2';
    $stmt = $pdo->query($sql);
    $result = $stmt->fetchAll();
    foreach($result as $line){
        if($animals == $line[6]){
        echo $line['id'].'：';
        echo $line['name'].'<br>';
        echo "<img src='{$line['image_path']}' alt='' class='post-image'>".'<br>';
        echo $line['comment'].'<br>';
        echo $line['date'].'<br>';
        echo "<hr>";
        }
    }
    ?> 
  </div>
  
  <div id="turtle" style="display:none;">
    <?php
    $animals = 'turtle';
    $sql = 'SELECT * FROM mission6_2';
    $stmt = $pdo->query($sql);
    $result = $stmt->fetchAll();
    foreach($result as $line){
        if($animals == $line[6]){
        echo $line['id'].'：';
        echo $line['name'].'<br>';
        echo "<img src='{$line['image_path']}' alt='' class='post-image'>".'<br>';
        echo $line['comment'].'<br>';
        echo $line['date'].'<br>';
        echo "<hr>";
        }
    }
    ?> 
  </div>
  
  <div id="rabbit" style="display:none;">
    <?php
    $animals = 'rabbit';
    $sql = 'SELECT * FROM mission6_2';
    $stmt = $pdo->query($sql);
    $result = $stmt->fetchAll();
    foreach($result as $line){
        if($animals == $line[6]){
        echo $line['id'].'：';
        echo $line['name'].'<br>';
        echo "<img src='{$line['image_path']}' alt='' class='post-image'>".'<br>';
        echo $line['comment'].'<br>';
        echo $line['date'].'<br>';
        echo "<hr>";
        }
    }
    ?> 
  </div>
  
  <div id="bird" style="display:none;">
    <?php
    $animals = 'bird';
    $sql = 'SELECT * FROM mission6_2';
    $stmt = $pdo->query($sql);
    $result = $stmt->fetchAll();
    foreach($result as $line){
        if($animals == $line[6]){
        echo $line['id'].'：';
        echo $line['name'].'<br>';
        echo "<img src='{$line['image_path']}' alt='' class='post-image'>".'<br>';
        echo $line['comment'].'<br>';
        echo $line['date'].'<br>';
        echo "<hr>";
        }
    }
    ?> 
  </div>
  
  <script>
  function viewChange(){
    if(document.getElementById('display')){
        id = document.getElementById('display').value;
        if(id == 'all'){
            document.getElementById('all').style.display = "";
            document.getElementById('dog').style.display = "none";
            document.getElementById('cat').style.display = "none";
            document.getElementById('hamster').style.display = "none";
            document.getElementById('turtle').style.display = "none";
            document.getElementById('rabbit').style.display = "none";
            document.getElementById('bird').style.display = "none";
        }else if(id == 'dog'){
            document.getElementById('all').style.display = "none";
            document.getElementById('dog').style.display = "";
            document.getElementById('cat').style.display = "none";
            document.getElementById('hamster').style.display = "none";
            document.getElementById('turtle').style.display = "none";
            document.getElementById('rabbit').style.display = "none";
            document.getElementById('bird').style.display = "none";
        }else if(id == 'cat'){
            document.getElementById('all').style.display = "none";
            document.getElementById('dog').style.display = "none";
            document.getElementById('cat').style.display = "";
            document.getElementById('hamster').style.display = "none";
            document.getElementById('turtle').style.display = "none";
            document.getElementById('rabbit').style.display = "none";
            document.getElementById('bird').style.display = "none";
        }else if(id == 'hamster'){
            document.getElementById('all').style.display = "none";
            document.getElementById('dog').style.display = "none";
            document.getElementById('cat').style.display = "none";
            document.getElementById('hamster').style.display = "";
            document.getElementById('turtle').style.display = "none";
            document.getElementById('rabbit').style.display = "none";
            document.getElementById('bird').style.display = "none";
        }else if(id == 'turtle'){
            document.getElementById('all').style.display = "none";
            document.getElementById('dog').style.display = "none";
            document.getElementById('cat').style.display = "none";
            document.getElementById('hamster').style.display = "none";
            document.getElementById('turtle').style.display = "";
            document.getElementById('rabbit').style.display = "none";
            document.getElementById('bird').style.display = "none";
        }else if(id == 'rabbit'){
            document.getElementById('all').style.display = "none";
            document.getElementById('dog').style.display = "none";
            document.getElementById('cat').style.display = "none";
            document.getElementById('hamster').style.display = "none";
            document.getElementById('turtle').style.display = "none";
            document.getElementById('rabbit').style.display = "";
            document.getElementById('bird').style.display = "none";
        }else if(id == 'bird'){
            document.getElementById('all').style.display = "none";
            document.getElementById('dog').style.display = "none";
            document.getElementById('cat').style.display = "none";
            document.getElementById('hamster').style.display = "none";
            document.getElementById('turtle').style.display = "none";
            document.getElementById('rabbit').style.display = "none";
            document.getElementById('bird').style.display = "";
        }
    }

 window.onload = viewChange;
 }
  </script>
  
  
  
    <!--クリックするとログアウトに遷移-->
    <br>
    <p><a href='mission6-2 logout.php'>ログアウト</a></p>
    
    </div>
</body>
</html>