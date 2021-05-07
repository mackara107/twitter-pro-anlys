#!/usr/bin/env python
import sys
import mysql.connector
import json
import nltk
from nltk.tag import pos_tag
from nltk.stem.wordnet import WordNetLemmatizer
import re, string
from nltk.corpus import stopwords
from nltk import FreqDist
import random
from nltk import classify
from nltk import NaiveBayesClassifier
from nltk import word_tokenize


#-----methods
def remove_noise(tweet_tokens, stop_words=()):

    cleaned_tokens = []

    for token, tag in pos_tag(tweet_tokens):
        token = re.sub('http[s]?://(?:[a-zA-Z]|[0-9]|[$-_@.&+#]|[!*\(\),]|'\
                       '(?:%[0-9a-fA-F][0-9a-fA-F]))+','', token)
        token = re.sub("(@[A-Za-z0-9_]+)","", token)
        if tag.startswith("NN"):
            pos = 'n'
        elif tag.startswith('VB'):
            pos = 'v'
        else:
            pos = 'a'

        lemmatizer = WordNetLemmatizer()
        token = lemmatizer.lemmatize(token, pos)

        if len(token) > 0 and token not in string.punctuation and token.lower() not in stop_words and not token=="http" and not token=="https"  and not(token.startswith('//')):
            cleaned_tokens.append(token.lower())
    return cleaned_tokens

def get_all_words(cleaned_tokens_list):
    for tokens in cleaned_tokens_list:
        for token in tokens:
            yield token

def get_tweets_for_model(cleaned_tokens_list):
    for tweet_tokens in cleaned_tokens_list:
        yield dict([token, True] for token in tweet_tokens)

#-----mySQL queries
con = mysql.connector.connect(
          host='localhost',
          user='km677788',
          password='703747',
          database='karadb',
	  auth_plugin='mysql_native_password')
#         doesn't work: auth_plugin='mysql_native_password' )
cursor = con.cursor()

sqlu = 'SELECT tweets.tweet FROM tweets INNER JOIN rating ON tweets.tweetid = rating.tweetid WHERE rating.rating = "Unprofessional"'
sqlp = 'SELECT tweets.tweet FROM tweets INNER JOIN rating ON tweets.tweetid = rating.tweetid WHERE rating.rating = "Professional"'

pro = []
un = []

#----putting unprofessional tweets into un list
cursor.execute(sqlu)
for tweet in cursor:
	textu = json.loads(tweet[0])
	textu = textu["text"]
	text = ""

	i = 0
	while i < len(textu):
		if textu[i] == "~" and textu[i+1] == "u":
			i+=5
		elif textu[i] == "~" and textu[i+1] == "n":
			i+=1
		else:
			text+=textu[i]
		i+=1
	un.append(nltk.word_tokenize(text)) #tokenized text
#print("\n",len(un),"Unprofessional Tweets Loaded")

#----putting professional tweets into pro list
cursor.execute(sqlp)
for tweet in cursor:
        textp = json.loads(tweet[0])
        textp = textp["text"]
        text = ""

        i = 0
        while i < len(textp):
                if textp[i] == "~" and textp[i+1] == "u":
                        i+=5
                elif textp[i] == "~" and textp[i+1] == "n":
                        i+=1
                else:
                        text+=textp[i]
                i+=1
        pro.append(nltk.word_tokenize(text)) #tokenized text
#print(len(pro),"Professional Tweets Loaded")

#print ("\nUncleaned Tweets")
#print(pro[2])
#print(un[2])

#-----normalizing data inside the list (lemmatizing, removing noise, removing stop words
stop_words = stopwords.words('english')

pro_tokens = []
unpro_tokens = []

for tokens in pro:
	pro_tokens.append(remove_noise(tokens,stop_words))

for tokens in un:
	unpro_tokens.append(remove_noise(tokens,stop_words))

#print("\nCleaned Tweets")
#print(pro_tokens[2])
#print(unpro_tokens[2])

#----creating dict for dataset
pro_tokens = get_tweets_for_model(pro_tokens)
unpro_tokens = get_tweets_for_model(unpro_tokens)

pro_dataset = [(tweet_dict, "Professional") for tweet_dict in pro_tokens]
unpro_dataset = [(tweet_dict, "Unprofessional") for tweet_dict in unpro_tokens]

#print(pro_dataset[0])
dataset = pro_dataset + unpro_dataset

random.shuffle(dataset)

seventy = int(len(dataset) * 0.70)
train_data = dataset[:seventy] #70%
test_data = dataset[seventy:]  #30%

#-----model
classifier = NaiveBayesClassifier.train(train_data)

accuracy = classify.accuracy(classifier, test_data)
#print("\nAccuracy is:", classify.accuracy(classifier, test_data))

#print(classifier.show_most_informative_features(10))

#-----testing on custom tweet
custom = sys.argv

custom_tweet = ""

for i in range(len(custom)):
	if not i==0:
		custom_tweet = custom_tweet +" "+custom[i]

custom_tokens = remove_noise(word_tokenize(custom_tweet))

#print(custom_tweet, classifier.classify(dict([token, True] for token in custom_tokens)))
answer = classifier.classify(dict([token, True] for token in custom_tokens))
if(answer=="Professional"):
	print('<h3 style="color: green;">Not Unprofessional</h3>')
else:
	print('<h3 style="color: red;">Unprofessional</h3>')

#print("<h3>",classifier.classify(dict([token, True] for token in custom_tokens)),"</h3>")
print("<br><br>Our Accuracy is currently:",accuracy,"<br><br><br>")

true_prof = 0;
false_prof = 0;
true_unprof = 0;
false_unprof = 0;
for i in test_data:
	predicted = classifier.classify(i[0])
	real = i[1]	
	if(predicted==real and real=="Professional"):
		true_prof = true_prof + 1
	if(predicted==real and real=="Unprofessional"):
		true_unprof = true_unprof + 1
	if(predicted == "Unprofessional" and real =="Professional"):
		false_unprof = false_unprof + 1
	if(predicted == "Professional" and real=="Unprofessional"):
		false_prof = false_prof + 1
#print("True Professional",true_prof)
#print("False Professional", false_prof)
#print("True Unprofessional", true_unprof)
#print("False Unprofessional", false_unprof)
#print(len(test_data))
#----closing----
cursor.close()
con.close()
