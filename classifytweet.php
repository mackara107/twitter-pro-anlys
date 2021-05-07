<html>
<head>
<h2>Check your Tweet's Rating Here!</h2>
<h4>Entering your Tweet below will send it to a Naive Bayes Classifier trained by thousands of hand collected Tweets and their ratings, where it will be determined if Unprofessional or Not.</h4>
</head>

<body>
<form method="post" action="">
<?php
if(isset($_POST['more'])){
	$url = "Location: http://grevera.ddns.net/~km677788/classifytweet.php";
        header($url);
}
if(isset($_POST['submit'])){
	echo("<h2>Your Tweet:</h2>");
	$tweet = $_POST['tweet'];
	echo("<h3>".$tweet."</h3>");
	echo("<br><br><br><h2>It is rated...</h2>");
	system("/usr/bin/python3 proAnalysis.py ".$tweet);

	echo("<input type='submit' id='more' name='more' value='Try Another Tweet'>");

}else{
?>

<h3>Enter Tweet Here<br> <input type='text' id='tweet' name='tweet'></h4>
<input type='submit' id='submit' name='submit' value='Rate This Tweet'>

<?php } 

//$response = system("/usr/bin/python3 proAnalysis.py ".);
echo($response);

?>

</form>
<br><br><br>
<h3><a href="http://grevera.ddns.net/~km677788/tweets.php">Help Improve our Accuracy!</a></h3>
<h3><a href="http://grevera.ddns.net/~km677788/db.php">Check out the Database!</a></h3>

</body>
</html>
