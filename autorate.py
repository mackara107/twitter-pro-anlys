import mysql.connector

con = mysql.connector.connect(
          host='localhost',
          user='km677788',
          password='703747',
          database='karadb',
          auth_plugin='mysql_native_password')
#         doesn't work: auth_plugin='mysql_native_password' )
cursor = con.cursor(buffered=True)

sql = 'Select tweetid from tweets where JSON_extract(tweet,"$.text") like "%twat%"'

cursor.execute(sql)
val=[]
for id in cursor:
	print(id[0])
	insert = "insert into rating (tweetid, rating, raterid) values (%s,%s,%s)"
	val.append((id[0],'Unprofessional',2))
print(val)
cursor.executemany(insert,val)
con.commit()
con.close()
