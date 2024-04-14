import serial
import mariadb
import sys

# Configure the serial connection
ser = serial.Serial('/dev/ttyS0', 9600)

# Try to connect to the database
try:
    conn = mariadb.connect(user="root", password="password123", host="127.0.0.1", port=3306, database="iot_db")
except mariadb.Error as e:
    print(f"There is an issue connecting to the database: {e}")
    sys.exit(1)

# Create a cursor object
cur = conn.cursor()

while True:
    # Read and decode the serial line
    line = ser.readline().decode('utf-8').strip()
    print(line)
    
    # Parse the temperature and light level from the line
    try:
        temp_part, light_part = line.split(" - Light level: ")
        temperature = float(temp_part.split(": ")[1].replace(" C", ""))
        light_level = int(light_part)
        
        # Insert the parsed data into the database
        cur.execute("INSERT INTO SensorData (temperature, light_level) VALUES (?, ?)", (temperature, light_level))
        conn.commit()
    except ValueError as e:
        print(f"Error parsing or inserting data: {e}")
