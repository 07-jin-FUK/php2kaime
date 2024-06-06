<?php
// 入力項目のチェック
include('functions.php');

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
// var_dump($_POST);
// exit();

$name = $_POST['name'];
$support = $_POST['support'];
$doko = $_POST['doko'];
$place = $_POST['place'];
$nojapan = $_POST['nojapan'];
$itu  = $_POST['itu'];
$id = $_POST['id'];

// $videoFileName = 'null';
// if (isset($_FILES['upfile']) && $_FILES['upfile']['error'] == 0) {
//     $uploadDir = 'uploads/';

//     // オリジナルのファイル名を取得し、安全なファイル名に変換
//     $originalFileName = basename($_FILES['upfile']['name']);
//     $safeFileName = preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $originalFileName);

//     // ユニークなファイル名を生成
//     $uniqueFileName = time() . '_' . $safeFileName;
//     $uploadFile = $uploadDir . $uniqueFileName;

//     // アップロード先ディレクトリが存在しない場合は作成
//     if (!is_dir($uploadDir)) {
//         mkdir($uploadDir, 0755, true);
//     }

//     // ファイルを移動
//     if (move_uploaded_file($_FILES['upfile']['tmp_name'], $uploadFile)) {
//         echo "ファイルは正しくアップロードされました。\n";
//         $videoFileName = $uniqueFileName;
//     } else {
//         echo "ファイルのアップロードに失敗しました。\n";
//     }
// } else {
//     echo "動画のアップロードにエラーがあります。エラーメッセージ: " . $_FILES['upfile']['error'] . "\n";
//     print_r($_FILES['upfile']);
// }

// DB接続
$pdo = connect_to_db();


// SQL実行
$sql = 'UPDATE todo_table SET name=:name, support=:support,doko=:doko,place=:place,nojapan=:nojapan,itu=:itu,media=:media, updated_at=now() WHERE id=:id';

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':name', $name, PDO::PARAM_STR);
$stmt->bindValue(':support', $support, PDO::PARAM_STR);
$stmt->bindValue(':doko', $doko, PDO::PARAM_STR);
$stmt->bindValue(':place', $place, PDO::PARAM_STR);
$stmt->bindValue(':nojapan', $nojapan, PDO::PARAM_STR);
$stmt->bindValue(':itu', $itu, PDO::PARAM_STR);
$stmt->bindValue(':id', $id, PDO::PARAM_STR);
$stmt->bindValue(':media', $videoFileName, PDO::PARAM_STR);

try {
    $status = $stmt->execute();
} catch (PDOException $e) {
    echo json_encode(["sql error" => "{$e->getMessage()}"]);
    exit();
}

header('Location:todo_read.php');
exit();
