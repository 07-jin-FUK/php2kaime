<?php
include('functions.php');
// POSTデータ確認
// var_dump($_FILES);
// exit();
if (
    !isset($_POST['name']) || $_POST['name'] === '' ||
    !isset($_POST['support']) || $_POST['support'] === '' ||
    !isset($_POST['itu']) || $_POST['itu'] === ''
    // 入力しなくていい画面もあるので入力確認は名前と時間のみ

) {
    exit('お名前とどなたといつ行ったのかの入力は必須です（場所は任意)');
};


$name = $_POST['name'];
$support = $_POST['support'];
$where = $_POST['doko'];
$place = $_POST['place'];
$nojapan = $_POST['nojapan'];
$when = $_POST['itu'];

// 動画ファイルをアップロード
$videoFileName = 'null';
if (isset($_FILES['upfile']) && $_FILES['upfile']['error'] == 0) {
    $uploadDir = 'uploads/';

    // オリジナルのファイル名を取得し、安全なファイル名に変換
    $originalFileName = basename($_FILES['upfile']['name']);
    $safeFileName = preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $originalFileName);

    // ユニークなファイル名を生成
    $uniqueFileName = time() . '_' . $safeFileName;
    $uploadFile = $uploadDir . $uniqueFileName;

    // アップロード先ディレクトリが存在しない場合は作成
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // ファイルを移動
    if (move_uploaded_file($_FILES['upfile']['tmp_name'], $uploadFile)) {
        echo "ファイルは正しくアップロードされました。\n";
        $videoFileName = $uniqueFileName;
    } else {
        echo "ファイルのアップロードに失敗しました。\n";
    }
} else {
    echo "動画のアップロードにエラーがあります。エラーメッセージ: " . $_FILES['upfile']['error'] . "\n";
    print_r($_FILES['upfile']);
}




// // 動画ファイルをアップロード
// $videoFileName = 'upload';
// if (isset($_FILES['video']) && $_FILES['video']['error'] == 0) {
//     $uploadDir = 'uploads/';

//     // オリジナルのファイル名を取得し、安全なファイル名に変換
//     $originalFileName = basename($_FILES['video']['name']);
//     $safeFileName = preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $originalFileName);

//     // ユニークなファイル名を生成
//     $uniqueFileName = time() . '_' . $safeFileName;
//     $uploadFile = $uploadDir . $uniqueFileName;

//     // アップロード先ディレクトリが存在しない場合は作成
//     if (!is_dir($uploadDir)) {
//         mkdir($uploadDir, 0755, true);
//     }

//     // ファイルを移動
//     if (move_uploaded_file($_FILES['video']['tmp_name'], $uploadFile)) {
//         echo "ファイルは正しくアップロードされました。\n";
//         $videoFileName = $uniqueFileName;
//     } else {
//         echo "ファイルのアップロードに失敗しました。\n";
//     }
// } else {
//     echo "動画のアップロードにエラーがあります。エラーメッセージ: " . $_FILES['video']['error'] . "\n";
//     print_r($_FILES['video']);
// }


// DB接続
$pdo = connect_to_db();
// $dbn = 'mysql:dbname=gs_l10_07;charset=utf8mb4;port=3306;host=localhost';
// $user = 'root';
// $pwd = '';
// try {

//     //ファイルアップロードがあったとき
//     if (isset($_FILES['upfile']['error']) && is_int($_FILES['upfile']['error']) && $_FILES["upfile"]["name"] !== "") {
//         //エラーチェック
//         switch ($_FILES['upfile']['error']) {
//             case UPLOAD_ERR_OK: // OK
//                 break;
//             case UPLOAD_ERR_NO_FILE:   // 未選択
//                 throw new RuntimeException('ファイルが選択されていません', 400);
//             case UPLOAD_ERR_INI_SIZE:  // php.ini定義の最大サイズ超過
//                 throw new RuntimeException('ファイルサイズが大きすぎます', 400);
//             default:
//                 throw new RuntimeException('その他のエラーが発生しました', 500);
//         }

//         //画像・動画をバイナリデータにする．
//         $raw_data = file_get_contents($_FILES['upfile']['tmp_name']);

//         //拡張子を見る
//         $tmp = pathinfo($_FILES["upfile"]["name"]);
//         $extension = strtolower($tmp["extension"]);

//         if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'mp4'])) {
//             if (
//                 $extension === "jpg" || $extension === "jpeg"
//             ) {
//                 $extension = "jpeg";
//             }
//         } else {
//             echo "非対応ファイルです．<br/>";
//             echo ("<a href=\"index.php\">戻る</a><br/>");
//             exit(1);
//         }

//         //DBに格納するファイルネーム設定
//         //サーバー側の一時的なファイルネームと取得時刻を結合した文字列にsha256をかける．
//         $date = getdate();
//         $fname = $_FILES["upfile"]["tmp_name"] . $date["year"] . $date["mon"] . $date["mday"] . $date["hours"] . $date["minutes"] . $date["seconds"];
//         $fname = hash("sha256", $fname);

//         //画像・動画をDBに格納．
//         $sql = "INSERT INTO media(fname, extension, raw_data) VALUES (:fname, :extension, :raw_data);";
//         $stmt = $pdo->prepare($sql);
//         $stmt->bindValue(":fname", $fname, PDO::PARAM_STR);
//         $stmt->bindValue(":extension", $extension, PDO::PARAM_STR);
//         $stmt->bindValue(":raw_data", $raw_data, PDO::PARAM_STR);
//         $stmt->execute();
//     }
// } catch (PDOException $e) {
//     echo ("<p>500 Inertnal Server Error</p>");
//     exit($e->getMessage());
// }

// データベースにデータを挿入
$sql = 'INSERT INTO todo_table (id,name,support,doko,place,nojapan,itu,media, created_at, updated_at) VALUES (NULL, :name,:support,:doko,:place,:nojapan,:itu,:media, now(), now())';
$stmt = $pdo->prepare($sql);

// バインド変数を設定
$stmt->bindValue(':name', $name, PDO::PARAM_STR);
$stmt->bindValue(':support', $support, PDO::PARAM_STR);
$stmt->bindValue(':doko', $where, PDO::PARAM_STR);
$stmt->bindValue(':place', $place, PDO::PARAM_STR);
$stmt->bindValue(':nojapan', $nojapan, PDO::PARAM_STR);
$stmt->bindValue(':itu', $when, PDO::PARAM_STR);
$stmt->bindValue(':media', $videoFileName, PDO::PARAM_STR);


// SQL実行（実行に失敗すると `sql error ...` が出力される）
try {
    $status = $stmt->execute();
} catch (PDOException $e) {
    echo json_encode(["sql error" => "{$e->getMessage()}"]);
    exit();
};

// // データをフォーマット
// $line = "{$videoFileName}\n";

// // ファイルに書き込み
// $file = fopen('data/todo.txt', 'a');
// flock($file, LOCK_EX);
// fwrite($file, $line);
// flock($file, LOCK_UN);
// fclose($file);

// 入力ページにリダイレクト
header("Location: todo_input.php");
exit;

// SQL実行の処理

header('Location:todo_input.php');
exit();
