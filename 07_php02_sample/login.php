<?php
session_start();

// print_r($_SERVER);
// exit();
$errmessage = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // POST情報がある時の処理
    $mail = $_POST['mail'] ?? '';
    $pass = $_POST['pass'] ?? '';


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

    $userfile = './userinfo.txt';
    if (empty($errmessage) && file_exists($userfile)) {
        $users = file_get_contents($userfile);
        $users = explode("\n", $users);
        foreach ($users as $k => $v) {
            $v_ary = str_getcsv($v);
            if ($v_ary[0] == $mail) {
                if (password_verify($pass, $v_ary[1])) {
                    $_SESSION['mail'] = $mail;

                    // ログイン後画面にリダイレクトする※まずここから①
                    $host = $_SERVER['HTTP_HOST'];
                    $uri = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
                    //上でcharlistは省略
                    header("Location: //$host$uri/todo_input.php");
                    exit();
                }
            }
        }
        $errmessage[] = "ユーザー名またはパスワードが正しくありません。";
    } else {
        $errmessage[] = "ユーザーリストが見つかりません";
    }


    // 入力チェック時

    // IDパスワードの確認

} else {
    if (isset($_SESSION['mail']) && $_SESSION['mail']) {
        $host = $_SERVER['HTTP_HOST'];
        $uri = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
        //上でcharlistは省略
        header("Location: //$host$uri/todo_input.php");
        exit();
    }
    $_POST = array();
    $mail = "";
    $pass = "";
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ログイン</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <style>
        .button {
            text-align: center;
        }

        body {
            margin-top: 8%;
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

            <form action="./login.php" method="POST">
                <!-- 処理を行う宛先を指定 -->
                <!-- <div>
                    <label>
                        名前：
                        <input type="text" name="name" class="form-control" style="width:400px" required><br>
                    </label>
                </div> -->
                <P style="margin-left:40%;font-size:20px ">ログイン</P><br>
                <div>
                    <label>
                        メールアドレス：
                        <input type="text" name="mail" class="form-control" value="<?php echo htmlspecialchars($mail, ENT_QUOTES, 'UTF-8'); ?>" style="width:400px" required><br>
                    </label>
                </div>
                <div>
                    <label>
                        パスワード：
                        <input type="password" name="pass" class="form-control" style="width:400px" required><br>
                    </label>
                </div>

                <div class="button">
                    <input type="submit" value="ログイン" class="btn btn-primary btn-lg"><br><br>
                </div>

            </form>

            <p>新規会員登録の方は<a href="./register.php">こちら</a></p>
            <p>パスワードをお忘れの方は<a href="./forget.php">こちら</a></p>
        </div>
    </div>
</body>

</html>