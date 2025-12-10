/*
 * SISTEMA DE RIEGO AUTOMATIZADO CON ESP32
 * =========================================
 *
 * Hardware:
 * - ESP32 WROOM-32
 * - DHT11 (Temp/Humedad ambiental)
 * - Sensor humedad suelo (analógico)
 * - HC-SR04 (Nivel de agua)
 * - Relé HW-307 (Control bomba)
 * - Mini bomba agua (3-6V DC)
 *
 * Conexiones:
 * - GPIO 34: Sensor humedad suelo
 * - GPIO 26: DHT11 DATA
 * - GPIO 5:  HC-SR04 TRIG
 * - GPIO 18: HC-SR04 ECHO
 * - GPIO 25: Relé IN (control bomba)
 */

#include <DHT.h>
#include <WiFi.h>

// ============================================
// CONFIGURACIÓN DE PINES
// ============================================
#define PIN_HUMEDAD_SUELO   34  // ADC1_CH6 (analógico)
#define PIN_DHT11           26  // DHT11 DATA
#define PIN_TRIG            5   // HC-SR04 TRIG
#define PIN_ECHO            18  // HC-SR04 ECHO
#define PIN_RELE            25  // Relé (control bomba)

// ============================================
// CONFIGURACIÓN DE SENSORES
// ============================================
#define DHTTYPE DHT11
DHT dht(PIN_DHT11, DHTTYPE);

// ============================================
// CONFIGURACIÓN DEL SISTEMA DE RIEGO
// ============================================
// Umbrales de humedad del suelo (valores ADC: 0-4095)
#define HUMEDAD_MIN         1500  // Por debajo de este valor → REGAR
#define HUMEDAD_MAX         2500  // Por encima de este valor → DETENER RIEGO

// Umbrales de nivel de agua (cm)
#define NIVEL_AGUA_MIN      5.0   // Nivel mínimo seguro (cm desde sensor)
#define NIVEL_AGUA_CRITICO  2.0   // Nivel crítico - NO REGAR

// Tiempos de seguridad
#define TIEMPO_RIEGO_MAX    30000  // Máximo 30 segundos de riego continuo
#define TIEMPO_ENTRE_RIEGOS 300000 // Mínimo 5 minutos entre riegos (300000 ms)
#define TIMEOUT_RIEGO       60000  // Timeout general de 1 minuto

// ============================================
// CONFIGURACIÓN WIFI (Opcional)
// ============================================
const char* ssid = "TU_RED_WIFI";
const char* password = "TU_PASSWORD";
WiFiServer server(80);

// ============================================
// VARIABLES GLOBALES
// ============================================
// Estado de la bomba
bool bombaEncendida = false;
unsigned long tiempoInicioBomba = 0;
unsigned long tiempoUltimoRiego = 0;

// Lecturas de sensores
float temperaturaAmbiente = 0;
float humedadAmbiente = 0;
int humedadSuelo = 0;
float nivelAgua = 0;

// Control manual (desde WiFi)
bool modoManual = false;
bool comandoManualBomba = false;

// ============================================
// FUNCIONES DE INICIALIZACIÓN
// ============================================
void setup() {
  Serial.begin(115200);
  Serial.println("\n\n========================================");
  Serial.println("SISTEMA DE RIEGO AUTOMATIZADO ESP32");
  Serial.println("========================================\n");

  // Inicializar pines
  inicializarPines();

  // Inicializar sensores
  inicializarSensores();

  // Inicializar WiFi (opcional)
  inicializarWiFi();

  // Estado inicial
  Serial.println("✓ Sistema inicializado correctamente");
  Serial.println("✓ Bomba en estado: APAGADA");
  Serial.println("========================================\n");
}

void inicializarPines() {
  Serial.println("Configurando pines...");

  // Configurar relé (bomba)
  pinMode(PIN_RELE, OUTPUT);
  digitalWrite(PIN_RELE, LOW);  // Bomba apagada al inicio

  // Configurar sensor ultrasónico
  pinMode(PIN_TRIG, OUTPUT);
  pinMode(PIN_ECHO, INPUT);

  // Configurar sensor de humedad suelo (analógico - no requiere pinMode)
  // El pin 34 es ADC1_CH6, configurado automáticamente

  Serial.println("✓ Pines configurados");
}

void inicializarSensores() {
  Serial.println("Inicializando sensores...");

  // Inicializar DHT11
  dht.begin();

  Serial.println("✓ Sensores inicializados");
}

