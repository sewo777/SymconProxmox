# SymconProxmoxVMID

### Inhaltsverzeichnis

1. [Funktionsumfang](#1-funktionsumfang)
2. [Voraussetzungen](#2-voraussetzungen)
3. [Einrichten der Instanzen in IP-Symcon](#4-einrichten-der-instanzen-in-ip-symcon)
4. [Statusvariablen und Profile](#5-statusvariablen-und-profile)


### 1. Funktionsumfang

Auslesen der Betriebsdaten von einzelnen VM's und LXC's

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
 VM oder LXC | Art der Virtuelle Maschine "VM oder LXC"
 VM/LXC ID | ID Nummer von der Virtuelle Maschine
 Aktualisierungsintervall | Intervall Daten auslesen

### 4. Statusvariablen und Profile

Die Statusvariablen werden automatisch angelegt und können in der Instanzeinstellung Deaktiviert werden.

#### Statusvariablen

Name   | Typ     | Beschreibung
------ | ------- | ------------
Name | String | Name von der Virtuelle Maschine
Betriebszeit | String | Betriebszeit vom Knoten
Status | String | Status der Virtuelle Maschine
CPU(s) | Integer | CPU's Zugewiesen
Bootdisk Größe | Float | Größe der Bootdisk
Bootdisk in Benutzung | Float | Benutzter Speicher der Bootdisk  "Diese Information steht nur LXC's zur verfügung"
RAM Gesamt | Float | RAM Zugewiesen
RAM Auslastung | Float | RAM in Benutzung

#### Profile

Name   | Typ
------ | -------
PVE_Speicher | Float
