const int LED = 7;
bool LEDOn = false;

//Task 4 sending the potentiometer data to raspberry pi using interrupt
void setup() 
{
  Serial.begin(9600);
  cli();                      //stop interrupts for till we make the settings
  /*1. First we reset the control register to make sure we start with everything disabled.*/
  TCCR1A = 0;                 // Reset entire TCCR1A to 0 
  TCCR1B = 0;                 // Reset entire TCCR1B to 0
 
  /*2. We set the prescalar to the desired value by changing the CS10 CS12 and CS12 bits. */  
  TCCR1B |= B00000100;        //Set CS12 to 1 so we get prescalar 256  
  
  /*3. We enable compare match mode on register A*/
  TIMSK1 |= B00000010;        //Set OCIE1A to 1 so we enable compare match A 
  
  /*4. Set the value of register A to 31250*/
  OCR1A = 65535;             //Finally we set compare register A to this value  
  sei();                     //Enable back the interrupts

  pinMode(LED, OUTPUT);
  LEDOn = false;
}

void loop() {
  // put your main code here, to run repeatedly:
}

//With the settings above, this IRS will trigger each 1000ms.
ISR(TIMER1_COMPA_vect){
  TCNT1  = 0;                  //First, set the timer back to 0 so it resets for next interrupt
  int analogueValue = analogRead(A3); 
  Serial.println(analogueValue);

  if (LEDOn)
  {
    digitalWrite(LED, HIGH);
  }
  else
  {
    digitalWrite(LED, LOW);
  }

  if (Serial.available() > 0) {
    String command = Serial.readStringUntil('\n');
    if (command.indexOf("ON") != -1) {
      LEDOn = true;
    }
    if (command.indexOf("OFF") != -1) {
      LEDOn = false;
    }
    
  }
}