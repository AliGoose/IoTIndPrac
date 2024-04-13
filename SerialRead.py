import serial

threshold = 500

if __name__ == '__main__':
    ser = serial.Serial('/dev/ttyS0',9600, timeout=1)
    ser.reset_input_buffer()
    value = 0
    while True:
        if ser.in_waiting > 0:
            line = ser.readline().decode('utf-8')
            value = int(line)
            print(value)
            if value > threshold:
                ser.write("ON\n".encode())
            else:
                ser.write("OFF\n".encode())
