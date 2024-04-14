import serial
import time

threshold_file = 'threshold.txt'  # File where the threshold value is stored

def read_threshold():
    try:
        with open(threshold_file, 'r') as file:
            return int(file.read().strip())
    except FileNotFoundError:
        return 500  # Return a default value if the file does not exist

if __name__ == '__main__':
    ser = serial.Serial('/dev/ttyS0', 9600, timeout=1)
    ser.reset_input_buffer()

    while True:
        threshold = read_threshold()  # Read the threshold value from the file
        if ser.in_waiting > 0:
            line = ser.readline().decode('utf-8').strip()
            value = int(line)
            print(f"Sensor Value: {value}, Threshold: {threshold}")
            if value > threshold:
                ser.write("ON\n".encode())
            else:
                ser.write("OFF\n".encode())
        time.sleep(1)  # Delay to reduce CPU usage
