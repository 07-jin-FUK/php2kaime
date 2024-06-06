<?php
include('functions.php');



// id受け取り
$id = $_GET['id'];

// DB接続
$pdo = connect_to_db();


// SQL実行
$sql = 'SELECT * FROM todo_table WHERE id=:id';
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
try {
    $status = $stmt->execute();
} catch (PDOException $e) {
    echo json_encode(["sql error" => "{$e->getMessage()}"]);
    exit();
}

$record = $stmt->fetch(PDO::FETCH_ASSOC);



?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>編集画面</title>
</head>

<body>
    <form action="todo_update.php" method="POST">
        <div class="container" id="tyuuou">
            <fieldset>
                <p>編集画面</p>
                <div>
                    <input type="hidden" name="id" value="<?= $record['id'] ?>">
                </div>
                <div class="anke">
                    <input type="text" name="name" id="nameInput" value="<?= $record['name'] ?>" placeholder="お名前は？">
                </div>
                <div class="anke">
                    <input type="text" name="support" value="<?= $record['support'] ?>" placeholder="どなたと？">
                </div>
                <div class="anke">
                    <input type="date" name="itu" value="<?= $record['itu'] ?>">
                </div>
                <div class="anke">
                    <select name="place" id="weatherInput" value="<?= $record['place'] ?>">
                        <option value="">どこにいきましたか？</option>
                        <option value="北海道">北海道</option>
                        <option value="東京都">東京都</option>
                        <option value="佐賀県">佐賀県</option>
                        <option value="福岡県">福岡県</option>
                        <option value="大分県">大分県</option>
                        <option value="沖縄県">沖縄県</option>
                        <option value="長崎県">長崎県</option>
                        <option value="大阪府">大阪府</option>
                        <option value="埼玉県">埼玉県</option>
                        <option value="埼玉県">熊本県</option>
                    </select>
                    <!-- <input type="text" name="place" placeholder="都道府県は？"> -->
                    <br>(海外の方は下に記載ください)
                </div>


                <div class="anke">
                    (任意) <input type="text" name="nojapan" placeholder="海外の地名？" value="<?= $record['nojapan'] ?>">
                </div>
                <div class="anke">
                    <input type="text" name="doko" placeholder="一言（コメント）" value="<?= $record['doko'] ?>">

                </div>
                <!-- <div class="anke">
                    <label>動画アップロード</label>
                    <input type="file" name="upfile">
                    <br>

                </div> -->



                <div>
                    <button class=" btn btn-flat">思い出を書き換える！</button>
                </div>


            </fieldset>
            <a href="todo_read.php">一覧画面へ</a>

    </form>

</body>

</html>