void inicializarWiFi() {
  Serial.println("Conectando a WiFi...");
  WiFi.begin(ssid, password);

  int intentos = 0;
  while (WiFi.status() != WL_CONNECTED && intentos < 20) {
    delay(500);
    Serial.print(".");
    intentos++;
  }

  if (WiFi.status() == WL_CONNECTED) {
    Serial.println("\n✓ WiFi conectado");
    Serial.print("IP: ");
    Serial.println(WiFi.localIP());
    server.begin();
  } else {
    Serial.println("\n✗ WiFi no conectado (continuando sin WiFi)");
  }
}

// ============================================
// LOOP PRINCIPAL
// ============================================
void loop() {
  // Leer sensores
  leerSensores();

  // Procesar comandos WiFi (si está conectado)
  if (WiFi.status() == WL_CONNECTED) {
    procesarComandosWiFi();
  }

  // Lógica de control de riego
  if (modoManual) {
    controlarBombaManual();
  } else {
    controlarBombaAutomatico();
  }

  // Verificar timeout de seguridad
  verificarTimeoutSeguridad();

  // Mostrar estado cada 5 segundos
  static unsigned long ultimoReporte = 0;
  if (millis() - ultimoReporte > 5000) {
    mostrarEstado();
    ultimoReporte = millis();
  }

  delay(1000);  // Ciclo cada 1 segundo
}

// ============================================
// LECTURA DE SENSORES
// ============================================
void leerSensores() {
  // Leer DHT11 (temperatura y humedad ambiental)
  temperaturaAmbiente = dht.readTemperature();
  humedadAmbiente = dht.readHumidity();

  // Validar lecturas DHT11
  if (isnan(temperaturaAmbiente) || isnan(humedadAmbiente)) {
    Serial.println("✗ Error leyendo DHT11");
    temperaturaAmbiente = 0;
    humedadAmbiente = 0;
  }

  // Leer sensor de humedad del suelo (analógico)
  humedadSuelo = analogRead(PIN_HUMEDAD_SUELO);

  // Leer nivel de agua con HC-SR04
  nivelAgua = medirDistanciaUltrasonico();
}

float medirDistanciaUltrasonico() {
  // Enviar pulso de 10us
  digitalWrite(PIN_TRIG, LOW);
  delayMicroseconds(2);
  digitalWrite(PIN_TRIG, HIGH);
  delayMicroseconds(10);
  digitalWrite(PIN_TRIG, LOW);

  // Leer duración del pulso
  long duracion = pulseIn(PIN_ECHO, HIGH, 30000);  // Timeout 30ms

  // Calcular distancia en cm
  float distancia = duracion * 0.034 / 2;

  // Validar lectura
  if (distancia <= 0 || distancia > 400) {
    Serial.println("✗ Error leyendo HC-SR04");
    return 0;
  }

  return distancia;
}

// ============================================
// CONTROL AUTOMÁTICO DE RIEGO
// ============================================
void controlarBombaAutomatico() {
  unsigned long tiempoActual = millis();

  // Verificar si es momento de regar
  bool necesitaRiego = humedadSuelo < HUMEDAD_MIN;
  bool nivelAguaSuficiente = nivelAgua > NIVEL_AGUA_MIN;
  bool tiempoEntreRiegosOK = (tiempoActual - tiempoUltimoRiego) > TIEMPO_ENTRE_RIEGOS;

  // Condiciones para INICIAR riego
  if (!bombaEncendida && necesitaRiego && nivelAguaSuficiente && tiempoEntreRiegosOK) {
    encenderBomba();
    logEvento("RIEGO INICIADO - Humedad baja detectada");
  }

  // Condiciones para DETENER riego
  if (bombaEncendida) {
    unsigned long tiempoRiego = tiempoActual - tiempoInicioBomba;
    bool humedadAlcanzada = humedadSuelo > HUMEDAD_MAX;
    bool tiempoMaximoAlcanzado = tiempoRiego > TIEMPO_RIEGO_MAX;
    bool nivelAguaBajo = nivelAgua < NIVEL_AGUA_CRITICO;

    if (humedadAlcanzada) {
      apagarBomba();
      logEvento("RIEGO COMPLETADO - Humedad óptima alcanzada");
    } else if (tiempoMaximoAlcanzado) {
      apagarBomba();
      logEvento("RIEGO DETENIDO - Tiempo máximo alcanzado");
    } else if (nivelAguaBajo) {
      apagarBomba();
      logEvento("RIEGO DETENIDO - Nivel de agua bajo");
    }
  }
}

