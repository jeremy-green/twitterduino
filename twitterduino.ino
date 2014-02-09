//twitterduino.ino
#include <LiquidCrystal.h>
LiquidCrystal lcd(12, 11, 5, 4, 3, 2);

const int contrastPin = 10;
const int contrast = 20;

char character;

void setup() {
  lcd.begin(16, 2);
  analogWrite(contrastPin, contrast);
  Serial.begin(9600);
}

void loop() {

  if (Serial.available() > 0) {
    lcd.clear();
    lcd.setCursor(0, 0);

    delay(100);
    int i = 0;

    while (Serial.available() > 0) {
      character = Serial.read();
      //Serial.println(character);
      i++;
      lcd.write(character);
      if (i >= 16) {
        lcd.setCursor(i - 16, 1);
      }
    }
  }
}
