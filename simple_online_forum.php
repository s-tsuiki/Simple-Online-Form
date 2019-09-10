<?php
	//必要な値を設定
	$id = 1;
	$date_time = date("Y/m/d H:i:s");
	//パスワードが正しいかの確認 0:初期状態 1:正しい 2:正しくない
	$is_correct = 0;
	//どの状況かを判断
	$case = 0;

	//データベースへの接続
	//$dsnの式の中にスペースを入れないこと！
	$dsn = 'データベース名';
	$user = 'ユーザー名';
	$password = 'パスワード';
	$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

	//データベースの作成
	$sql = "CREATE TABLE IF NOT EXISTS comment_database"
	." ("
	. "id INT AUTO_INCREMENT PRIMARY KEY,"
	. "name char(32),"
	. "comment TEXT,"
	. "date_time DATETIME,"
	. "password char(32)"
	.");";
	$stmt = $pdo->query($sql);

	//入力フォームの処理
	if(!empty($_POST['e_number'])){
		//編集モード
		$case = 1;

		if(isset($_POST['name']) || isset($_POST['comment'])){
			
			if(!empty($_POST['name']) && !empty($_POST['comment']) && !empty($_POST['password'])){
				//POSTで各値を受け取る
				$id = $_POST['e_number'];
				$name = $_POST['name'];
				$comment = $_POST['comment'];
				$password = $_POST['password'];
				
				//入力したデータをupdateによって編集する
				$sql = 'update comment_database set name=:name,comment=:comment,date_time=:date_time,password=:password where id=:id';
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':name', $name, PDO::PARAM_STR);
				$stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
				$stmt->bindParam(':date_time', $date_time, PDO::PARAM_STR);	//注意
				$stmt->bindParam(':password', $password, PDO::PARAM_STR);
				$stmt->bindParam(':id', $id, PDO::PARAM_INT);
				$stmt->execute();

				//初期化
				$name = "";
				$comment = "";
				$e_number = NULL;
			}
		}
	}
	elseif(isset($_POST['name']) || isset($_POST['comment'])){
		//入力モード
		$case = 2;
		if(!empty($_POST['name']) && !empty($_POST['comment'])&& !empty($_POST['password'])){
			//POSTで各値を受け取る
			$name = $_POST['name'];
			$comment = $_POST['comment'];
			$password = $_POST['password'];
			
			//insertを行ってデータを入力
			$sql = $pdo -> prepare("INSERT INTO comment_database (name, comment, date_time, password) VALUES (:name, :comment, :date_time, :password)");
			$sql -> bindParam(':name', $name, PDO::PARAM_STR);
			$sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
			$sql -> bindParam(':date_time', $date_time, PDO::PARAM_STR);	//注意
			$sql -> bindParam(':password', $password, PDO::PARAM_STR);
			$sql -> execute();
			
			//初期化
			$name = "";
			$comment = "";
			$e_number = NULL;
		}
	}

	//削除フォームの処理
	elseif(isset($_POST['delete_number'])){
		$case = 3;
		if(!empty($_POST['delete_number'])&& !empty($_POST['delete_password'])){
			//削除番号を格納
			$id = $_POST['delete_number'];
			$password = $_POST['delete_password'];
			
			//入力したデータをdeleteによって削除する
			if($id > 0){
				$sql = 'delete from comment_database where id=:id and password=:password';
				$stmt = $pdo->prepare($sql);
				
				$stmt->bindParam(':id', $id, PDO::PARAM_INT);
				$stmt->bindParam(':password', $password, PDO::PARAM_STR);
				$stmt->execute();
				if($stmt->rowCount() == 1){
					//削除成功
					$is_correct = 1;
				}else{
					//パスワードが一致しないとき、削除しない
					$is_correct = 2;
				}
			}
		}
	}

	//編集フォームの処理
	elseif(isset($_POST['edit_number'])){
		$case = 4;
		if(!empty($_POST['edit_number']) && !empty($_POST['edit_password'])){
			//編集番号を格納
			$id = $_POST['edit_number'];
			$password = $_POST['edit_password'];
			
			if($id > 0){
				$sql = 'SELECT * FROM comment_database';
				$stmt = $pdo->query($sql);
				$results = $stmt->fetchAll();
				//編集対象番号の名前とコメントを取得する
				foreach($results as $row){
					if($row['id'] === $id && $row['password'] === $password){
						//編集番号をセット
						$e_number = $row['id'];
						//名前とコメントを取得
						$name = $row['name'];
						$comment = $row['comment'];
						$is_correct = 1;
					}elseif($row['id'] === $id){
						$is_correct = 2;
					}
				}
			}
		}
	}
	//パスワードの初期化
	$password = "";
