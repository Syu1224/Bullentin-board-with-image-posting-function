<!DOCTYPE html>
<html lang = "ja">
<head>
<meta charset = "UFT-8">
<title>ログイン</title>
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
</style>
</head>
<body>
    <div class="content">
    <?php
    // DB接続設定
    $dsn = 'データベース';
    $user = 'ユーザー名';
    $password = 'パスワード';
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
    ?>
    <?php
    //入力されたら
    if( !empty($_POST["loginname"]) && !empty($_POST["loginpass"])){
            //入力内容を変数に
            $loginname = $_POST["loginname"];
            $loginpass = $_POST["loginpass"];

            //ユーザー名検索
            $sql = 'SELECT * FROM members WHERE username=:username';
            $select = $pdo->prepare($sql);
            $select->bindParam(':username', $loginname, PDO::PARAM_STR);
            $select->execute();

            //パスワードの照合
            $lines = $select->fetchAll();
            foreach ($lines as $line){
            if($line['pass']==$loginpass){
                session_start();
                $_SESSION["loginname"] = $loginname;
                header("location: mission6-2 board.php");
                exit;
        }
        else{
            echo "パスワードが一致しませんでした";
        }
    }
}
?>
    <form action="mission6-2 login.php" method="post">
    <h2>ログイン</h2>
    
    <div class="control">
    ユーザー名<input type="text" name="loginname">
    <?php if( empty($_POST["loginname"]) && !empty($_POST["loginpass"])): ?>
    <p class="error">ユーザー名が入力されていません</p>
    <?php endif ?>
    </div>
    
    <div class="control">
    パスワード<input type="text" name="loginpass">
    <?php if( !empty($_POST["loginname"]) && empty($_POST["loginpass"])): ?>
    <p class="error">パスワードが入力されていません</p>
    <?php endif ?>
    </div>
    
    <input type="submit" name="login" value="ログイン" class="btn"><br>
    </form>
    <p><a href='mission6-2 register.php'>ユーザー登録されていない方はこちら</a></p>
    </form>
    </div>
</body>
</html>