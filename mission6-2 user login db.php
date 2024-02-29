<?php
    // DB接続設定
    $dsn = 'データベース';
    $user = 'ユーザー名';
    $password = 'パスワード';
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
    // DB内にテーブルを作成
    $sql = "CREATE TABLE IF NOT EXISTS members"
    ." ("
    . "username CHAR(32),"
    . "pass varchar(50)"
    .");";
    $stmt = $pdo->query($sql);
?>