// ============================================
// CONTROL MANUAL DE RIEGO (WiFi)
// ============================================
void controlarBombaManual() {
  if (comandoManualBomba && !bombaEncendida) {
    // Verificar nivel de agua antes de encender
    if (nivelAgua > NIVEL_AGUA_MIN) {
      encenderBomba();
      logEvento("RIEGO MANUAL INICIADO");
    } else {
      logEvento("ERROR: Nivel de agua insuficiente para riego manual");
    }
  } else if (!comandoManualBomba && bombaEncendida) {
    apagarBomba();
    logEvento("RIEGO MANUAL DETENIDO");
  }
}

// ============================================
// FUNCIONES DE CONTROL DE BOMBA
// ============================================
void encenderBomba() {
  digitalWrite(PIN_RELE, HIGH);
  bombaEncendida = true;
  tiempoInicioBomba = millis();
  Serial.println("💧 BOMBA ENCENDIDA");
}

void apagarBomba() {
  digitalWrite(PIN_RELE, LOW);
  bombaEncendida = false;
  tiempoUltimoRiego = millis();
  Serial.println("⏹  BOMBA APAGADA");
}

// ============================================
// SEGURIDAD: TIMEOUT
// ============================================
void verificarTimeoutSeguridad() {
  if (bombaEncendida) {
    unsigned long tiempoRiego = millis() - tiempoInicioBomba;

    if (tiempoRiego > TIMEOUT_RIEGO) {
      apagarBomba();
      logEvento("EMERGENCIA: Timeout de seguridad - Bomba apagada forzosamente");
    }
  }
}

// ============================================
// LOGGING DE EVENTOS
// ============================================
void logEvento(String mensaje) {
  Serial.println("─────────────────────────────────────");
  Serial.print("[LOG] ");
  Serial.print(millis() / 1000);
  Serial.print("s: ");
  Serial.println(mensaje);
  Serial.println("─────────────────────────────────────");
}

// ============================================
// MOSTRAR ESTADO DEL SISTEMA
// ============================================
void mostrarEstado() {
  Serial.println("\n╔════════════════════════════════════════╗");
  Serial.println("║     ESTADO DEL SISTEMA DE RIEGO       ║");
  Serial.println("╠════════════════════════════════════════╣");

  // Sensores ambientales
  Serial.print("║ Temperatura: ");
  Serial.print(temperaturaAmbiente, 1);
  Serial.println(" °C");

  Serial.print("║ Humedad Amb: ");
  Serial.print(humedadAmbiente, 1);
  Serial.println(" %");

  // Humedad del suelo
  Serial.print("║ Humedad Suelo: ");
  Serial.print(humedadSuelo);
  Serial.print(" (");
  if (humedadSuelo < HUMEDAD_MIN) {
    Serial.print("BAJO - NECESITA RIEGO");
  } else if (humedadSuelo > HUMEDAD_MAX) {
    Serial.print("ALTO - OK");
  } else {
    Serial.print("NORMAL");
  }
  Serial.println(")");

  // Nivel de agua
  Serial.print("║ Nivel Agua: ");
  Serial.print(nivelAgua, 1);
  Serial.print(" cm (");
  if (nivelAgua < NIVEL_AGUA_CRITICO) {
    Serial.print("CRÍTICO");
  } else if (nivelAgua < NIVEL_AGUA_MIN) {
    Serial.print("BAJO");
  } else {
    Serial.print("OK");
  }
  Serial.println(")");

  // Estado de la bomba
  Serial.print("║ Bomba: ");
  if (bombaEncendida) {
    Serial.print("ENCENDIDA (");
    Serial.print((millis() - tiempoInicioBomba) / 1000);
    Serial.println("s)");
  } else {
    Serial.println("APAGADA");
  }

  // Modo de operación
  Serial.print("║ Modo: ");
  Serial.println(modoManual ? "MANUAL" : "AUTOMÁTICO");

  // WiFi
  Serial.print("║ WiFi: ");
  Serial.println(WiFi.status() == WL_CONNECTED ? "Conectado" : "Desconectado");

  Serial.println("╚════════════════════════════════════════╝\n");
}