?>

<!DOCTYPE html>
<html lang = "ja">
<head>
  <meta name="viewport" content="width=320, height=480, initial-scale=1.0, minimum-scale=1.0, maximum-scale=2.0, user-scalable=yes"><!-- for smartphone. ここは一旦、いじらなくてOKです。 -->
	<meta charset="utf-8"><!-- 文字コード指定。ここはこのままで。 -->
	<title>Web掲示板</title>
</head>

<body>
<h1>Web掲示板</h1>
<h2>テーマ:雑談(何を話してもよいです！)</h2>

<h2>入力フォーム</h2>
<form method = "post" action = "simple_online_forum.php">
 名前:<input type = "text" name = "name" value = <?php if(!empty($name)){echo "'$name'";}?> > <br>
 コメント:<input type = "text" name = "comment" value = <?php if(!empty($comment)){echo "'$comment'";}?> > <br>
 パスワード:<input type = "password" name = "password"><br>
 <input type = "submit" value = "送信">
 <input type = "hidden" name = "e_number" value = <?php if(!empty($e_number)){echo $e_number;}?> >
</form>

<h2>削除フォーム</h2>
<form method = "post" action = "simple_online_forum.php">
 削除番号:<input type = "number" name = "delete_number"><br>
 パスワード:<input type = "password" name = "delete_password"><br>
 <input type = "submit" value = "削除">
</form>

<h2>編集フォーム</h2>
<form method = "post" action = "simple_online_forum.php">
 編集番号:<input type = "number" name = "edit_number"><br>
 パスワード:<input type = "password" name = "edit_password"><br>
 <input type = "submit" value = "編集">
</form>

</body>

</html>

<br>

<?php
	//エラーメッセージ
	if($case == 1 || $case == 2){
		if(empty($_POST['name'])){
			echo "-----------------------------------------<br>";
			echo "Error: name is empty.<br>";
			echo "-----------------------------------------";
		}
		elseif(empty($_POST['comment'])){
			echo "-----------------------------------------<br>";
			echo "Error: comment is empty.<br>";
			echo "-----------------------------------------";
		}
		elseif(empty($_POST['password'])){
			echo "-----------------------------------------<br>";
			echo "Error: password is empty.<br>";
			echo "-----------------------------------------";
		}
		elseif($is_correct == 2){
			echo "-----------------------------------------<br>";
			echo "Error: password is incorrect.<br>";
			echo "-----------------------------------------";
		}
	}
	elseif($case == 3){
		if(empty($_POST['delete_number'])){
			echo "-----------------------------------------<br>";
			echo "Error: delete number is empty.<br>";
			echo "-----------------------------------------";
		}
		elseif($_POST['delete_number'] <= 0){
			echo "-----------------------------------------<br>";
			echo "Error: delete number is invalid.<br>";
			echo "-----------------------------------------";
		}
		elseif(empty($_POST['delete_password'])){
			echo "-----------------------------------------<br>";
			echo "Error: password is empty.<br>";
			echo "-----------------------------------------";
		}
		elseif($is_correct == 2){
			echo "-----------------------------------------<br>";
			echo "Error: password is incorrect.<br>";
			echo "-----------------------------------------";
		}
	}
	elseif($case == 4){
		if(empty($_POST['edit_number'])){
			echo "-----------------------------------------<br>";
			echo "Error: edit number is empty.<br>";
			echo "-----------------------------------------";
		}
		elseif($_POST['edit_number'] <= 0){
			echo "-----------------------------------------<br>";
			echo "Error: edit number is invalid.<br>";
			echo "-----------------------------------------";
		}
		elseif(empty($_POST['edit_password'])){
			echo "-----------------------------------------<br>";
			echo "Error: password is empty.<br>";
			echo "-----------------------------------------";
		}
		elseif($is_correct == 2){
			echo "-----------------------------------------<br>";
			echo "Error: password is incorrect.<br>";
			echo "-----------------------------------------";
		}
	}
	
?>

<h2>コメント</h2>

<?php
	//現時点でのテーブル内容の表示
	$sql = 'SELECT * FROM comment_database';
	$stmt = $pdo->query($sql);
	$results = $stmt->fetchAll();
	foreach ($results as $row){
		//$rowの中にはテーブルのカラム名が入る
		echo $row['id'].'　';
		echo $row['name'].'　';
		echo $row['comment'].'　';
		echo date('Y/m/d H:i:s', strtotime($row['date_time'])).'<br>';
	}
?>