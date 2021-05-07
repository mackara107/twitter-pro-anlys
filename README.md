# TwitterProAnlys
A machine learning program that uses Natural Language Processing to analyze if a Tweet is Not Unprofessional or Unprofessional.

# collectTweets.ipynb
A Python program for collecting 1,000 Tweets from the US using Tweepy and outputs a text file of the Tweet's JSON strings.

# correctJSON.py
A Python program that takes an input of the text file of Tweet JSONs and corrects the JSONs to be compatible for inserting into MySQL.

# getsqlfortweets.py
A Python program that takes an input of the corrected text file of Tweet JSONs amd outputs a text file of MySQL insert statements.

# finalsqlconversion.ipynb
A Python program finalizing the SQL. Essentially gets rid of the unicode caused by Emojis so that Tweets can successfully be inserted into MySQL.

# tweets.php
A PHP file (I used on an Apache server) for collecting user Ratings for each Tweet. Displays a random 10 Tweets I collected from the database I created and uses HTML form to load responses into new MySQL table.

# autorate.py
A Python program that takes an input of a word and will rate Tweets Unprofessional if they contain that word at all.

# proAnalysis.py
The Python program that imports the data from my database, cleans it, prepares is, and trains it on a Naive Bayes Classifier. The program also takes input which is used as a custom tweet that will be classified by the model.

# classifyTweet.php
The User Interface (I hosted on an Apache server) that prompts the user for a custom tweet and sends it over to the proAnalysis.py program to be classified. It also links to tweets.php and db.php

# db.php
A page that will show the contents of the database. Shows all Unprofessional and Not Unprofessional tweets when each button is clicked.


