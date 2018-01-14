### IP-Symcon Modul SymconMeteoblue Weather

## Dokumentation

**Inhaltsverzeichnis**

1. [Funktionsumfang](#1-funktionsumfang) 
2. [Systemanforderungen](#2-systemanforderungen)
3. [Installation](#3-installation)
4. [Variablen](#4-variablen)
5. [Befehlsreferenz](#5-befehlsreferenz)
6. [Changelog](#6-changelog) 


## 1. Funktionsumfang

IP-Symcon Modul zur graphischen Anzeige der Wettervorhersage
sowie Einzelwerten von meteoblue.

Diese Implementierung basiert auf:
http://content.meteoblue.com/en/help/technical-documentation/meteoblue-api 


## 2. Systemanforderungen
- IP-Symcon ab Version 4.2


## 3. Installation
Mach' ein Backup. Die Installation erfolgt auf eigene Verantwortung!

Über die Kern-Instanz "Module Control" folgende URL hinzufügen:

`https://github.com/nik78476/SymconMeteoblue.git`

Nach erfolgreicher Installation an dem Ort eurer Wahl eine neue Instanz
anlegen (Hersteller: Sonstige, Gerät: MeteoblueWetter). 
Die neue Instanz findet ihr dort, wo ihr sie angelegt habt.

Konfiguration:

Die Konfiguration ist eigentlich selbsterklärend. Über die Homepage von Meteoblue
muss ein API-Key beantragt werden, dieser ist dann 1 Jahr gültig. Die Homepage bietet
auch die Möglichkeit die Positionsbestimmung durchzuführen. 


Parameter | Beschreibung
------ | ---------------------------------
API Key | Persönlicher API-Key
Latitude | Latitude des Auswerteortes (http://content.meteoblue.com/en/help/global-location-search/find-a-place)
Longitude | Longitude des Auswerteortes (http://content.meteoblue.com/en/help/global-location-search/find-a-place)
ASL Code | ASL Code des Auswerteortes (http://content.meteoblue.com/en/help/global-location-search/find-a-place)
Intervall | Aktualisierungsintervall in Sek. (Standard: 100)
Sprache | noch nicht unterstützt
Temperatureinheit | Celsius oder Fahrenheit (Standard: Celsius)
Anzahl Tage | noch nicht unterstützt (Standard: 1 Tag)


## 4. Profile

Das Modul legt folgende Profile an:

Name | Typ | Verwendung
------ | ------ | ---------------------------------
MBW.WindDirection | Integer | Darstellung der Gradzahlen in Himmelsrichtungen
MBW.UVIndex | Integer | Farbkodierung des UVIndex (Transparent, Grün, Rot, Lila) - angelehnt an Warnstufen


## 5. Variablen

Das Modul legt folgende Variablen an:


Variable | Typ | Variable | Beschreibung
------ | ------ |------ | ---------------------------------
Wetter |Wetter | String | HTML Darstellung der Vorhersage gem. Einstellungen




## 6. Befehlsreferenz

Das Module hat eine öffentliche Funktion: MBW_Update()

## 6. Changelog

v1.0 first release

v1.1 update image zoom

v1.2 update Variables and Single-Image-Mode

v1.3 Luftdruck entfernt, WetterImage zeigt nun den OriginalCode von Yahoo 
