<?php

session_start();
// var_dump($_SESSION);
// exit();

$errmessage = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // POST情報がある時の処理
    $mail = $_POST['mail'] ?? '';
    $pass = $_POST['pass'] ?? '';
    $pass2 = $_POST['pass2'] ?? '';
    $name = $_POST['name'] ?? '';

    if (!$mail) {
        $errmessage[] = "Eメールを入力してください";
    } else if (strlen($mail) > 200) {
        $errmessage[] = "Eメールは200文字以内に指定してください";
    } else if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
        $errmessage[] = "Eメールアドレスは不正です";
    }

    if (!$pass) {
        $errmessage[] = "パスワードを入力してください";
    } else if (strlen($pass) > 100) {
        $errmessage[] = "パスワードは100文字以内に指定してください";
    }

    if ($pass !== $pass2) {
        $errmessage[] = "確認用パスワードが一致していません";
    }

    $userfile = './userinfo.txt';
    $users = array();
    if (file_exists($userfile)) {
        $users = file_get_contents($userfile);
        $users = explode("\n", $users);
        foreach ($users as $k => $v) {
            $v_ary = str_getcsv($v);
            if ($v_ary[0] == $mail) {
                $errmessage[] = "このEメールは既に登録されています。";
                break;
            }
        }
    }








    $userfile = './userinfo.txt';
    if (empty($errmessage)) {
        $ph = password_hash($pass, PASSWORD_DEFAULT);
        $line = '"' . $mail . '","' . $ph . '"' . "\n";
        $ret = file_put_contents($userfile, $line, FILE_APPEND);

        if ($ret !== false) {
            $_SESSION['name'] = $name; // ユーザーネームをセッションに保存
            $_SESSION['mail'] = $mail; // メールをセッションに保存
            $host = $_SERVER['HTTP_HOST'];
            $uri = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
            header("Location: //$host$uri/login.php");
            exit();
        } else {
            $errmessage[] = "ファイル書き込みに失敗しました";
        }
    }
} else {
    $name = '';
    $mail = '';
    $pass = '';
    $pass2 = '';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>新規登録</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <style>
        .button {
            text-align: center;
        }

        body {
            margin-top: 5%;
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

            <form action="./register.php" method="POST">
                <!-- 処理を行う宛先を指定 -->
                <P style="margin-left:35%;font-size:20px ">新規会員登録</P>
                <div>
                    <label>
                        名前：
                        <input type="text" name="name" value="<?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?>" class=" form-control" style="width:400px"><br>
                    </label>
                </div>
                <div>
                    <label>
                        メールアドレス：
                        <input type="text" name="mail" value="<?php echo htmlspecialchars($mail, ENT_QUOTES, 'UTF-8'); ?>" class="form-control" style="width:400px"><br>
                    </label>
                </div>
                <div>
                    <label>
                        パスワード：
                        <input type="password" name="pass" class="form-control" style="width:400px"><br>
                    </label>
                </div>
                <div>
                    <label>
                        パスワード(確認)：
                        <input type="password" name="pass2" class="form-control" style="width:400px"><br>
                    </label>
                </div>
                <div class="button">
                    <input type="submit" value="登録" class="btn btn-primary btn-lg"><br><br>
                </div>

            </form>

            <p>すでに登録済みの方は<a href="./login.php">こちら</a></p>
        </div>
    </div>
</body>

</html>