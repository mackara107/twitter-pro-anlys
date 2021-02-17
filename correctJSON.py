#!/usr/bin/python3

import ast
import fileinput
import json
#------------------------------------------------------------------------------
cnt = 0
for line in fileinput.input( files=('tweets 2020-09-25 13_22_24.051466.txt'), mode='rb' ):    #read input one line at a time
    what = "{'created_at':"    #start of a tweet
    cnt += 1
    start = 0
    while True:
        # where = line.find( what, start )
        where = line.find( bytes(what,'utf-8'), start )
        if where == -1:    break    #the end of this line
        if where == 0:    #first one is a special case
            start = where
            what = "}" + what
        else:
            start = where + 1    #to skip the leading }
        #now find the end
        next = line.find( bytes(what,'utf-8'), start+1 )
        if next == -1:    #last one?
            next = line.rfind( bytes('}','utf-8') )
            s = line[ start : next+1 ]
            s = str( s, 'utf=8' )
            s = ast.literal_eval( s )
            s = json.dumps( s )
            print( s )
            json.loads( s )
            break
        #not the last one
        s = line[ start : next+1 ]
        s = str( s, 'utf-8' )
        s = ast.literal_eval( s )
        s = json.dumps( s )
        print( s )
        json.loads( s )
        start += 1
#------------------------------------------------------------------------------

