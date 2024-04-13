#include <OneWire.h>
#include <DallasTemperature.h>

// Data wire is connected to Arduino pin 2
#define ONE_WIRE_BUS 8

// Setup a oneWire instance to communicate with any OneWire devices
OneWire oneWire(ONE_WIRE_BUS);

// Pass our oneWire reference to Dallas Temperature sensor 
DallasTemperature sensors(&oneWire);

int lightSensorPin = A0; // Photoresistor connected to analog pin A0
int greenLED = 2; 
int yellowLED = 3;
int redLED = 4;
int buzzer = 7;

void setup() {
  Serial.begin(9600); // Start serial communication at 9600 bps
  sensors.begin(); // Start up the library for the temperature sensor

  pinMode(greenLED, OUTPUT);
  pinMode(yellowLED, OUTPUT);
  pinMode(redLED, OUTPUT);

  pinMode(buzzer, OUTPUT);
}

void loop() {
  // Reading from the digital temperature sensor
  sensors.requestTemperatures(); // Send the command to get temperatures
  float temperature = sensors.getTempCByIndex(0); // Read temperature in Celsius
  Serial.print("Temperature: ");
  Serial.print(temperature);
  Serial.print(" C  - ");
  
  // Reading from the analog light sensor
  int lightLevel = analogRead(lightSensorPin); // Read the light level (0 to 1023)
  Serial.print("Light level: ");
  Serial.print(lightLevel);
  Serial.print(" - ");

  if (lightLevel >= 0 && lightLevel < 100)
  {
    digitalWrite(greenLED, LOW);
    digitalWrite(yellowLED, LOW);
    digitalWrite(redLED, HIGH);
    digitalWrite(buzzer, HIGH);
  }
  else if (lightLevel >= 100 && lightLevel < 200)
  {
    digitalWrite(greenLED, LOW);
    digitalWrite(yellowLED, HIGH);
    digitalWrite(redLED, LOW);
    digitalWrite(buzzer, LOW);
  }
  else
  {
    digitalWrite(greenLED, HIGH);
    digitalWrite(yellowLED, LOW);
    digitalWrite(redLED, LOW);
    digitalWrite(buzzer, LOW);
  }
  
  delay(10);
}
