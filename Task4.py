import serial
import mariadb
import sys

ser=serial.Serial('/dev/ttyS0', 9600)
try:
    conn=mariadb.connect(user="root", password="password123", host="127.0.0.1", port=3306, database="test")
except mariadb.Error as e:
    print ("There is an issue connecting to db")
    sys.exit(1)
cur=conn.cursor()
while True:
    line=ser.readline().decode('utf-8').rstrip()
    print(line)
    cur.execute("Insert into Ard values (%s)", (line,))
    conn.commit()
    
