# # Symfony Arbeitszeiterfassung – Backend Portfolio Projekt

DZS (Digitales ZeiterfassungsSystem) ist eine webbasierte Anwendung zur Erfassung und Verwaltung von Arbeitszeiten mit rollenbasierter Zugriffskontrolle.
Dieses Projekt wurde eigenständig konzipiert und entwickelt, um typische Backend-Patterns (Authentifizierung, Rollenmodell, Validierung, Mailflows, Domainmodellierung) praxisnah abzubilden.

## Features

- Registrierung & Login
- Rollen-/Rechtekonzept (z. B. Mitarbeiter / Personal / Projektverwaltung)
- Arbeitszeiten erfassen (Start/Ende) inkl. Projektnummer & Bemerkung
- Übersicht/Dashboard (eigene Zeiten, je nach Rolle erweiterte Sicht)
- Passwort-Reset via E-Mail (Token-Link)
- Formularvalidierung & serverseitige Prüfungen

## Tech Stack

- Apache2
- PHP >= 8.2
- Symfony 7.x
- Doctrine ORM + Migrations
- Twig
- Symfony Mailer
- Bootstrap 5

## UI Template & Third Party Assets

Frontend basiert auf:

- Mazer Admin Template (zuramai)
- Bootstrap
- FontAwesome

Alle Lizenzrechte verbleiben bei den jeweiligen Autoren.
Das Frontend basiert auf dem **Free Mazer Admin Dashboard** (zuramai).  
Quelle: https://zuramai.github.io/mazer/  
(Lizenz-/Copyright-Hinweise siehe jeweilige Template-Dateien bzw. Vendor-Assets im Projekt.)

### Logo & Favicon

Logo und Favicon wurden mit https://www.freelogodesign.org erstellt und unterliegen den jeweiligen Lizenzbedingungen der Plattform.

## Custom UI Implementation

Die folgenden Seiten wurden eigenständig entwickelt und nicht aus dem verwendeten Admin-Template übernommen:

- Registrierung
- Anmeldung
- Impressum
- Datenschutz

Layout, Formularstruktur sowie die Integration in Symfony (Controller, Routing, Validierung, Security-Flow) wurden individuell umgesetzt.

## Lokales Setup

### Voraussetzungen
- Apache2-Webserver
- PHP 8.2+
- Composer
- MySQL/MariaDB

## Hinweis

Dieses Repository dient ausschließlich zur Code-Einsicht
im Rahmen meines Entwickler-Portfolios.