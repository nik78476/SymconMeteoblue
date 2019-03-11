### IP-Symcon Modul SymconMeteoblue Weather

[![Version](https://img.shields.io/badge/Symcon_Version-5.0-red.svg)](https://www.symcon.de/service/dokumentation/entwicklerbereich/sdk-tools/sdk-php/)
![Version](https://img.shields.io/badge/Modul_Version-1.0-blue.svg)
![Version](https://img.shields.io/badge/Code-PHP-blue.svg)
[![License](https://img.shields.io/badge/License-CC%20BY--NC--SA%204.0-green.svg)](https://creativecommons.org/licenses/by-nc-sa/4.0/)
[![StyleCI](https://github.styleci.io/repos/136796530/shield?branch=master)](https://github.styleci.io/repos/136796530)

## Dokumentation

**Inhaltsverzeichnis**

1. [Funktionsumfang](#1-funktionsumfang) 
2. [Systemanforderungen](#2-systemanforderungen)
3. [Installation](#3-installation)
4. [Konfiguration](#4-konfiguration)
5. [Profile](#5-profile)
6. [Variablen](#6-variablen)
7. [Befehlsreferenz](#7-befehlsreferenz)
8. [Lizenz](#8-lizenz) 
9. [Changelog](#9-changelog) 
10. [GUIDs](#10-GUIDs) 
11. [Author](#11-author) 

---
## 1. Funktionsumfang

IP-Symcon Modul zur graphischen Anzeige der Wettervorhersage
sowie Einzelwerten von meteoblue.

Diese Implementierung basiert auf:
http://content.meteoblue.com/en/help/technical-documentation/meteoblue-api 

---
## 2. Systemanforderungen
- IP-Symcon ab Version 5.x+
- API-Key von Meteoblue, dieser ist für die 1-Tages-Vorhersage kostenlos und
  muss bei Meteoblue beantragt werden: 
  https://content.meteoblue.com/de/produkte/servicezugang/meteoblue-wetter-api

---
## 3. Installation
###### **Mach' ein Backup! Die Installation erfolgt auf eigene Verantwortung!*

Über die Kern-Instanz "Module Control" folgende URL hinzufügen:

`https://github.com/nik78476/SymconMeteoblue.git`

Nach erfolgreicher Installation an dem Ort eurer Wahl eine neue Instanz
anlegen (Hersteller: Sonstige, Gerät: MeteoblueWetter). 
Die neue Instanz findet ihr dort, wo ihr sie angelegt habt.

---
## 4. Konfiguration:

Die Konfiguration ist weitestgehend selbsterklärend. Über die Homepage von Meteoblue
muss ein API-Key beantragt werden, dieser ist dann 1 Jahr gültig. Die Homepage bietet
auch die Möglichkeit die Positionsbestimmung durchzuführen. Ohne eingetragenen API-Key
werden keine Werte abgefragt und es erfolgen Fehlermeldungen.


Parameter | Beschreibung
------ | ---------------------------------
API Key | Persönlicher API-Key
Latitude | Latitude des Auswerteortes (http://content.meteoblue.com/en/help/global-location-search/find-a-place)
Longitude | Longitude des Auswerteortes (http://content.meteoblue.com/en/help/global-location-search/find-a-place)
ASL Code | ASL Code des Auswerteortes (ASL = Above Sea Level, m über Meereshöhe) (http://content.meteoblue.com/en/help/global-location-search/find-a-place)
Datumsformat | PHP-Format konformes Datumsformat für die Anzeige (z.B. d.m.Y oder d-m-Y) - stellt man hier "l" ein, wird der Tag als Name (Montag, Dienstag, Mittwoch....) angezeigt
Bildbreite (px) | Definition der Bildbreite des Pictrogramms
Bildhöhe (px) | Definition der Bildhöhe des Pictrogramms
Intervall | Aktualisierungsintervall in Sek. (Standard: 3600) - Die kostenlose Version unterstützt nur 100 Abfragen pro Tag, daher auf diese Einstellung achten
Temperatureinheit | Celsius oder Fahrenheit (Standard: Celsius)
Tage anzeigen | Auswahl Tag (heute, morgen, Tag+x) - Max Heute + 5
Nachkommastellen Vorhersage | Anzahl Nachkommastellen in der Vorhersageanzeige
Schriftgrösse (px) | Regelt die Schriftgrösse in px in der Vorhersageanzeige

---
## 5. Profile

Das Modul legt folgende Profile an:

Name | Typ | Verwendung
------ | ------ | ---------------------------------
MBW.WindDirection | Integer | Darstellung der Gradzahlen in Himmelsrichtungen
MBW.UVIndex | Integer | Farbkodierung des UVIndex (Transparent, Grün, Rot, Lila) - angelehnt an Warnstufen von https://de.wikipedia.org/wiki/UV-Index

---
## 6. Variablen

Das Modul legt folgende Variablen an:

VariablenID | Typ | Variablenbezeichnung (sichtbar) | Profil| Beschreibung
------ | ------ |------|------ | ---------------------------------
MBW_V_LASTUPDATE|String | Last Update | | Letzte Datenaktualisierung
MBW_V_UVINDEX|Integer | UV Index | MBW.UVIndex| UV Index
MBW_V_TEMPERATURE_MAX|Float | Temp (max) | ~Temperature | Maximale Temperatur 
MBW_V_TEMPERATURE_MIN|Float | Temp (min) | ~Temperature | Minimale Temperatur 
MBW_V_FELTTEMPERATURE_MIN|Float | Gef. Temp (min) | ~Temperature | Gefühlte Minimaltemperatur 
MBW_V_FELTTEMPERATURE_Max|Float | Gef. Temp (max) | ~Temperature | Gefühlte Maximaltemperatur 
MBW_V_WINDDIRECTION|Integer | Windrichtung | MBW.WindDirection | Windrichtung 
MBW_V_FORECASTHTML|String|Vorhersage|~HTMLBox|Vorhersagedarstellung HTML für Anz. Tage aus den Parametern
MBW_V_PICTOCODEURL|String|Wetterpictogramm|~HTMLBox|Wetterpictogramm
MBW_V_SEALEVELPRESSUREMIN|Integer|Luftdruck (min)| |Luftdruck min. in hPa
MBW_V_SEALEVELPRESSUREMAX|Integer|Luftdruck (max)| |Luftdruck max. in hPa
       

---
## 7. Befehlsreferenz

Das Module hat eine öffentliche Funktion: 
```php
MBW_Update('ID der Instanz');
```
---
## 8. Lizenz

[![License: CC BY-SA 4.0](https://img.shields.io/badge/License-CC%20BY--SA%204.0-lightgrey.svg)](https://creativecommons.org/licenses/by-sa/4.0/)
Darf gerne geteilt, geforkt, geliked, darauf verlinkt, für gut und für schlecht befunden, ignoriert werden.

---
## 9. Changelog

Version     | Datum      | Beschreibung
----------- | -----------| -------------------
1.0        | 01.08.2018 | Modulerstellung
1.1        | 13.01.2019 | Div. Erweiterungen
1.2        | 24.01.2019 | Konfigurationsoption MBW_FORECASTPRECISION (Nachkommastellen für Vorhersageanzeige)
1.3        | 30.01.2019 | Übersetzungen, Fehlerkorrekturen
1.4        | 11.03.2019 | Übersetzungen, Fehlerkorrekturen, Datumshandling, Erweiterung auf 6 Anzeigetage


---
## 10. GUIDs

__Modul GUIDs__:

 Name       | GUID                                   | Bezeichnung  |
------------| -------------------------------------- | -------------|
Bibliothek  | {351DDB8A-8A18-4604-943D-6BA4BEE026C2} | Library GUID |
Modul       | {351DDB8A-8A18-4604-943D-6BA4BEE026C2} | Module GUID  |

---
## 11. Author

Mike Fröhlich
https://github.com/nik78476
