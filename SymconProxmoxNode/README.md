# SymconProxmoxNode
Beschreibung des Moduls.

### Inhaltsverzeichnis

1. [Funktionsumfang](#1-funktionsumfang)
2. [Voraussetzungen](#2-voraussetzungen)
3. [Software-Installation](#3-software-installation)
4. [Einrichten der Instanzen in IP-Symcon](#4-einrichten-der-instanzen-in-ip-symcon)
5. [Statusvariablen und Profile](#5-statusvariablen-und-profile)
6. [WebFront](#6-webfront)
7. [PHP-Befehlsreferenz](#7-php-befehlsreferenz)

### 1. Funktionsumfang

Auslesen der Betriebsdaten vom Knoten (Node)

### 2. Vorraussetzungen

- IP-Symcon ab Version 5.5
- [SymconProxmoxIO](https://github.com/sewo777/SymconProxmox/tree/main/SymconProxmoxIO#readme)


### 4. Einrichten der Instanzen in IP-Symcon

 Unter 'Instanz hinzufügen' kann das 'SymconProxmoxNode'-Modul mithilfe des Schnellfilters gefunden werden.  
	- Weitere Informationen zum Hinzufügen von Instanzen in der [Dokumentation der Instanzen](https://www.symcon.de/service/dokumentation/konzepte/instanzen/#Instanz_hinzufügen)

__Konfigurationsseite__:

Name     | Beschreibung
-------- | ------------------
Knoten   | Name vom Knoten (Node)
Aktualisierungsintervall | Intervall Daten auslesen        |

### 5. Statusvariablen und Profile

Die Statusvariablen/Kategorien werden automatisch angelegt und können in der Instanzeinstellung Deaktiviert werden.

#### Statusvariablen

Name   | Typ     | Beschreibung
------ | ------- | ------------
Betriebszeit | String | Betriebszeiz vom Knoten
CPU | String | CPU Bezeichning
CPU Auslastung | Float | Auslastung der CPU in %
CPU Sockel | Integer | Anzahl CPU Sockel im System
CPU Kerne | Integer | Anzahl CPU Kerne im System
CPU(s) | Integer | Anzahl CPU's Gesamt
RAM Gesamt | Float | RAM Speicher im System
RAM Auslastung | Float | Speicher in Benutzung
RAM Verfügbar | Float | Verfügbarer RAM Speicher 
Speicherplatz Gesamt | Float | HD Speicher Gesamt von Root
Speicherplatz in Benutzung | Float | HD Speicher in Benutzung
Speicherplatz Verfügbar | Float | HD Speicher in Verfügbar

#### Profile

Name   | Typ
------ | -------
       |
       |

### 6. WebFront

Die Funktionalität, die das Modul im WebFront bietet.

### 7. PHP-Befehlsreferenz

`boolean PVENODE_BeispielFunktion(integer $InstanzID);`
Erklärung der Funktion.

Beispiel:
`PVENODE_BeispielFunktion(12345);`
