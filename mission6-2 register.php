<!DOCTYPE html>
<html lang = "ja">
<head>
<meta charset="utf-8">
<title>ユーザー登録</title>
<style>
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
input /*入力フォームのレイアウト*/{
    border: none;
    border-bottom: 1px solid #d1d1d1;
    font-size: 1.2em;
    width: 100%;
    padding: 8px;
}
.btn /*ボタンのレイアウト*/{
    width: 100%;
    background-color: rgba(32, 152, 243, 0.9);
    border: none;
    color: #fff;
    padding: 15px;
    border-radius: 5px;
    box-shadow: 0 5px 2px rgba(0, 0, 0, .4);
    cursor: pointer;
}
.btn:hover /*カーソルをボタンに持って行ったとき少し色が変わる*/ {
    background-color: rgba(32, 152, 243, 1.0);
}
.btn:active /*ボタンを押した時に動く*/{
    position: relative;
    top: 5px;
    box-shadow: none;
}
.error {
    color: #d60e0e;
    font-size: 80%;
}
.required {
    margin-left: .3em;
    color: #f33;
    font-size: .9em;
    padding: 3px;
    background-color: #fee;
    font-weight: bold;
}
</style>
</head>
<body>
    <div class="content">

<?php
    // DB接続設定
    $dsn = 'データベース名';
    $user = 'ユーザー名';
    $password = 'パスワード';
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

    if( isset($_POST['register'])){
    if( !empty($_POST['username']) && !empty($_POST['pass']) && !empty($_POST['pass2'])){

        $username = $_POST['username'];
        $pass = $_POST['pass'];
        $pass2 = $_POST['pass2'];

        if($pass != $pass2){
            echo '<font class="error">パスワードが一致していません。</font><br>';
        }
        
        if($pass == $pass2){
            $sql = 'SELECT username FROM members WHERE username = ?';
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(1, $username, PDO::PARAM_STR);
            $stmt->execute();
            $rows = $stmt->fetchAll();
            if (count($rows) !== 0) {
                echo '<font class="error">同じユーザー名が既に登録されています。</font>';
                } else {
                // 新規ユーザー情報を登録する
                    
                //データベースに登録
                $register = $pdo->prepare("INSERT INTO members(username, pass) VALUES (:username, :pass)");
                $register->bindParam(":username", $username,PDO::PARAM_STR);
                $register->bindParam(":pass", $pass,PDO::PARAM_STR);
                $register->execute();
                echo '登録完了';
        }
    }
}
}
?>
        <form action="" method="POST">
            <h2>ユーザー登録</h2>
                <div class="control">
                ユーザー名<span class="required">必須</span>
                <input  type="text" name="username">
                <?php
                if( isset($_POST['register'])){
                    if(empty($_POST['username'])){
                    echo '<font class="error">名前を入力してください</font><br>';
                    }
                }?>
                </div>
                
                <div class="control">
                パスワード<span class="required">必須</span>
                <input type="text" name="pass">
                <?php
                if( isset($_POST['register'])){
                    if(empty($_POST['pass'])){
                    echo '<font class="error">パスワードを入力してください</font><br>';
                    }
                }?>
                </div>
                
                <div class="control">            
                パスワード（確認用）<span class="required">必須</span>
                <input type="text" name="pass2">             
                <?php
                if( isset($_POST['register'])){
                    if(empty($_POST['pass2'])){
                    echo '<font class="error">確認用パスワードを入力してください</font><br>';
                    }
                }?>
                </div>
            
                <button type="submit" name="register" class="btn">登録する</button>
        </form>

        <p><a href='mission6-2 login.php'>ログインページへ戻る</a></p>
        </div>
</body>
</html>