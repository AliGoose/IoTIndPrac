import serial
import pymysql

device = '/dev/ttyS0'

arduino = serial.Serial(device,9600)

while True:
	data = arduino.readline().decode('utf-8').rstrip()
	print(data)
	dbConn = pymysql.connect("localhost","aligoose","","iot_db") or die("could not connect to database")

	print(dbConn)

	with dbConn:
		cursor = dbConn.cursor()
		cursor.execute("INSERT INTO log (temperature) VALUES(%s)"%(data))
		dbConn.commit()
		cursor.close()
