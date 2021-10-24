//0.1 add reset function to esp
//0.2 change SW servo PWM to HW servo PWM


#include <DS3231.h>
#include <Wire.h>
#include <SoftwareSerial.h>

SoftwareSerial espserial(11, 12); // RX, TX
DS3231  rtc(SDA, SCL);
Time  t;
#define buzzer 4
int Hor;
int Min;
int Sec;

#define IntervalReset     (5)    // 5*3m = 15 menit
#define reqCek_Wifi       (2)
#define reqCek_Esp        (1)
#define timeOut_serial    (30)    // 30s

boolean flag_request = false;
int timeout_serial = 0;
unsigned char countRequest = 0;


//int MotorPin = 10;

int JamPakanPagi = 6;
int MenitPakanPagi = 0;

int JamPakanSiang = 12;
int MenitPakanSiang = 00;

int JamPakanSore = 17;
int MenitPakanSore = 00;

int pakan_sensor = A0;
int limit_pakan_sensor = 700;
//(jika nilai sensor pakan lebih dari 700 berarti pakan habis/macet)

//millis
unsigned long previousMillis = 0;        // will store last time LED was updated
const long interval = 1000;           // interval at which to blink (milliseconds)
int detik_cekpakan = 0;
int detik_pakan = 0;
int detik_test = 0;
int cekesp_counter = 0;
int cekespwifi_counter = 0;

unsigned int timeUpload_data = 0;

int reset_pin = 2;

#define pinServo    (9)

void setup()
{
  pinMode(buzzer, OUTPUT);
  //    analogWrite(Pin, LOW);
  delay(100);
  Wire.begin();
  rtc.begin();
  Serial.begin(9600);
  delay(100);
  Serial.println("SISTEM PAKAN IKAN OTOMATIS");
  //   The following lines can be uncommented to set the date and time
  //  rtc.setDOW(WEDNESDAY);     // Set Day-of-Week to SUNDAY
  //       rtc.setTime(16, 11, 00);     // Set the time to 12:00:00 (24hr format)
  //      rtc.setDate(10, 10, 2021);   // Set the date to January 1st, 2014
  delay(1000);
  rtc_time();
  Serial.print("WAKTU PEMBERIAN PAKAN PAGI  : ");
  Serial.print(JamPakanPagi);
  Serial.print(":");
  Serial.println(MenitPakanPagi);
  Serial.print("WAKTU PEMBERIAN PAKAN SIANG : ");
  Serial.print(JamPakanSiang);
  Serial.print(":");
  Serial.println(MenitPakanSiang);
  Serial.print("WAKTU PEMBERIAN PAKAN SORE  : ");
  Serial.print(JamPakanSore);
  Serial.print(":");
  Serial.println(MenitPakanSore);
  //
  //  delay(1000);
  espserial.begin(9600);
  delay(100);
  servo_init();
  delay(100);
  servoWrite(0);
  pinMode(reset_pin, OUTPUT);
  digitalWrite(reset_pin, HIGH);

}

void loop()
{
  if (espserial.available())
  {
    Serial.println();
    String incomingString = espserial.readString();
    String esp_replay = incomingString;
    esp_replay.trim();
    if (esp_replay == "ESP_OK")
    {
      flag_request = false;
      cekesp_counter = 0;
    }
    if (esp_replay == "WIFI_OK")
    {
      flag_request = false;
      cekespwifi_counter = 0;
    }
    Serial.print("ARDUINO received: ");
    Serial.println(incomingString);
  }

  if (millis() - previousMillis >= interval)
  {
    Serial.print(".");
    previousMillis = millis();

    if (++detik_cekpakan >= 3600) {
      detik_test = 0;
      if (cek_pakan()) {
        send_esp("cek", "masih");
      } else {
        send_esp("cek", "habis");
      }
      detik_cekpakan = 0;
    }
    
    if (++detik_pakan >= 60) {
      rtc_time();
      if ( Hor == JamPakanPagi &&  Min == MenitPakanPagi) {
        detik_test = 0;
        Serial.println("WAKTU PEMBERIAN PAKAN PAGI");
        Beri_pakan();
        if (cek_pakan()) {
          send_esp("pagi", "masih");
        } else {
          send_esp("pagi", "habis");
        }
      } else if ( Hor == JamPakanSiang &&  Min == MenitPakanSiang) {
        detik_test = 0;
        Serial.println("WAKTU PEMBERIAN PAKAN SIANG");
        Beri_pakan();
        if (cek_pakan()) {
          send_esp("siang", "masih");
        } else {
          send_esp("siang", "habis");
        }
      } else if ( Hor == JamPakanSore &&  Min == MenitPakanSore) {
        detik_test = 0;
        Serial.println("WAKTU PEMBERIAN PAKAN SORE");
        Beri_pakan();
        if (cek_pakan()) {
          send_esp("sore", "masih");
        } else {
          send_esp("sore", "habis");
        }
      } else {
      }
      detik_pakan = 0;
    }



    if ( ( Hor == JamPakanPagi &&  Min == MenitPakanPagi) || ( Hor == JamPakanSiang &&  Min == MenitPakanSiang) || ( Hor == JamPakanSore &&  Min == MenitPakanSore) ){
      detik_test = 0;
    }else {
     switch (detik_test)
      {
        case 60 : get_cekesp(); timeout_serial = 0; flag_request = true; countRequest = reqCek_Esp; break;
        case 120 : get_cekwifi(); detik_test = 0; timeout_serial = 0; flag_request = true; countRequest = reqCek_Wifi; break;
      } 
    }

    if (flag_request)
    {
      if (++timeout_serial >= timeOut_serial)
      {
        flag_request = false;
        timeout_serial = 0;

        if (countRequest == reqCek_Esp)
        {
          Serial.println();
          Serial.print("ESP PROBLEM 0");
          cekesp_counter++;
          Serial.println(cekesp_counter);
        }
        else if (countRequest == reqCek_Wifi)
        {
          Serial.println();
          Serial.print("WIFI_PROBLEM 0");
          cekespwifi_counter++;
          Serial.println(cekespwifi_counter);
        }

        if (cekespwifi_counter >= IntervalReset || cekesp_counter >= IntervalReset)
        {
          Serial.println();
          if (cekespwifi_counter >= IntervalReset) Serial.print("WIFI MATI, RESET ESP ");
          if (cekesp_counter >= IntervalReset) Serial.print("ESP HANG, RESET ESP ");

          digitalWrite(reset_pin, LOW);
          delay(100);
          digitalWrite(reset_pin, HIGH);
          cekespwifi_counter = 0;
          cekesp_counter = 0;
        }
      }
    }
    ++detik_test;
  }
}

