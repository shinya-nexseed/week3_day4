<?php
    // mysql系の関数はPHP5.5系からは非推奨になりました。
    // PHP5.5系から推奨になった新しい書き方は、mysqli系で、
    // よりセキュアなコードです。

    // PHPからDBへの接続
    // mysqli_connect()関数
    // DBに接続するための関数です。
    // 引数を4つ持っていて、
    // ホスト名、ユーザー名、パスワード、DB名
    // の順番で付けます
    // (引数とは)
    //    関数の後の()の中にある値のこと
    $db = mysqli_connect('localhost', 'root', 'mysql', 'online_bbs') or die(mysqli_connect_error()); // ①
    // dieで処理が失敗したときにはプログラムを停止してエラーを出している

    // DBから情報をひきだす際の文字コードを指定
    mysqli_set_charset($db,'utf8'); // ②


?>

<?php
    // データの保存
    
    // sqlのINSERT文
    // INSERT文は情報を入力するための文
    // INTO テーブル名でテーブルを指定し、SET以下で入力する値を決める
    // あとはカラム名=データ,を繰り返して指定する
    // この時、全体を文字列として囲む''と、データの文字を囲む""がかぶらないよう注意
    // $sql = 'INSERT INTO post SET name="Takuya", comment="筋肉痛", created_at="2015-08-20 09:40:00" ';
    // mysqli_query($db,$sql);


    // スーパーグローバル変数
    // PHPには、いつ何時どのファイルからでも使用することができる変数が存在します
    // それがスーパーグローバル変数
    // 形式は $_大文字の変数名 なので、どこにいても見つけやすいです。
    // 今回はその中の$_POSTという変数を使用
    // この変数には、htmlのformタグでmethod=postを指定していた場合の
    // inputタグにユーザーが入力したデータが入っています
    // この変数の中身も連想配列になっている
    // ひとつのinputタグのデータを取得したい場合、
    // $_POST['inputタグのname=のあとにつけた文字列']で取得可能


    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // htmlのformからデータを受け取って変数にする
        // mysqli_real_escape_string()関数は、特殊文字をエスケープします
        // 例えば、悪意あるユーザーがformに直接sql文を入力して、
        // データを壊したり盗んだりするsqlインジェクション攻撃などから
        // 守るためにこの関数が必要です。
        $name = mysqli_real_escape_string($db, $_POST['name']);
        $comment = mysqli_real_escape_string($db, $_POST['comment']);

        // sprintf()関数
        // ''の中の文字をスライスして、その後に指定した文字列を挿入するようなイメージ (ようは文字列を連結させるための関数)
        // ''内に%sなどの特殊変換文字を入れる
        // ,で区切ったあとの文字列や変数の中身を%sと置き換える
        // %sの数と、 ,で区切って配置している文字列もしくは変数の数がリンクしている必要がある
        $sql = sprintf('INSERT INTO post SET name="%s", comment="%s", created_at="%s" ',
            $name,
            $comment,
            date('Y-m-d H:i:s')
        );
        mysqli_query($db, $sql);
    }
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>Online BBS</title>
</head>
<body>
  <h1>ひとこと掲示板</h1>
  <form action="bbs.php" method="post">
    名前: <input type="text" name="name"><br>
    ひとこと: <input type="text" name="comment" size="60"><br>
    <input type="submit" name="submit" value="送信">
  </form>

  <?php
      // sql文の作成
      // sql文とは
      // MySQLなどのDBに命令を与えて、データを操作するための文
      // ここでは、SELECT文というDB内の情報を取得するための命令文を使用
      // *の部分は、カラム名を入れるとそのカラムの情報のみ取得できる
      // 今回はすべてのカラムの情報を取得したいので、*「すべて」を選択
      // 「FROM テーブル名」でどのテーブルにアクセスするか指定
      // ORDER BY で「何順」でデータを取得するかを指定できる
      // 今回はcreated_atカラムのデータを軸に順に取る
      // DESCで降順に取得できる (新しい日付のデータから並ぶ)
      // 昇順はASC
      $sql = "SELECT * FROM `post` ORDER BY `created_at` DESC";

      // 実際にデータを取得しているコード
      // $resultに取得したデータ全件が入っている
      // mysqli_query()関数を使用して、データを取得している
      // query (クエリ)とは、データを取得する際の条件や処理そのもののこと
      $result = mysqli_query($db,$sql) or die(mysqli_error($db));
  ?>

  <ul>
    <!-- 
      $resultに入っている複数のデータを、while文を使って$postに一件ずつ
      格納して以下の処理を実行する
      $postの中には「key => value,key => value」といった連想配列の形で
      データが入っていて、今回だと
      「'name' => 'shinyahirai', 'comment' => 'ほげほげ', 'created_at' => '2015....'」といったデータが入っている
      $post一件の更にどのカラムのデータが欲しいかを決めて取得する際は
      連想配列へのデータのアクセスの仕方を思い出して下さい。
      $変数['key']の形です
     -->
    <?php while ($post = mysqli_fetch_assoc($result)): ?>
    <li><?php echo $post['name']; ?>: <?php echo $post['comment'] ?></li>
    <?php endwhile; ?>
  </ul>
</body>
</html>
