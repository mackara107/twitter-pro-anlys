<html>
<head>
<title>Kara's Tweets</title>
<h2>Help Kara Rate her Tweets (Please)!</h2>
</head>
<body>
<?php

if (!isset( $_SERVER["HTTP_HOST"] ) && isset( $argv[1] )) {
    parse_str( $argv[1], $_GET  );
    parse_str( $argv[1], $_POST );
}
?>

<form method="post" action="">

<?php
	try{
		$conn = new PDO("mysql:host=localhost;dbname=karadb","****","****");
		echo("<!-- connected --> \n");
	}catch (PDOException $e){
		die("connection failed: ".$e->getMessage());
	}


	function getRating($rating, $num){
                global $conn;
		global $nameid;

                $tid = $_POST["tid".$num];
                echo ($tid.": ");
                switch($rating){
                        case '1': echo "N/A"; break;
                        case '2': echo "Professional";
                                $sql = "insert into rating (tweetid, rating, raterid) values (".$tid.", 'Professional', ".$nameid.")";
                                try{
                                        $conn->exec( $sql );
                                }catch(PDOException $ex){
                                        echo $ex->getMessage();
                                }
                        break;
                        case '3': echo "Unprofessional";
                                $sql = "insert into rating (tweetid, rating) values (".$tid.", 'Unprofessional', ".$nameid.")";
                                try{
                                        $conn->exec($sql);
                                }catch(PDOException $ex){
                                        echo $ex->getMessage();
                                }
                        break;

                }
        }

	if (isset($_POST["reload"])){
		header("Location: http://grevera.ddns.net/~km677788/tweets.php");
		$reload = true;	
	}

	if (isset($_POST["submit"])){

		if(!$reload){
			$name = $_POST["name"];
			$sql = "insert into rater (name) values ('".$name."')";
			try{
				$conn->exec($sql);
                	}catch(PDOException $ex){
                        	echo $ex->getMessage();
                	}

			$sql = "select max(raterid) from rater";
			$statement = $conn->query($sql);
        		$results = $statement->fetchAll();

        		$nameid;
        		foreach ($results as $row) {
                		$nameid = $row[0];
        		}
		}

		echo("<h3>Thanks for Submitting the Below Information ".$name."!</h3>");
	
		$rating1 = $_POST["rating1"];
		$rating2 = $_POST["rating2"];
		$rating3 = $_POST["rating3"];
		$rating4 = $_POST["rating4"];
		$rating5 = $_POST["rating5"];
		$rating6 = $_POST["rating6"];
		$rating7 = $_POST["rating7"];
		$rating8 = $_POST["rating8"];
		$rating9 = $_POST["rating9"];
		$rating10 = $_POST["rating10"];

	
                getRating($rating1, 1);    echo("<br><br>");
		
                getRating($rating2, 2);    echo("<br><br>");

                getRating($rating3, 3);    echo("<br><br>");

                getRating($rating4, 4);    echo("<br><br>");

                getRating($rating5, 5);    echo("<br><br>");

                getRating($rating6, 6);    echo("<br><br>");

                getRating($rating7, 7);    echo("<br><br>");

                getRating($rating8, 8);    echo("<br><br>");

                getRating($rating9, 9);    echo("<br><br>");

                getRating($rating10, 10);  echo("<br><br>");


		echo "<h3>Want to Rate more???</h4>";
		echo "<form method='post' action=''>";
		echo "<input type = 'submit' name = 'reload' value = 'LOADMORE'>";		
	}

	else{
	$sql = "select count(*) from rating;";
        $statement = $conn->query($sql);
        $results = $statement->fetchAll();

        $totalratings;
        foreach ($results as $row) {
                $totalratings = $row[0];
        }

        $sql = "select count(*) from tweets;";
        $statement = $conn->query($sql);
        $results = $statement->fetchAll();

        $totaltweets;
        foreach ($results as $row) {
                $totaltweets = $row[0];
        }

	$sql = "select count(distinct tweetid) from rating;";
        $statement = $conn->query($sql);
        $results = $statement->fetchAll();

        $distinctratings;
        foreach ($results as $row) {
                $distinctratings = $row[0];
        }

        $totalleft = $totaltweets - $distinctratings;
        echo("<h4>".$totalratings." ratings so far... (".$totaltweets." Total Tweets) ".$totalleft." Tweets still need ratings.</h4>");


	echo "<h4>Help me rate these tweets for my research project!".
		" Below are 10 random tweets, please give an unbiased opinion ".
		"as to whether each tweet is Professional or Unprofessional. ".
		"<br><br>Submitting is final! If you are not sure the rating, leave N/A".
		" and answer won't be submitted.</h4>";

	if(!$reload){
		echo "<h4>Name: <input type='text' id='name' name='name'></h4>";
	}else{
		echo "<h4>Thanks for rating more, ".$name."!</h4>";
	}

	$sql = "select * from  tweets order by rand() limit 10";
	$statement = $conn->query($sql);
	
	$results = $statement->fetchAll();
	//echo json_decode('"\u1000"');	

	$x = 1;
	foreach ($results as $row) {
   		//echo($row['tweetid']);
		//echo("<br>");

		$json = $row['tweet'];
		$ARRAY = json_decode($json,true);
		$text = $ARRAY["text"];

		$offset = 0;
		$allpos = array();
		while (($pos = strpos($text, "~u", $offset)) !==FALSE){
			$offset = $pos + 1;
			$allpos[] = $pos;
		}

		for ($i = 0; $i < strlen($text); $i++){
			if( in_array($i, $allpos)){
				$unicode = "u";
				$unicode .= $text[$i + 2];
				$unicode .= $text[$i + 3];
				$unicode .= $text[$i + 4];
				$unicode .= $text[$i + 5];
				$unicode .= "";
				$unicode = "\\".$unicode;
				echo json_decode("'".$unicode."'");

				$i += 5;
			}else if($text[$i]=="~" && $text[$i+1]=="n"){
				$i++;
			}
			else{
				echo ($text[$i]);
			}
		}

		?>
		<table>
		<tr><td>

		<?php
	switch((string)$x){
	case "1":
		printf('<input id="tid1" name="tid1" type="hidden" value="%s">', $row['tweetid']);

		 ?>

		<select name="rating1">
                        <option value="1">N/A</option>
                        <option value="2">Professional</option>
                        <option value="3">Unprofessional</option>
                </select>
		<?php break;

	case "2": 
		printf('<input id="tid2" name="tid2" type="hidden" value="%s">', $row['tweetid']);
		?>
		<select name="rating2">
                        <option value="1">N/A</option>
                        <option value="2">Professional</option>
                        <option value="3">Unprofessional</option>
                </select>
		<?php break;

	case "3": 
		printf('<input id="tid3" name="tid3" type="hidden" value="%s">', $row['tweetid']);
		?>
		<select name="rating3">
                        <option value="1">N/A</option>
                        <option value="2">Professional</option>
                        <option value="3">Unprofessional</option>
                </select>
		<?php break;

	case "4": 
		printf('<input id="tid4" name="tid4" type="hidden" value="%s">', $row['tweetid']);
		?>
		<select name="rating4">
                        <option value="1">N/A</option>
                        <option value="2">Professional</option>
                        <option value="3">Unprofessional</option>
                </select>
		<?php break;

	case "5": 
		printf('<input id="tid5" name="tid5" type="hidden" value="%s">', $row['tweetid']);
		?>
		<select name="rating5">
                        <option value="1">N/A</option>
                        <option value="2">Professional</option>
                        <option value="3">Unprofessional</option>
                </select>
		<?php break;

	case "6": 
		printf('<input id="tid6" name="tid6" type="hidden" value="%s">', $row['tweetid']);
		?>
		<select name="rating6">
                        <option value="1">N/A</option>
                        <option value="2">Professional</option>
                        <option value="3">Unprofessional</option>
                </select>
		<?php break;

	case "7": 
		printf('<input id="tid7" name="tid7" type="hidden" value="%s">', $row['tweetid']);
		?>
		<select name="rating7">
                        <option value="1">N/A</option>
                        <option value="2">Professional</option>
                        <option value="3">Unprofessional</option>
                </select>
		<?php break;

	case "8": 
		printf('<input id="tid8" name="tid8" type="hidden" value="%s">', $row['tweetid']);
		?>
		<select name="rating8">
                        <option value="1">N/A</option>
                        <option value="2">Professional</option>
                        <option value="3">Unprofessional</option>
                </select>
		<?php break;

	case "9": 
		printf('<input id="tid9" name="tid9" type="hidden" value="%s">', $row['tweetid']);
		?>
		<select name="rating9">
                        <option value="1">N/A</option>
                        <option value="2">Professional</option>
                        <option value="3">Unprofessional</option>
                </select>
		<?php break;

	case "10": 
		printf('<input id="tid10" name="tid10" type="hidden" value="%s">', $row['tweetid']);
		?>
		<select name="rating10">
                        <option value="1">N/A</option>
                        <option value="2">Professional</option>
                        <option value="3">Unprofessional</option>
                </select>
		<?php break;


} ?>


		</td></tr></table>
		<?php
		$x++;
		echo("<br><br>");
	}


?>

<input type = "submit" name="submit" value="submit" />
<?php } ?>
</form>

</body>
</html>