// ============================================
// SERVIDOR WEB WIFI
// ============================================
void procesarComandosWiFi() {
  WiFiClient client = server.available();

  if (client) {
    String request = "";

    while (client.connected()) {
      if (client.available()) {
        char c = client.read();
        request += c;

        if (c == '\n') {
          // Procesar comandos
          if (request.indexOf("GET /status") >= 0) {
            enviarEstadoJSON(client);
          } else if (request.indexOf("GET /on") >= 0) {
            modoManual = true;
            comandoManualBomba = true;
            enviarRespuesta(client, "Bomba encendida manualmente");
          } else if (request.indexOf("GET /off") >= 0) {
            modoManual = true;
            comandoManualBomba = false;
            enviarRespuesta(client, "Bomba apagada manualmente");
          } else if (request.indexOf("GET /auto") >= 0) {
            modoManual = false;
            enviarRespuesta(client, "Modo automático activado");
          } else if (request.indexOf("GET /") >= 0) {
            enviarPaginaWeb(client);
          }

          break;
        }
      }
    }

    client.stop();
  }
}

void enviarEstadoJSON(WiFiClient& client) {
  client.println("HTTP/1.1 200 OK");
  client.println("Content-Type: application/json");
  client.println("Connection: close");
  client.println();

  client.print("{");
  client.print("\"temperatura\":");
  client.print(temperaturaAmbiente);
  client.print(",\"humedad_amb\":");
  client.print(humedadAmbiente);
  client.print(",\"humedad_suelo\":");
  client.print(humedadSuelo);
  client.print(",\"nivel_agua\":");
  client.print(nivelAgua);
  client.print(",\"bomba\":");
  client.print(bombaEncendida ? "true" : "false");
  client.print(",\"modo\":\"");
  client.print(modoManual ? "manual" : "automatico");
  client.println("\"}");
}

void enviarRespuesta(WiFiClient& client, String mensaje) {
  client.println("HTTP/1.1 200 OK");
  client.println("Content-Type: text/plain");
  client.println("Connection: close");
  client.println();
  client.println(mensaje);
}

void enviarPaginaWeb(WiFiClient& client) {
  client.println("HTTP/1.1 200 OK");
  client.println("Content-Type: text/html");
  client.println("Connection: close");
  client.println();

  client.println("<!DOCTYPE html>");
  client.println("<html><head><meta charset='UTF-8'>");
  client.println("<title>Sistema de Riego ESP32</title>");
  client.println("<meta name='viewport' content='width=device-width, initial-scale=1'>");
  client.println("<style>");
  client.println("body{font-family:Arial;margin:20px;background:#f0f0f0}");
  client.println(".container{max-width:600px;margin:0 auto;background:white;padding:20px;border-radius:10px;box-shadow:0 2px 10px rgba(0,0,0,0.1)}");
  client.println("h1{color:#2c3e50;text-align:center}");
  client.println(".sensor{background:#ecf0f1;padding:15px;margin:10px 0;border-radius:5px}");
  client.println(".btn{display:inline-block;padding:10px 20px;margin:5px;background:#3498db;color:white;text-decoration:none;border-radius:5px}");
  client.println(".btn:hover{background:#2980b9}");
  client.println(".status{font-size:24px;text-align:center;padding:20px}");
  client.println(".on{color:#27ae60}.off{color:#e74c3c}");
  client.println("</style></head><body>");

  client.println("<div class='container'>");
  client.println("<h1>🌱 Sistema de Riego</h1>");

  client.print("<div class='status ");
  client.print(bombaEncendida ? "on'>💧 BOMBA ENCENDIDA" : "off'>⏹ BOMBA APAGADA");
  client.println("</div>");

  client.print("<div class='sensor'>🌡️ Temperatura: ");
  client.print(temperaturaAmbiente, 1);
  client.println(" °C</div>");

  client.print("<div class='sensor'>💧 Humedad Ambiental: ");
  client.print(humedadAmbiente, 1);
  client.println(" %</div>");

  client.print("<div class='sensor'>🌿 Humedad Suelo: ");
  client.print(humedadSuelo);
  client.println("</div>");

  client.print("<div class='sensor'>💦 Nivel Agua: ");
  client.print(nivelAgua, 1);
  client.println(" cm</div>");

  client.print("<div class='sensor'>⚙️ Modo: ");
  client.print(modoManual ? "MANUAL" : "AUTOMÁTICO");
  client.println("</div>");

  client.println("<div style='text-align:center;margin-top:20px'>");
  client.println("<a href='/on' class='btn'>💧 Encender</a>");
  client.println("<a href='/off' class='btn'>⏹ Apagar</a>");
  client.println("<a href='/auto' class='btn'>⚙️ Automático</a>");
  client.println("</div>");

  client.println("<script>setTimeout(function(){location.reload()},10000)</script>");
  client.println("</div></body></html>");
}
