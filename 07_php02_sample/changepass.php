<?php
session_start();
if (!isset($_SESSION['mail'])) {
    $host = $_SERVER['HTTP_HOST'];
    $uri = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
    //上でcharlistは省略
    header("Location: //$host$uri/login.php");
    exit();
}

// print_r($_SERVER);
// exit();
$errmessage = array();
$complete = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // POST情報がある時の処理
    $mail = $_POST['mail'] ?? '';
    $pass = $_POST['pass'] ?? '';
    $npass = $_POST['newpass'] ?? '';

    if (!$pass) {
        $errmessage[] = "現在のパスワードを入力してください";
    } else if (strlen($pass) > 100) {
        $errmessage[] = "現在のパスワードは100文字以内に指定してください";
    }

    if (!$pass) {
        $errmessage[] = "新しいパスワードを入力してください";
    } else if (strlen($pass) > 100) {
        $errmessage[] = "新しいパスワードは100文字以内に指定してください";
    }

    $userfile = './userinfo.txt';
    if (empty($errmessage) && file_exists($userfile)) {
        $users = file_get_contents($userfile);
        $users = explode("\n", $users);
        foreach ($users as $k => $v) {
            $v_ary = str_getcsv($v);
            if ($v_ary[0] == $_SESSION['mail']) {
                if (password_verify($pass, $v_ary[1])) {
                    $ph = password_hash($npass, PASSWORD_DEFAULT);

                    $line = '"' . $_SESSION['mail'] . '","' . $ph . '"';
                    $users[$k] = $line;
                    $userinfo = implode("\n", $users);
                    $ret = file_put_contents($userfile, $userinfo);
                    $complete = true;
                    break;
                }
            }
        }
        if (!$complete) {
            $errmessage[] = "現在のパスワードが正しくありません。";
        }
    } else {
        $errmessage[] = "ユーザーリストが見つかりません";
    }


    // 入力チェック時

    // IDパスワードの確認

} else {
    $_POST = array();
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>パスワード変更</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <style>
        .button {
            text-align: center;
        }
    </style>

</head>

<body>

    <div class="container">
        <div class="mx-auto" style="width:400px">
            <?php
            if ($errmessage) {
                echo '<div class="alert alert-danger" role="alert">';
                echo implode('<br>', $errmessage);
                echo '</div>';
            }
            ?>
            <?php if ($complete) { ?>
                パスワードを変更しました。
                <p>ホームに戻る<a href="./todo_input.php">こちら</a></p>
            <?php } else { ?>
                <form action="./changepass.php" method="POST">

                    <div>
                        <label>
                            現在のパスワード：
                            <input type="password" name="pass" class="form-control" style="width:400px" required><br>
                            新しいパスワード：
                            <input type="password" name="newpass" class="form-control" style="width:400px" required><br>
                        </label>
                    </div>

                    <div class="button">
                        <input type="submit" value="変更" class="btn btn-primary btn-lg"><br><br>
                    </div>

                </form>
            <?php } ?>

        </div>
    </div>
</body>

</html>