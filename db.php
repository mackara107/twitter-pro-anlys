<html>
<head>
<h1>Check out the Tweets in the Database.</h1>
<h4>Click Unprofessional or Not Unprofessional to show the Tweets</h4>
</head>

<body>
<?php
try{
	$conn = new PDO("mysql:host=localhost;dbname=karadb","km677788","703747");
        echo("<!-- connected --> \n");
}catch (PDOException $e){
        die("connection failed: ".$e->getMessage());
}

echo('<form method="post" action="">');
echo('<input type="submit" id="un" name="un" value="Unprofessional">');
echo('<input type="submit" id="pro" name="pro" value="Not Unprofessional">');
echo('<input type="submit" id="clear" name="clear" value="Clear">');
echo('<br><br>');

if(isset($_POST['un'])){
	$sql = "SELECT tweets.tweet FROM tweets INNER JOIN rating ON tweets.tweetid = rating.tweetid WHERE rating.rating = 'Unprofessional'";
        $statement = $conn->query($sql);

        $results = $statement->fetchAll();

        foreach ($results as $row) {
                $json = $row['tweet'];
                $ARRAY = json_decode($json,true);
                $text = $ARRAY["text"];
		$finalt = "";

        	$i = 0;
        	while ($i < strlen($text)){
                	if ($text[$i] == "~" and $text[$i+1] == "u"){
                        	$i+=5;
			}else if($text[$i] == "~" and $text[$i+1] == "n"){
                        	$i+=1;
                	}else{
                        	$finalt=$finalt.$text[$i];
                	}
			$i+=1;
		}

		echo($finalt);

		echo("<br>");
	}
}
if(isset($_POST['pro'])){
	$sql = "SELECT tweets.tweet FROM tweets INNER JOIN rating ON tweets.tweetid = rating.tweetid WHERE rating.rating = 'Professional'";
        $statement = $conn->query($sql);

        $results = $statement->fetchAll();

        foreach ($results as $row) {
                $json = $row['tweet'];
                $ARRAY = json_decode($json,true);
                $text = $ARRAY["text"];
                $finalt = "";

                $i = 0;
                while ($i < strlen($text)){
                        if ($text[$i] == "~" and $text[$i+1] == "u"){
                                $i+=5;
                        }else if($text[$i] == "~" and $text[$i+1] == "n"){
                                $i+=1;
                        }else{
                                $finalt=$finalt.$text[$i];
                        }
                        $i+=1;
                }

                echo($finalt);

                echo("<br>");
        }


}
if(isset($_POST['clear'])){
}
?>




</form>
</body>

</html>
