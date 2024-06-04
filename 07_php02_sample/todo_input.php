<?php



session_start();

// var_dump($_SESSION);
// exit();
if (!isset($_SESSION['mail'])) {
  $host = $_SERVER['HTTP_HOST'];
  $uri = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
  //上でcharlistは省略
  header("Location: //$host$uri/login.php");
  exit();
}
$username = isset($_SESSION['name']) ? $_SESSION['name'] : 'ゲスト';
?>



<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>旅記録</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
  <style>
    .anke {

      /* display: flex;
      justify-content: center;  */
      align-items: center;
      margin-bottom: 10px;
    }



    @import url(//fonts.googleapis.com/css?family=Lato:300:400);

    body {
      margin: 0;
    }

    h1 {
      font-family: 'Lato', sans-serif;
      font-weight: 300;
      letter-spacing: 2px;
      font-size: 48px;
    }

    p {
      font-family: 'Lato', sans-serif;
      letter-spacing: 1px;
      font-size: 14px;
      color: #333333;
    }

    .header {
      position: relative;
      text-align: center;
      background: linear-gradient(60deg, rgba(84, 58, 183, 1) 0%, rgba(0, 172, 193, 1) 100%);
      color: white;
    }

    .logo {
      width: 50px;
      fill: white;
      padding-right: 15px;
      display: inline-block;
      vertical-align: middle;
    }

    .inner-header {
      height: 65vh;
      width: 100%;
      margin: 0;
      padding: 0;
    }

    .flex {
      /*Flexbox for containers*/
      display: flex;
      justify-content: center;
      align-items: center;
      text-align: center;
      margin-bottom: 10px;
    }

    .waves {
      position: relative;
      width: 100%;
      height: 15vh;
      margin-bottom: -7px;
      /*Fix for safari gap*/
      min-height: 100px;
      max-height: 150px;
    }

    .content {
      position: relative;
      height: 20vh;
      text-align: center;
      background-color: white;
    }

    /* Animation */

    .parallax>use {
      animation: move-forever 25s cubic-bezier(.55, .5, .45, .5) infinite;
    }

    .parallax>use:nth-child(1) {
      animation-delay: -2s;
      animation-duration: 7s;
    }

    .parallax>use:nth-child(2) {
      animation-delay: -3s;
      animation-duration: 10s;
    }

    .parallax>use:nth-child(3) {
      animation-delay: -4s;
      animation-duration: 13s;
    }

    .parallax>use:nth-child(4) {
      animation-delay: -5s;
      animation-duration: 20s;
    }

    @keyframes move-forever {
      0% {
        transform: translate3d(-90px, 0, 0);
      }

      100% {
        transform: translate3d(85px, 0, 0);
      }
    }

    /*Shrinking for mobile*/
    @media (max-width: 768px) {
      .waves {
        height: 40px;
        min-height: 40px;
      }

      .content {
        height: 30vh;
      }

      h1 {
        font-size: 24px;
      }
    }


    .btn,
    a.btn,
    button.btn {
      margin-left: 10px;
      font-size: 15px;
      font-weight: 700;
      line-height: 0.2;
      position: relative;
      display: inline-block;
      padding: 1rem 1rem;
      cursor: pointer;
      -webkit-user-select: none;
      -moz-user-select: none;
      -ms-user-select: none;
      user-select: none;
      -webkit-transition: all 0.3s;
      transition: all 0.3s;
      text-align: center;
      vertical-align: middle;
      text-decoration: none;
      letter-spacing: 0.1em;
      color: black;
      border-radius: 0.5rem;
      background-color: white;
    }

    .btn:hover {
      scale: 1.1;
      background-color: silver;
      color: blue;
    }

    label {
      font-size: 20px;
    }

    a.btn-flat {
      overflow: hidden;
      /* margin-left: 200px; */
      padding: 1.3rem 2rem;

      color: #fff;
      border-radius: 0;
      background: #000;
    }

    a.btn-flat span {
      position: relative;
    }

    a.btn-flat:before {
      position: absolute;
      top: 0;
      left: 0;

      width: 150%;
      height: 500%;

      content: "";
      -webkit-transition: all 0.5s ease-in-out;
      transition: all 0.5s ease-in-out;
      -webkit-transform: translateX(-98%) translateY(-25%) rotate(45deg);
      transform: translateX(-98%) translateY(-25%) rotate(45deg);

      background: #00b7ee;
    }

    a.btn-flat:hover:before {
      -webkit-transform: translateX(-9%) translateY(-25%) rotate(45deg);
      transform: translateX(-9%) translateY(-25%) rotate(45deg);
    }
  </style>
</head>

<body>
  <!--Hey! This is the original version
of Simple CSS Waves-->
  <audio id="audio" src="./Img/寝落ち.mp3" preload="auto" loop></audio>
  <div class="header">
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
      <div class="container">
        <a class="navbar-brand" href="#">Welcome</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            <li class="nav-item">
              <a class="nav-link active" aria-current="page" href="./todo_input.php">Home(記録)</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="./todo_read.php">投稿履歴</a>
            </li>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                オプション
              </a>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="./changepass.php">パスワード変更</a></li>
                <li><a class="dropdown-item" href="./forget.php">パスワードを忘れた
                  </a></li>
                <li><a class="dropdown-item" href="./register.php">新規ユーザー登録</a></li>

                <li><a class="dropdown-item" href="./logout.php">ログアウト</a></li>
              </ul>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="./logout.php">ログアウト</a>
            </li>
          </ul>
        </div>
      </div>
    </nav>
    <!--Content before waves-->
    <div class="inner-header flex">
      <!--Just the logo.. Don't mind this-->
      <svg version="1.1" class="logo" baseProfile="tiny" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 500 500" xml:space="preserve">
        <path fill="#FFFFFF" stroke="#000000" stroke-width="10" stroke-miterlimit="10" d="M57,283" />
      </svg>
      <form action="todo_create.php" method="POST">
        <div class="container" id="tyuuou">
          <fieldset>
            <div>
              <legend>今日も1日お疲れさまでした!<br>楽しめましたか？？</legend>
              <a href="todo_read.php"></a>
            </div>
            <div class="anke">
              <input type="text" name="name" id="nameInput" placeholder="お名前は？">
            </div>
            <div class="anke">
              <input type="text" name="support" placeholder="どなたと？">
            </div>
            <div class="anke">
              <input type="date" name="itu">
            </div>
            <div class="anke">
              <select name="place" id="weatherInput">
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
              (任意) <input type="text" name="nojapan" placeholder="海外の地名？">
            </div>
            <div class="anke">
              <input type="text" name="doko" placeholder="一言（コメント）">

            </div>



            <div>
              <button class=" btn btn-flat">思い出を追加！</button>
            </div>


          </fieldset>
        </div>
      </form>
    </div>

    <!--Waves Container-->
    <div>
      <svg class="waves" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 24 150 28" preserveAspectRatio="none" shape-rendering="auto">
        <defs>
          <path id="gentle-wave" d="M-160 44c30 0 58-18 88-18s 58 18 88 18 58-18 88-18 58 18 88 18 v44h-352z" />
        </defs>
        <g class="parallax">
          <use xlink:href="#gentle-wave" x="48" y="0" fill="rgba(255,255,255,0.7" />
          <use xlink:href="#gentle-wave" x="48" y="3" fill="rgba(255,255,255,0.5)" />
          <use xlink:href="#gentle-wave" x="48" y="5" fill="rgba(255,255,255,0.3)" />
          <use xlink:href="#gentle-wave" x="48" y="7" fill="#fff" />
        </g>
      </svg>
    </div>
    <!--Waves end-->

  </div>
  <!--Header ends-->

  <!--Content starts-->
  <div class="content flex">
    <p>By.Jinmaru | Anyohaseyo </p>
  </div>
  <!--Content ends-->




  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
  <script>
    document.addEventListener('DOMContentLoaded', (event) => {
      const music = document.getElementById('audio');
      // 音量を小さく設定（0.0から1.0の範囲で設定）
      music.volume = 0.3;

      // ページロード時に音楽を再生
      music.play();

      // 名前の入力フィールドがクリックされたときに音楽を再生
      nameInput.addEventListener('click', () => {
        music.play();
      });
    });
  </script>
</body>

</html>