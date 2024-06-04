<?php

// DB接続

$dbn = 'mysql:dbname=gs_l10_07;charset=utf8mb4;port=3306;host=localhost';
$user = 'root';
$pwd = '';

try {
  $pdo = new PDO($dbn, $user, $pwd);
} catch (PDOException $e) {
  echo json_encode(["db error" => "{$e->getMessage()}"]);
  exit();
};

// SQL作成&実行

$sql = 'SELECT * FROM todo_table';
$stmt = $pdo->prepare($sql);

try {
  $status = $stmt->execute();
} catch (PDOException $e) {
  echo json_encode(["sql error" => "{$e->getMessage()}"]);
  exit();
};

$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
// echo "<pre>";
// var_dump($result);
// exit();
// echo "</pre>";



$output = "";
foreach ($result as $record) {
  $output .= "
    <tr>
      <td><span style='color: green; font-size:20px ;'>{$record["name"]}</span>は、
     <span style='color: gray; font-size:20px ;'> {$record["support"]}</span>と、
     <span style='color: black; font-size:20px ;'>{$record["itu"]}</span>に
     <span style='color: red; font-size:20px ;'> {$record["place"]}{$record["nojapan"]}</span>に行ったよ！
   その日の一言は <span style='color: blue; font-size:20px ;'>「{$record["doko"]}」</span>だったよ！</td>
    </tr>
  ";
}

$sql_count = 'SELECT place, COUNT(*) as count FROM todo_table WHERE place IS NOT NULL AND place <> "" GROUP BY place';
// $sql_count = 'SELECT place, COUNT(*) as count FROM todo_table GROUP BY place';
$stmt_count = $pdo->prepare($sql_count);

try {
  $status_count = $stmt_count->execute();
} catch (PDOException $e) {
  echo json_encode(["sql error" => "{$e->getMessage()}"]);
  exit();
}

$count_result = $stmt_count->fetchAll(PDO::FETCH_ASSOC);
$places = [];
$counts = [];
foreach ($count_result as $row) {
  $places[] = $row['place'];
  $counts[] = $row['count'];
}


?>

<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>記録の森</title>

  <style>
    table {
      border-collapse: separate;
      /* セル間の境界線を分離する */
      border-spacing: 0;
      /* セルの間隔を0に設定 */
      width: 100%;
      /* テーブルの幅を100%に設定 */
    }

    tr {
      border-bottom: 1px solid black;
      /* 行の下部に区切り線を追加 */
    }

    td,
    th {
      padding: 10px;
      /* セルの余白を設定 */
      border: 1px solid black;
      /* セルの境界線を設定 */
    }

    td,
    th {
      padding: 10px;
      /* セルの余白を設定 */
    }

    .chart-container {
      width: 50%;
      /* 画面幅の半分 */
      margin: auto;
      /* 中央に配置 */
    }


    .hidden {
      display: none;
    }
  </style>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
  <audio id="audio" src="./Img/いい感じ.mp3" preload="auto"></audio>
  <nav class="navbar navbar-expand-lg bg-body-tertiary">
    <div class="container">
      <a class="navbar-brand" href="#">Welcome</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item">
            <a class="nav-link" href="./todo_input.php">Home(記録)</a>
          </li>
          <li class="nav-item">
            <a class="nav-link active" aria-current="page" href="./todo_read.php">投稿履歴</a>
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
  <fieldset>
    <div class="container">
      <legend>思い出一覧</legend>
      <a href="todo_input.php">追加で記入する
      </a>
      <table>
        <!-- <thead>
        <tr>
          <th>だれが</th>
          <th>だれと</th>
          <th>地名</th>
          <th>都道府県名</th>
          <th>海外地名</th>
          <th>いつ</th>
        </tr>
      </thead> -->
        <tbody id="memories">
          <!-- ここに<tr><td>deadline</td><td>todo</td><tr>の形でデータが入る -->
          <?= $output ?>
        </tbody>
      </table>
      <button id="showMoreButton" class="btn btn-primary btn-lg">もっと見る</button>

      <button id="showLessButton" class="hidden">閉じる</button>
    </div>
  </fieldset>

  <div class=" container chart-container">
    <canvas id="placeChart"></canvas>
  </div>
  <script>
    const ctx = document.getElementById('placeChart').getContext('2d');
    const placeChart = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: <?= json_encode($places) ?>,
        datasets: [{
          label: '訪問回数',
          data: <?= json_encode($counts) ?>,
          backgroundColor: 'rgba(75, 192, 192, 0.2)',
          borderColor: 'rgba(75, 192, 192, 1)',
          borderWidth: 1
        }]
      },
      options: {
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              stepSize: 1 // 縦軸のカウントを1ずつに設定
            }
          }
        }
      }
    });

    document.addEventListener('DOMContentLoaded', (event) => {
      const music = document.getElementById('audio');

      // 音量を小さく設定（0.0から1.0の範囲で設定）
      music.volume = 0.3;

      // ページロード時に音楽を再生
      music.play();
    });
    const memories = document.getElementById('memories');
    const showMoreButton = document.getElementById('showMoreButton');
    const showLessButton = document.getElementById('showLessButton');

    // 最初は10件まで表示
    const initialShowCount = 7;
    let showCount = initialShowCount;

    // 10件以上のデータがある場合、残りを非表示にする
    for (let i = initialShowCount; i < memories.children.length; i++) {
      memories.children[i].classList.add('hidden');
    }

    // もっと見るボタンをクリックした時の処理
    showMoreButton.addEventListener('click', () => {
      showCount += initialShowCount;

      // 表示する要素がない場合、ボタンを非表示にする
      if (showCount >= memories.children.length) {
        showMoreButton.style.display = 'none';
        showLessButton.classList.remove('hidden');
      }

      // showCountまでの要素を表示する
      for (let i = 0; i < showCount; i++) {
        memories.children[i].classList.remove('hidden');
      }
    });

    // 閉じるボタンをクリックした時の処理
    showLessButton.addEventListener('click', () => {
      showMoreButton.style.display = 'block';
      showLessButton.classList.add('hidden');

      // 初期表示まで非表示にする
      for (let i = initialShowCount; i < memories.children.length; i++) {
        memories.children[i].classList.add('hidden');
      }
    });
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
</body>


</html>