<?php

// DB接続
include('functions.php');
$pdo = connect_to_db();

$filter_name = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'])) {
  $filter_name = $_POST['name'];
}

// 名前でソートされた場合のSQLクエリ
$sql = 'SELECT *, media AS video_path FROM todo_table';
if ($filter_name !== '') {
  $sql .= ' WHERE name LIKE :name'; // 名前でフィルタリング
}
$sql .= ' ORDER BY id DESC'; // IDを降順でソート（投稿された順）
// $sql = 'SELECT *, media AS video_path FROM todo_table';
// if ($filter_name !== '') {
//   $sql .= ' WHERE name LIKE :name'; // 名前でフィルタリング
// }
// $sql .= ' ORDER BY name ASC'; // 名前でソート

$stmt = $pdo->prepare($sql);

if ($filter_name !== '') {
  $stmt->bindValue(':name', '%' . $filter_name . '%', PDO::PARAM_STR);
}



try {
  $status = $stmt->execute();
} catch (PDOException $e) {
  echo json_encode(["sql error" => "{$e->getMessage()}"]);
  exit();
}

$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

$output = "";
foreach ($result as $record) {
  $output .= "
   <tr>
      <td style='border: 1px double black;'><span style='color: green; font-size:20px;'>{$record["name"]}</span>は、
     <span style='color: gray; font-size:20px; '>{$record["support"]}</span>と、
     <span style='color: black; font-size:20px;'>{$record["itu"]}</span>に
     <span style='color: red; font-size:20px;'>{$record["place"]}{$record["nojapan"]}</span>に行ったよ！<br>
   その日の一言は <span style='color: blue; font-size:20px;'>「{$record["doko"]}」</span>だったよ！</td>
   <td style='border: 1px solid black;'>
        <video width='160' height='120' controls>
            <source src='./uploads/{$record["video_path"]}' type='video/mp4'>
        </video>
    </td>
     <td style='border: 1px solid black;'><a href='todo_edit.php?id={$record["id"]}'>編集する</a></td>
     <td style='border: 1px solid black;'><a href='todo_delete.php?id={$record["id"]}'>削除する</a></td>
   </tr>
  ";
}


// 新しいデータを取得する
$sql_sorted = 'SELECT place, COUNT(*) as count FROM todo_table WHERE place IS NOT NULL AND place <> "" AND deleted = 0 GROUP BY place ORDER BY name ASC';
$stmt_sorted = $pdo->prepare($sql_sorted);
// 削除リンクからIDを取得
$id = isset($_GET['id']) ? $_GET['id'] : null;

// IDが存在する場合のみ削除処理を行う
if ($id !== null) {
  $sql_delete = "UPDATE todo_table SET deleted = 1 WHERE id = :id";
  $stmt_delete = $pdo->prepare($sql_delete);
  $stmt_delete->bindValue(':id', $id, PDO::PARAM_INT); // $id をバインドする
  $stmt_delete->execute();
}
try {
  $status_sorted = $stmt_sorted->execute();
} catch (PDOException $e) {
  echo json_encode(["sql error" => "{$e->getMessage()}"]);
  exit();
}
$sorted_result = $stmt_sorted->fetchAll(PDO::FETCH_ASSOC);

// データをJavaScriptの配列形式に変換する
$places_sorted = [];
$counts_sorted = [];
foreach ($sorted_result as $row) {
  $places_sorted[] = $row['place'];
  $counts_sorted[] = $row['count'];
}


