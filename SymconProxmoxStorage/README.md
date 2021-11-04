# SymconProxmoxStorage

### Inhaltsverzeichnis

1. [Funktionsumfang](#1-funktionsumfang)
2. [Voraussetzungen](#2-voraussetzungen)
3. [Einrichten der Instanzen in IP-Symcon](#4-einrichten-der-instanzen-in-ip-symcon)
4. [Statusvariablen und Profile](#5-statusvariablen-und-profile)


### 1. Funktionsumfang

Auslesen der Speicherinformation

### 2. Vorraussetzungen

- IP-Symcon ab Version 5.5
- [SymconProxmoxIO](https://github.com/sewo777/SymconProxmox/tree/main/SymconProxmoxIO#readme)


### 3. Einrichten der Instanzen in IP-Symcon

 Unter 'Instanz hinzufügen' kann das 'SymconProxmoxNode'-Modul mithilfe des Schnellfilters gefunden werden.  
	- Weitere Informationen zum Hinzufügen von Instanzen in der [Dokumentation der Instanzen](https://www.symcon.de/service/dokumentation/konzepte/instanzen/#Instanz_hinzufügen)

__Konfigurationsseite__:

Name     | Beschreibung
-------- | ------------------
 Knoten | Name vom Knoten (Node)
 Name Speicher | Name von Speicher der Ausgelesen werden soll
 Aktualisierungsintervall | Intervall Daten auslesen

### 4. Statusvariablen und Profile

Die Statusvariablen werden automatisch angelegt und können in der Instanzeinstellung Deaktiviert werden.

#### Statusvariablen

Name   | Typ     | Beschreibung
------ | ------- | ------------
Speicher Aktiv | Bool | Speicher Aktiv 
Speicher Aktiviert | Bool | Speicher Aktiviert
Speicherplatz Gesammt | Float | Speicherplatz Gesammt 
Speicherplatz in Benutzung | Float | Speicherplatz in Benutzung
Speicherplatz Verfügbar | Float | Speicherplatz Verfügbar
Typ | String | Dateisystem

#### Profile

Name   | Typ
------ | -------
PVE_Speicher | Float
