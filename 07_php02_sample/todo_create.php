<?php
// POSTデータ確認
// var_dump($_POST);
// exit();
if (
    !isset($_POST['name']) || $_POST['name'] === '' ||
    !isset($_POST['support']) || $_POST['support'] === '' ||
    // !isset($_POST['doko']) || $_POST['doko'] === '' ||
    // !isset($_POST['place']) || $_POST['place'] === '' ||
    // !isset($_POST['nojapan']) || $_POST['nojapan'] === '' ||

    !isset($_POST['itu']) || $_POST['itu'] === ''


) {
    exit('お名前とどなたといつ行ったのかの入力は必須です（場所は任意)');
};

$name = $_POST['name'];
$support = $_POST['support'];
$where = $_POST['doko'];
$place = $_POST['place'];
$nojapan = $_POST['nojapan'];
$when = $_POST['itu'];

// DB接続
// 各種項目設定
$dbn = 'mysql:dbname=gs_l10_07;charset=utf8mb4;port=3306;host=localhost';
$user = 'root';
$pwd = '';

// DB接続
try {
    $pdo = new PDO($dbn, $user, $pwd);
} catch (PDOException $e) {
    echo json_encode(["db error" => "{$e->getMessage()}"]);
    exit();
}

// 「dbError:...」が表示されたらdb接続でエラーが発生していることがわかる．

// SQL作成&実行
$sql = 'INSERT INTO todo_table (id,name,support,doko,place,nojapan,itu, created_at, updated_at) VALUES (NULL, :name,:support,:doko,:place,:nojapan,:itu, now(), now())';

$stmt = $pdo->prepare($sql);

// バインド変数を設定
$stmt->bindValue(':name', $name, PDO::PARAM_STR);
$stmt->bindValue(':support', $support, PDO::PARAM_STR);
$stmt->bindValue(':doko', $where, PDO::PARAM_STR);
$stmt->bindValue(':place', $place, PDO::PARAM_STR);
$stmt->bindValue(':nojapan', $nojapan, PDO::PARAM_STR);
$stmt->bindValue(':itu', $when, PDO::PARAM_STR);

// SQL実行（実行に失敗すると `sql error ...` が出力される）
try {
    $status = $stmt->execute();
} catch (PDOException $e) {
    echo json_encode(["sql error" => "{$e->getMessage()}"]);
    exit();
};

// SQL実行の処理

header('Location:todo_input.php');
exit();
