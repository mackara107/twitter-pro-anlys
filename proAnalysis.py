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


#-----methods
def remove_noise(tweet_tokens, stop_words):

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
          password='288462',
          database='karadb' )
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
print("\n",len(un),"Unprofessional Tweets Loaded")

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
print(len(pro),"Professional Tweets Loaded")

print ("\nUncleaned Tweets")
print(pro[2])
print(un[2])

#-----normalizing data inside the list (lemmatizing, removing noise, removing stop words
stop_words = stopwords.words('english')

pro_tokens = []
unpro_tokens = []

for tokens in pro:
	pro_tokens.append(remove_noise(tokens,stop_words))

for tokens in un:
	unpro_tokens.append(remove_noise(tokens,stop_words))

print("\nCleaned Tweets")
print(pro_tokens[2])
print(unpro_tokens[2])

#----creating dict for dataset
pro_tokens = get_tweets_for_model(pro_tokens)
unpro_tokens = get_tweets_for_model(unpro_tokens)

pro_dataset = [(tweet_dict, "Professional") for tweet_dict in pro_tokens]
unpro_dataset = [(tweet_dict, "Unprofessional") for tweet_dict in unpro_tokens]

dataset = pro_dataset + unpro_dataset

random.shuffle(dataset)

seventy = int(len(dataset) * 0.70)
train_data = dataset[:seventy] #70%
test_data = dataset[seventy:]  #30%

#-----model
classifier = NaiveBayesClassifier.train(train_data)

print("\nAccuracy is:", classify.accuracy(classifier, test_data))

print(classifier.show_most_informative_features(10))

#----closing----
cursor.close()
con.close()
