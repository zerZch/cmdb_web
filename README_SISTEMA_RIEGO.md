# Sistema de Riego Automatizado ESP32

## 📋 Descripción

Sistema de riego inteligente que monitorea humedad del suelo, temperatura ambiente y nivel de agua, controlando automáticamente una bomba de agua con múltiples medidas de seguridad.

---

## 🔌 Hardware Requerido

| Componente | Especificaciones | Función |
|------------|------------------|---------|
| ESP32 WROOM-32 | Microcontrolador con WiFi | Cerebro del sistema |
| DHT11 | Sensor temperatura/humedad | Monitoreo ambiental |
| Sensor Humedad Suelo | Analógico | Mide humedad en tierra |
| HC-SR04 | Sensor ultrasónico | Nivel de agua en depósito |
| Relé HW-307 | 5VDC, 1 canal | Control de bomba |
| Mini Bomba | 3-6V DC sumergible | Riego |

---

## 🔧 Conexiones

```
ESP32 WROOM-32          →    Componente
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
GPIO 34 (ADC1_CH6)      →    Sensor humedad suelo (analógico)
GPIO 26 (D26)           →    DHT11 - Pin DATA
GPIO 5 (D5)             →    HC-SR04 - Pin TRIG
GPIO 18 (D18)           →    HC-SR04 - Pin ECHO
GPIO 25 (D25)           →    Relé HW-307 - Pin IN (control)

VIN (5V)                →    Relé HW-307 - Pin VCC
GND                     →    Relé HW-307 - Pin GND
GND                     →    DHT11 - Pin GND
GND                     →    HC-SR04 - Pin GND
GND                     →    Sensor humedad suelo - GND
3.3V                    →    DHT11 - Pin VCC
3.3V o 5V               →    HC-SR04 - Pin VCC
3.3V                    →    Sensor humedad suelo - VCC
```

### ⚠️ Importante: Relé
- El relé HW-307 funciona con **lógica invertida** en algunos modelos
- `HIGH` = Bomba encendida
- `LOW` = Bomba apagada
- Si tu relé funciona al revés, invierte `HIGH` y `LOW` en las funciones

---

## 📚 Librerías Necesarias

Instalar desde el Administrador de Librerías de Arduino IDE:

```cpp
DHT sensor library by Adafruit (v1.4.4 o superior)
```

---

## ⚙️ Configuración

### 1. Configurar WiFi (Opcional)

Editar en el código:

```cpp
const char* ssid = "TU_RED_WIFI";
const char* password = "TU_PASSWORD";
```

### 2. Calibrar Sensor de Humedad del Suelo

El sensor de humedad devuelve valores ADC (0-4095):
- **Seco**: ~3500-4095
- **Húmedo**: ~1000-2000
- **Agua**: ~0-500

Para calibrar:

1. Seca completamente el sensor → Anota el valor
2. Sumerge el sensor en agua → Anota el valor
3. Ajusta los umbrales en el código:

```cpp
#define HUMEDAD_MIN  1500  // Por debajo → REGAR
#define HUMEDAD_MAX  2500  // Por encima → DETENER
```

### 3. Configurar Nivel de Agua

Ajustar según la altura de tu depósito:

```cpp
#define NIVEL_AGUA_MIN      5.0   // cm desde sensor (mínimo seguro)
#define NIVEL_AGUA_CRITICO  2.0   // cm (nivel crítico - NO REGAR)
```

### 4. Ajustar Tiempos

```cpp
#define TIEMPO_RIEGO_MAX    30000  // 30 segundos máximo continuo
#define TIEMPO_ENTRE_RIEGOS 300000 // 5 minutos mínimo entre riegos
#define TIMEOUT_RIEGO       60000  // 1 minuto timeout de seguridad
```

---

## 🚀 Instalación

### Arduino IDE

1. Instalar ESP32 Board Manager:
   - Archivo → Preferencias
   - Agregar URL: `https://dl.espressif.com/dl/package_esp32_index.json`
   - Herramientas → Placa → Gestor de tarjetas → Buscar "ESP32" → Instalar

2. Seleccionar placa:
   - Herramientas → Placa → ESP32 Arduino → ESP32 Dev Module

3. Configurar parámetros:
   - Upload Speed: 115200
   - Flash Frequency: 80MHz
   - Partition Scheme: Default

4. Instalar librerías:
   - Programa → Incluir Librería → Administrar Librerías
   - Buscar "DHT sensor library" → Instalar

5. Abrir `sistema_riego_esp32.ino`

6. Compilar y cargar al ESP32

---

## 📊 Funcionamiento

### Modo Automático (Default)

El sistema riega automáticamente cuando:

✅ Humedad del suelo < HUMEDAD_MIN
✅ Nivel de agua > NIVEL_AGUA_MIN
✅ Han pasado > 5 minutos desde último riego

El sistema detiene el riego cuando:

🛑 Humedad del suelo > HUMEDAD_MAX (objetivo alcanzado)
🛑 Tiempo de riego > 30 segundos (seguridad)
🛑 Nivel de agua < NIVEL_AGUA_CRITICO (seguridad)

### Medidas de Seguridad

