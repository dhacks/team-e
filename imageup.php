<?php
require_once 'init.php';

$user_id = $_SESSION['user_id'];
/* HTML特殊文字をエスケープする関数 */
function h($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

// XHTMLとしてブラウザに認識させる
header('Content-Type: application/xhtml+xml; charset=utf-8');

try {

    // データベースに接続
    $pdo = new PDO(
        'mysql:dbname=egm-dhacks_test;host=mysql429.db.sakura.ne.jp;charset=utf8',
        'egm-dhacks',
        'egm_dhacks',
        [
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );

    /* アップロードがあったとき */
    if (isset($_FILES['upfile']['error']) && is_int($_FILES['upfile']

['error'])) {


        // バッファリングを開始
        ob_start();

        try {

            // $_FILES['upfile']['error'] の値を確認
            switch ($_FILES['upfile']['error']) {
                case UPLOAD_ERR_OK: // OK
                    break;
                case UPLOAD_ERR_NO_FILE:   // ファイル未選択
                    throw new RuntimeException('ファイルが選択されていません', 

400);
                case UPLOAD_ERR_INI_SIZE:  // php.ini定義の最大サイズ超過
                case UPLOAD_ERR_FORM_SIZE: // フォーム定義の最大サイズ超過
                    throw new RuntimeException('ファイルサイズが大きすぎます', 

400);
                default:
                    throw new RuntimeException('その他のエラーが発生しました', 

500);
            }

            // $_FILES['upfile']['mime']の値はブラウザ側で偽装可能なので
            // MIMEタイプを自前でチェックする
            if (!$info = @getimagesize($_FILES['upfile']['tmp_name'])) {
                throw new RuntimeException('有効な画像ファイルを指定してくださ

い', 400);
            }
            if (!in_array($info[2], [IMAGETYPE_GIF, IMAGETYPE_JPEG, 

IMAGETYPE_PNG], true)) {
                throw new RuntimeException('未対応の画像形式です', 400);
            }

            // サムネイルをバッファに出力
            $create = str_replace('/', 'createfrom', $info['mime']);
            $output = str_replace('/', '', $info['mime']);
            if ($info[0] >= $info[1]) {
                $dst_w = 120;
                $dst_h = ceil(120 * $info[1] / max($info[0], 1));
            } else {
                $dst_w = ceil(120 * $info[0] / max($info[1], 1));
                $dst_h = 120;
            }
            if (!$src = @$create($_FILES['upfile']['tmp_name'])) {
                throw new RuntimeException('画像リソースの生成に失敗しました', 

500);
            }
            $dst = imagecreatetruecolor($dst_w, $dst_h);
            imagecopyresampled($dst, $src, 0, 0, 0, 0, $dst_w, $dst_h, $info

[0], $info[1]);
            $output($dst);
            imagedestroy($src);
            imagedestroy($dst);

            
            // INSERT処理
            $user_data = getUserData($pdo, $user_id);
            $user_id = $user_data["id"];

            $lastEvent = getLastEvent($pdo, $user_id);

            $stmt = $pdo->prepare('INSERT INTO image (event_id,name,type,raw_data,thumb_data,date) VALUES(?,?,?,?,?,?)');
            $stmt->execute([
                $lastEvent["id"],
                $_FILES['upfile']['name'],
                $info[2],
                file_get_contents($_FILES['upfile']['tmp_name']),
                ob_get_clean(), // バッファからデータを取得してクリア
                (new DateTime('now', new DateTimeZone('Asia/Tokyo')))->format('Y-m-d H:i:s'),
            ]);

            $msgs[] = ['green', 'ファイルは正常にアップロードされました'];
            $index_url = 'index.php';
            header("Location: {$index_url}");
            exit;

        } catch (RuntimeException $e) {

            while (ob_get_level()) {
                ob_end_clean(); // バッファをクリア
            }
            http_response_code($e instanceof PDOException ? 500 : $e->getCode

());
            $msgs[] = ['red', $e->getMessage()];

        }

    /* ID指定があったとき */
    } elseif (isset($_GET['id'])) {

        try {

            $stmt = $pdo->prepare('SELECT type, raw_data FROM image WHERE id = 

? LIMIT 1');
            $stmt->bindValue(1, $_GET['id'], PDO::PARAM_INT);
            $stmt->execute();
            if (!$row = $stmt->fetch()) {
                throw new RuntimeException('該当する画像は存在しません', 404);
            }
            header('X-Content-Type-Options: nosniff');
            header('Content-Type: ' . image_type_to_mime_type($row['type']));
            echo $row['raw_data'];
            exit;

        } catch (RuntimeException $e) {

            http_response_code($e instanceof PDOException ? 500 : $e->getCode

());
            $msgs[] = ['red', $e->getMessage()];

        }

    }

    // サムネイル一覧取得
    $rows = $pdo->query('SELECT id,event_id,name,type,thumb_data,date FROM image ORDER 

BY date DESC')->fetchAll();

} catch (PDOException $e) {

    http_response_code(500);
    $msgs[] = ['red', $e->getMessage()];

}

$db = connectDb();

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <title>画像アップロード</title>
</head>
<body>
<div>
    <p>イベント名　：
    <?php $event_data = getLastEvent($db, $user_id);
    print $event_data["event_name"];
    ?>
    </p>
</div>
  <form enctype="multipart/form-data" method="post" action="./imageup.php">
    <fieldset>
      <legend>画像ファイルを選択(GIF, JPEG, PNGのみ対応)</legend>
      <input type="file" name="upfile" /><br />
      <input type="submit" value="送信" />
    </fieldset>
  </form>
</body>
</html>
