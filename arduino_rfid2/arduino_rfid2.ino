#include <WiFi.h>
#include <HTTPClient.h>
#include <SPI.h>
#include <MFRC522.h>

// ---------------- RFID Pins ----------------
#define SS_PIN       5
#define RST_PIN      22

// ---------------- Output Pins ----------------
#define BUZZER_PIN   4
#define GREEN_LED    12
#define RED_LED      13

MFRC522 rfid(SS_PIN, RST_PIN);

// ---------------- WiFi Credentials ----------------
const char* ssid = "";
const char* password = "";

// ---------------- PHP API URLs ----------------
// data_upload.php  → logs access, checks authorized_cards, drives buzzer/LED
// receive_card.php → saves UID to current_uid.txt for the Card Manager page
const char* serverName    = "";
const char* managerServer = "";

void setup()
{
    Serial.begin(115200);

    SPI.begin();
    rfid.PCD_Init();

    pinMode(BUZZER_PIN, OUTPUT);
    pinMode(GREEN_LED, OUTPUT);
    pinMode(RED_LED, OUTPUT);

    digitalWrite(BUZZER_PIN, LOW);
    digitalWrite(GREEN_LED, LOW);
    digitalWrite(RED_LED, LOW);

    connectWiFi();

    Serial.println("RFID Attendance System Ready");
}

void loop()
{
    // Reconnect WiFi if disconnected
    if (WiFi.status() != WL_CONNECTED)
    {
        connectWiFi();
    }

    // Wait for RFID card
    if (!rfid.PICC_IsNewCardPresent())
    {
        return;
    }

    // Read RFID card
    if (!rfid.PICC_ReadCardSerial())
    {
        return;
    }

    String uidString = "";

    for (byte i = 0; i < rfid.uid.size; i++)
    {
        if (rfid.uid.uidByte[i] < 0x10)
        {
            uidString += "0";
        }

        uidString += String(rfid.uid.uidByte[i], HEX);

        if (i < rfid.uid.size - 1)
        {
            uidString += " ";
        }
    }

    uidString.toUpperCase();

    Serial.println("--------------------------------");
    Serial.print("Card UID: ");
    Serial.println(uidString);

    sendUIDToServer(uidString);    // → access log + LED/buzzer
    notifyManager(uidString);      // → card manager page

    rfid.PICC_HaltA();
    rfid.PCD_StopCrypto1();

    delay(1000);
}

// ==========================================
// WiFi Connection
// ==========================================
void connectWiFi()
{
    Serial.println();
    Serial.print("Connecting to WiFi");

    WiFi.begin(ssid, password);

    while (WiFi.status() != WL_CONNECTED)
    {
        delay(500);
        Serial.print(".");
    }

    Serial.println();
    Serial.println("WiFi Connected");
    Serial.print("IP Address: ");
    Serial.println(WiFi.localIP());
}

// ==========================================
// Send UID to Server
// ==========================================
void sendUIDToServer(String uid)
{
    if (WiFi.status() != WL_CONNECTED)
    {
        Serial.println("WiFi Disconnected");
        return;
    }

    HTTPClient http;

    http.begin(serverName);

    http.addHeader(
        "Content-Type",
        "application/x-www-form-urlencoded"
    );

    String postData = "uid=" + uid;

    Serial.println("Sending UID to Server...");
    Serial.println(postData);

    int httpResponseCode = http.POST(postData);

    if (httpResponseCode > 0)
    {
        String response = http.getString();

        response.trim();

        Serial.print("Server Response: ");
        Serial.println(response);

        if (response == "ACCESS_GRANTED")
        {
            accessGranted();
        }
        else
        {
            accessDenied();
        }
    }
    else
    {
        Serial.print("HTTP Error: ");
        Serial.println(httpResponseCode);

        accessDenied();
    }

    http.end();
}

// ==========================================
// Access Granted
// ==========================================
void accessGranted()
{
    Serial.println("ACCESS GRANTED");

    digitalWrite(GREEN_LED, HIGH);
    digitalWrite(BUZZER_PIN, HIGH);

    delay(500);

    digitalWrite(BUZZER_PIN, LOW);
    digitalWrite(GREEN_LED, LOW);
}

// ==========================================
// Access Denied
// ==========================================
void accessDenied()
{
    Serial.println("ACCESS DENIED");

    digitalWrite(RED_LED, HIGH);

    for (int i = 0; i < 10; i++)
    {
        digitalWrite(BUZZER_PIN, HIGH);
        delay(100);

        digitalWrite(BUZZER_PIN, LOW);
        delay(100);
    }

    digitalWrite(RED_LED, LOW);
}

// ==========================================
// Notify Card Manager Page (receive_card.php)
// Sends UID via GET so manage_cards.php can
// display it for registration.
// ==========================================
void notifyManager(String uid)
{
    if (WiFi.status() != WL_CONNECTED)
    {
        return;
    }

    HTTPClient http;

    // URL-encode spaces in UID (spaces become %20)
    String encodedUID = uid;
    encodedUID.replace(" ", "%20");

    String url = String(managerServer) + "?uid=" + encodedUID;

    http.begin(url);

    int httpResponseCode = http.GET();

    if (httpResponseCode > 0)
    {
        Serial.print("Manager notified: ");
        Serial.println(http.getString());
    }
    else
    {
        Serial.print("Manager notify error: ");
        Serial.println(httpResponseCode);
    }

    http.end();
}