1. **Tiempo máximo de riego**: 30 segundos continuos
2. **Tiempo entre riegos**: Mínimo 5 minutos
3. **Verificación de nivel de agua**: No riega si el agua está baja
4. **Timeout de emergencia**: 1 minuto máximo absoluto
5. **Inicialización segura**: Bomba apagada al arrancar

---

## 🌐 Control WiFi

### Acceder al Sistema

1. Conectar ESP32
2. Abrir Monitor Serie (115200 baud)
3. Buscar la IP asignada: `IP: 192.168.x.x`
4. Abrir navegador y acceder a: `http://192.168.x.x`

### Página Web

Interfaz visual con:
- 📊 Estado de todos los sensores en tiempo real
- 💧 Estado de la bomba (ON/OFF)
- 🔘 Botones de control:
  - **Encender**: Activa bomba manualmente
  - **Apagar**: Desactiva bomba manualmente
  - **Automático**: Vuelve al modo automático
- 🔄 Auto-recarga cada 10 segundos

### API REST

| Endpoint | Método | Descripción | Respuesta |
|----------|--------|-------------|-----------|
| `/status` | GET | Estado en JSON | `{"temperatura":25,"humedad_amb":60,...}` |
| `/on` | GET | Encender bomba (modo manual) | Texto plano |
| `/off` | GET | Apagar bomba (modo manual) | Texto plano |
| `/auto` | GET | Activar modo automático | Texto plano |
| `/` | GET | Página web de control | HTML |

#### Ejemplo de uso con curl:

```bash
# Obtener estado
curl http://192.168.1.100/status

# Encender bomba
curl http://192.168.1.100/on

# Apagar bomba
curl http://192.168.1.100/off

# Modo automático
curl http://192.168.1.100/auto
```

---

## 🖥️ Monitor Serie

### Información Mostrada

Cada 5 segundos se muestra:

```
╔════════════════════════════════════════╗
║     ESTADO DEL SISTEMA DE RIEGO       ║
╠════════════════════════════════════════╣
║ Temperatura: 25.0 °C
║ Humedad Amb: 60.0 %
║ Humedad Suelo: 1800 (BAJO - NECESITA RIEGO)
║ Nivel Agua: 12.5 cm (OK)
║ Bomba: ENCENDIDA (15s)
║ Modo: AUTOMÁTICO
║ WiFi: Conectado
╚════════════════════════════════════════╝
```

### Logs de Eventos

```
─────────────────────────────────────
[LOG] 120s: RIEGO INICIADO - Humedad baja detectada
─────────────────────────────────────
```

---

## 🐛 Solución de Problemas

### Problema: Bomba no enciende

**Soluciones:**
1. Verificar conexión del relé al GPIO 25
2. Comprobar alimentación del relé (5V)
3. Si el relé es de lógica invertida, cambiar en el código:
   ```cpp
   digitalWrite(PIN_RELE, LOW);  // En encenderBomba()
   digitalWrite(PIN_RELE, HIGH); // En apagarBomba()
   ```

### Problema: Sensor de humedad siempre lee 0 o 4095

**Soluciones:**
1. Verificar que está conectado a GPIO 34 (ADC1_CH6)
2. No usar GPIO 25, 26, o 27 (solo ADC2, incompatible con WiFi)
3. Limpiar el sensor de corrosión
4. Verificar 3.3V de alimentación

### Problema: HC-SR04 no lee distancia

**Soluciones:**
1. Verificar TRIG en GPIO 5 y ECHO en GPIO 18
2. Usar 5V de alimentación (no 3.3V)
3. Máxima distancia: 4 metros
4. Verificar que no hay obstáculos

### Problema: DHT11 siempre devuelve NaN

**Soluciones:**
1. Verificar DATA conectado a GPIO 26
2. Usar 3.3V (no 5V)
3. Esperar 2 segundos después del arranque
4. Agregar resistencia pull-up de 10kΩ entre DATA y VCC

### Problema: WiFi no conecta

**Soluciones:**
1. Verificar SSID y contraseña
2. Estar cerca del router
3. Red debe ser 2.4GHz (ESP32 no soporta 5GHz)
4. El sistema funciona sin WiFi (modo offline)

---

## 📈 Mejoras Futuras Sugeridas

1. **MQTT**: Integración con Home Assistant o Domoticz
2. **Base de datos**: Guardar historial de riegos en SD
3. **Notificaciones**: Push notifications cuando el agua está baja
4. **Pantalla OLED**: Display local sin necesidad de WiFi
5. **Múltiples zonas**: Controlar varios sectores de riego
6. **Sensor de lluvia**: Cancelar riego si está lloviendo
7. **Luz UV**: Sensor para detectar horas de sol
8. **Válvulas**: Control de múltiples válvulas con un solo sistema

---

## 📝 Licencia

Código de dominio público. Úsalo libremente en tus proyectos.

---

## 🙏 Créditos

Sistema diseñado para ESP32 con énfasis en seguridad y confiabilidad.

---

## 📞 Soporte

Si tienes problemas o preguntas:
1. Revisa la sección "Solución de Problemas"
2. Verifica las conexiones según el diagrama
3. Comprueba el Monitor Serie para mensajes de error
4. Asegúrate de que todas las librerías están instaladas

---

**Versión**: 1.0
**Fecha**: 2025-12-09
**Compatible con**: ESP32 WROOM-32, Arduino IDE 2.x