//FUNGSI PWM SERVO HARDWARE
void servo_init(void)
{
  // Timer/Counter 1 initialization
  // Clock source: System Clock
  // Clock value: 15.625 kHz
  // Mode: Fast PWM top=0x00FF
  // OC1A output: Non-Inverted PWM
  // OC1B output: Disconnected
  // Noise Canceler: Off
  // Input Capture on Falling Edge
  // Timer Period: 16.384 ms
  // Output Pulse(s):
  // OC1A Period: 16.384 ms Width: 0 us
  // Timer1 Overflow Interrupt: Off
  // Input Capture Interrupt: Off
  // Compare A Match Interrupt: Off
  // Compare B Match Interrupt: Off
  TCCR1A = (1 << COM1A1) | (0 << COM1A0) | (0 << COM1B1) | (0 << COM1B0) | (0 << WGM11) | (1 << WGM10);
  TCCR1B = (0 << ICNC1) | (0 << ICES1) | (0 << WGM13) | (1 << WGM12) | (1 << CS12) | (0 << CS11) | (1 << CS10);
  TCNT1H = 0x63;
  TCNT1L = 0xC0;
  ICR1H = 0x00;
  ICR1L = 0x00;
  OCR1AH = 0x00;
  OCR1AL = 0x00;
  OCR1BH = 0x00;
  OCR1BL = 0x00;
  TIMSK1 = (0 << ICIE1) | (0 << OCIE1B) | (0 << OCIE1A) | (0 << TOIE1);

  pinMode(pinServo, OUTPUT);
}

void servoWrite(uint8_t position)
{
  if (position > 180)position = 180;
  uint8_t derajat = map(position, 0, 180, 16, 32);
  OCR1A = derajat;
}


void get_cekwifi()
{
  Serial.println();
  Serial.println("CEK_WIFI");
  espserial.println("CEK_WIFI");
}

void get_cekesp()
{
  Serial.println();
  Serial.println("CEK_ESP");
  espserial.println("CEK_ESP");
}

void send_esp(String pakan_ikan, String cek_pakan)
{
  Serial.println();
  Serial.println("KIRIM DATA");
  String data = "&pakan_ikan=" + pakan_ikan + "&cek_pakan=" + cek_pakan;
  Serial.println(data);
  espserial.println(data);

  //  StaticJsonDocument<200> doc;
  //  doc["pakan_ikan"] = pakan_ikan;
  //  doc["cek_pakan"] = cek_pakan;
  //  serializeJson(doc, Serial);
  //  Serial.println();
  //  serializeJson(doc, espserial);
}

bool cek_pakan()
{
  int sensor = analogRead(pakan_sensor);
  Serial.println();
  Serial.print("Cek Pakan = ");
  Serial.print(sensor);
  if (sensor >= limit_pakan_sensor) {
    Serial.println(" PAKAN MASIH");
    return true;
  } else {
    Serial.println(" PAKAN HABIS");
    return false;
  }
}

void rtc_time()
{
  t = rtc.getTime();
  Hor = t.hour;
  Min = t.min;
  Sec = t.sec;
  Serial.println();
  Serial.print(Hor);
  Serial.print(":");
  Serial.print(Min);
  Serial.print(":");
  Serial.println(Sec);
  Serial.println();
}

void Beri_pakan()
{
  if (cek_pakan) {

  } else {
    buzz_pakan();
  }
  buzz();
  delay(500);
  buzz();
  delay(700);
  buzz();
  //  analogWrite(MotorPin, 75);
  servoWrite(0);
  servoWrite(100);
  delay(600);
  servoWrite(0);
  buzz();
  delay(500);
  buzz();
  //  analogWrite(MotorPin, LOW);
}

void buzz_pakan() {
  digitalWrite(buzzer, HIGH);
  delay(300);
  digitalWrite(buzzer, LOW);
  delay(50);
  digitalWrite(buzzer, HIGH);
  delay(100);
  digitalWrite(buzzer, LOW);
  delay(50);
  digitalWrite(buzzer, HIGH);
  delay(100);
  digitalWrite(buzzer, LOW);
  delay(50);
  digitalWrite(buzzer, HIGH);
  delay(300);
  digitalWrite(buzzer, LOW);
}
//
//
void buzz() {
  digitalWrite(buzzer, HIGH);
  delay(50);
  digitalWrite(buzzer, LOW);
  delay(50);
  digitalWrite(buzzer, HIGH);
  delay(50);
  digitalWrite(buzzer, LOW);
  delay(50);
  digitalWrite(buzzer, HIGH);
  delay(200);
  digitalWrite(buzzer, LOW);
  delay(50);
  digitalWrite(buzzer, HIGH);
  delay(200);
  digitalWrite(buzzer, LOW);
}