$sql_count = 'SELECT place, COUNT(*) as count FROM todo_table WHERE place IS NOT NULL AND place <> "" GROUP BY place';
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
    body {
      background-color: #fffacd;
    }

    table {
      border-collapse: collapse;
      border-spacing: 0;
      width: 100%;
    }

    tr {
      border-bottom: 1px solid black;
    }

    td,
    th {
      padding: 10px;
      border: 1px solid black;
    }

    .chart-container {
      width: 50%;
      margin: auto;
    }

    .hidden {
      display: none;
    }

    .yoko {
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .sort {
      display: flex;
      justify-content: center;
    }

    .top {
      margin-left: 30px;
    }
  </style>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
  <audio id="audio" src="./Img/あの日見た景色.mp3" preload="auto"></audio>
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
      <div class="sort">
        <h3>思い出一覧</h3>
        <a class="top" href="todo_input.php">追加で記入する</a><br>

        <form class="top" method="POST" action="">
          <label for="name">名前で検索:</label>
          <input type="text" id="name" name="name" value="<?= htmlspecialchars($filter_name, ENT_QUOTES, 'UTF-8') ?>">
          <input type="submit" value="検索">
        </form><br>

      </div>
      <div class="yoko">
        <table class="yoko">
          <tbody id="memories">
            <?= $output ?>
          </tbody>
        </table>
      </div>
      <button id="showMoreButton" class="btn btn-primary btn-lg">もっと見る</button>
      <button id="showLessButton" class="hidden">閉じる</button>
    </div>
  </fieldset>
  <div class="container" style='margin-left: 40%;'>
    <p style='font-size:20px;'>みんながよくいってる地域！！</p>
  </div>
  <div class="chart-container">
    <canvas id="placeChart"></canvas>
  </div>
  <div class="chart-container">
    <canvas id="placePieChart"></canvas>
  </div>
  <!-- <div class="container">
    <h3>訪問回数ランキング</h3>
    <ol id="rankingList"></ol>
  </div> -->
  <div class="container" id="concentrationMessageContainer">
    <h3 class="yoko" id="concentrationMessage"></h3>
  </div>
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const ctx = document.getElementById('placeChart').getContext('2d');
      const counts = <?= json_encode($counts) ?>;
      const maxCount = Math.max(...counts);
      const backgroundColors = counts.map(count => count === maxCount ? 'rgba(255, 99, 132, 0.2)' : 'rgba(75, 192, 192, 0.2)');
      const borderColors = counts.map(count => count === maxCount ? 'rgba(255, 99, 132, 1)' : 'rgba(75, 192, 192, 1)');

      const placeChart = new Chart(ctx, {
        type: 'bar',
        data: {
          labels: <?= json_encode($places) ?>,
          datasets: [{
            label: '訪問回数',
            data: counts,
            backgroundColor: backgroundColors,
            borderColor: borderColors,
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

      document.addEventListener('DOMContentLoaded', () => {
        const ctx = document.getElementById('placeChart').getContext('2d');
        let placeChart; // Chartオブジェクトを保持する変数

        function updateChart() {
          // グラフがすでに描画されている場合は破棄する
          if (placeChart) {
            placeChart.destroy();
          }

          // 新しいデータを使用してグラフを再描画する
          placeChart = new Chart(ctx, {
            type: 'bar',
            data: {
              labels: <?= json_encode($places_sorted) ?>,
              datasets: [{
                label: '訪問回数',
                data: <?= json_encode($counts_sorted) ?>,
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
        }

        // 初回描画
        updateChart();
      });

      const music = document.getElementById('audio');
      music.volume = 0.3;
      music.play();

      const memories = document.getElementById('memories');
      const showMoreButton = document.getElementById('showMoreButton');
      const showLessButton = document.getElementById('showLessButton');

      const initialShowCount = 3;
      let showCount = initialShowCount;

      for (let i = initialShowCount; i < memories.children.length; i++) {
        memories.children[i].classList.add('hidden');
      }

      showMoreButton.addEventListener('click', () => {
        showCount += initialShowCount;

        if (showCount >= memories.children.length) {
          showMoreButton.style.display = 'none';
          showLessButton.classList.remove('hidden');
        }

        for (let i = 0; i < showCount; i++) {
          memories.children[i].classList.remove('hidden');
        }
      });

      showLessButton.addEventListener('click', () => {
        showMoreButton.style.display = 'block';
        showLessButton.classList.add('hidden');

        for (let i = initialShowCount; i < memories.children.length; i++) {
          memories.children[i].classList.add('hidden');
        }
      });
    });

    // 円グラフを描画する関数
    function drawPieChart() {
      const pieCtx = document.getElementById('placePieChart').getContext('2d');

      // データの準備
      const pieData = {
        labels: <?= json_encode($places) ?>,
        datasets: [{
          data: <?= json_encode($counts) ?>,
          backgroundColor: [
            'rgba(255, 99, 132, 0.2)',
            'rgba(54, 162, 235, 0.2)',
            'rgba(255, 206, 86, 0.2)',
            'rgba(75, 192, 192, 0.2)',
            'rgba(153, 102, 255, 0.2)',
            'rgba(255, 159, 64, 0.2)'
            // さらに必要な色を追加する場合はここに追加してください
          ],
          borderColor: [
            'rgba(255, 99, 132, 1)',
            'rgba(54, 162, 235, 1)',
            'rgba(255, 206, 86, 1)',
            'rgba(75, 192, 192, 1)',
            'rgba(153, 102, 255, 1)',
            'rgba(255, 159, 64, 1)'
            // さらに必要な色を追加する場合はここに追加してください
          ],
          borderWidth: 1
        }]
      };

      // 円グラフを描画
      const placePieChart = new Chart(pieCtx, {
        type: 'pie',
        data: pieData,
        options: {
          responsive: true,
          maintainAspectRatio: false,
          // その他のオプションは必要に応じて追加してください
        }
      });
    }

    // ページの読み込みが完了したら円グラフを描画
    document.addEventListener('DOMContentLoaded', drawPieChart);

    // // 訪問回数ランキングを生成する関数
    // function generateRanking() {
    //   const rankingList = document.getElementById('rankingList');
    //   rankingList.innerHTML = ''; // リストを初期化

    //   // ランキングを生成
    //   const sortedPlaces = <?= json_encode($places_sorted) ?>;
    //   const sortedCounts = <?= json_encode($counts_sorted) ?>;
    //   for (let i = 0; i < sortedPlaces.length; i++) {
    //     const listItem = document.createElement('li');
    //     listItem.textContent = `${sortedPlaces[i]}: ${sortedCounts[i]}回`;
    //     rankingList.appendChild(listItem);
    //   }
    // }

    // ページの読み込みが完了したらランキングを生成
    // document.addEventListener('DOMContentLoaded', generateRanking);

    // 特定の地域への集中度を計算する関数
    function calculateConcentration() {
      const maxCount = Math.max(...<?= json_encode($counts) ?>);
      const concentrationThreshold = maxCount * 0.75; // 集中度を判定する閾値（訪問回数の最大値の50%）

      const places = <?= json_encode($places) ?>;
      const counts = <?= json_encode($counts) ?>;
      const concentratedPlaces = [];

      // 集中度が閾値を超える地域を抽出
      for (let i = 0; i < counts.length; i++) {
        if (counts[i] >= concentrationThreshold) {
          concentratedPlaces.push(places[i]);
        }
      }

      return concentratedPlaces;
    }

    // 集中度を計算し、特定の地域を取得
    const concentratedPlaces = calculateConcentration();

    // 集中度メッセージを生成
    let concentrationMessage = '';
    if (concentratedPlaces.length > 0) {
      concentrationMessage = `訪問回数が集中している地域は  ${concentratedPlaces.join(', ')}です！`;
    } else {
      concentrationMessage = '特定の地域への訪問回数は均等に分散しています。';
    }

    // 集中度メッセージをHTMLに挿入
    const concentrationMessageContainer = document.getElementById('concentrationMessageContainer');
    const concentrationMessageElement = document.getElementById('concentrationMessage');
    concentrationMessageElement.textContent = concentrationMessage;
  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
</body>

</html>