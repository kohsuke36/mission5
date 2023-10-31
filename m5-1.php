<?php
    $dsn = 'mysql:dbname=データベース名;host=localhost';//data source number
    $user = 'ユーザー名';
    $password = 'パスワード';
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));//php data object
    $sql = "CREATE TABLE IF NOT EXISTS post_table"
    ." ("
    . "id INT AUTO_INCREMENT PRIMARY KEY,"
    . "name CHAR(32),"
    . "comment TEXT,"
    . "date DATETIME,"
    . "pass TEXT"
    .");";
    $stmt = $pdo->query($sql);
    
    //編集実行機能
    if(!empty($_POST["name"]) && !empty($_POST["comment"]) && !empty($_POST["editMode"])){
        $edit=$_POST["editMode"];
        $name=$_POST["name"];
        $comment=$_POST["comment"];
        if (isset($_POST["pass"])) {
            if(!empty($_POST["pass"]) || $_POST["pass"]=="0"){
                $pass = $_POST["pass"];
            }
        } else {
            $pass = null;
        }
        $date=date("Y-m-d H:i:s");
        $id = $edit;
        $sql = 'UPDATE post_table SET name=:name,comment=:comment,date=:date,pass=:pass WHERE id=:id';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':date', $date, PDO::PARAM_STR);
        $stmt->bindParam(':pass', $pass, PDO::PARAM_STR);
        $stmt->execute();
    //新規投稿
    }elseif (!empty($_POST["name"]) && !empty($_POST["comment"])){
        $name=$_POST["name"];
        $comment=$_POST["comment"];
        $date=date("Y/m/d H:i:s");
        if (isset($_POST["pass"])) {
            if(!empty($_POST["pass"]) || $_POST["pass"]=="0"){
                $pass = $_POST["pass"];
            }
        } else {
            $pass = null;
        }
        $sql = "INSERT INTO post_table (name, comment, date, pass) VALUES (:name, :comment, :date, :pass)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
        $stmt->bindParam(':date', $date, PDO::PARAM_STR);
        $stmt->bindParam(':pass', $pass, PDO::PARAM_STR);
        $stmt->execute();
    }
    //削除機能
    if (!empty($_POST["deleteNum"]) && isset($_POST["deletePass"])){
        if(!empty($_POST["deletePass"]) || $_POST["deletePass"]=="0"){
            $deletePass = $_POST["deletePass"];
            $deleteNum=$_POST["deleteNum"];
        
        //削除対象番号の行の情報取得
            $sql = 'SELECT * FROM post_table WHERE id=:id';
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $deleteNum, PDO::PARAM_INT);
            $stmt->execute();
            $row=$stmt->fetch();
        //削除実行
            if($row && $deletePass == $row['pass']){
                $id = $deleteNum;
                $sql = 'delete from post_table where id=:id';
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
            }
        }
    }
        //編集選択機能
    if(!empty($_POST["editNum"]) && isset($_POST["editPass"])){
        if(!empty($_POST["editPass"]) || $_POST["editPass"]=="0"){
            $editPass = $_POST["editPass"];
            $editNum=$_POST["editNum"];
            $sql = 'SELECT * FROM post_table WHERE id=:id';
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $editNum, PDO::PARAM_INT);
            $stmt->execute();
            $row=$stmt->fetch();
            if($row && $editPass == $row['pass']){
                $editName=$row['name'];
                $editText=$row['comment'];
                $editMode=$editNum;
            }
        }
    }
        
?>
    
    <!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>mission_5</title>
</head>
<body>
    <h1>歳追うごとに嫌いな食べ物減るけど好きな食べ物への熱量も減ってない？</h1>
    <form action="" method="post">
        <input type="text" name="name" placeholder="名前" value="<?php if(isset($editName)) { echo $editName;} ?>">
        <input type="text" name="comment" placeholder="コメント" value="<?php if(isset($editText)) { echo $editText;} ?>">
        <input type="hidden" name="editMode" value="<?php if(isset($editMode)){ echo $editMode;}?>">
        <input type="password" name="pass" placeholder="パスワード">
        <input type="submit" name="submit">
    </form>
     <form action="" method="post">
        <input type="number" name="deleteNum" placeholder="削除対象番号">
        <input type="password" name="deletePass" placeholder="パスワード">
        <input type="submit" name="deleteSub" value="削除">
    </form>
    <form action="" method="post">
        <input type="number" name="editNum" placeholder="編集対象番号">
        <input type="password" name="editPass" placeholder="パスワード">
        <input type="submit" name="editSub" value="編集">
    </form>
    <br>
    <?php
    //書き出し
    $sql = 'SELECT * FROM post_table';
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll();
    foreach ($results as $row){
        echo $row['id'].' : ';
        echo '<strong>'.$row['name'].'</strong> : ';
        echo date("Y/m/d H:i:s", strtotime($row['date'])).'<br>';
        echo '&emsp;&emsp;&emsp;'.$row['comment'].'<br>';
    echo "<hr>";
    }
    ?>
    </body>
</html>