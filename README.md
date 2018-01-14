### IP-Symcon Modul SymconMeteoblue Weather

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

## 4. Konfiguration:

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


## 5. Profile

Das Modul legt folgende Profile an:

Name | Typ | Verwendung
------ | ------ | ---------------------------------
MBW.WindDirection | Integer | Darstellung der Gradzahlen in Himmelsrichtungen
MBW.UVIndex | Integer | Farbkodierung des UVIndex (Transparent, Grün, Rot, Lila) - angelehnt an Warnstufen


## 6. Variablen

Das Modul legt folgende Variablen an:


Variable | Typ | Variable | Beschreibung
------ | ------ |------ | ---------------------------------
Wetter |Wetter | String | HTML Darstellung der Vorhersage gem. Einstellungen




## 7. Befehlsreferenz

Das Module hat eine öffentliche Funktion: MBW_Update()

## 9. Changelog

[![License: CC BY 4.0](https://img.shields.io/badge/License-CC%20BY%204.0-lightgrey.svg)](https://creativecommons.org/licenses/by/4.0/)

## 9. Changelog

v1.0 first release

