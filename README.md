# Directory Listing, Suche und Changelog

Dieses Projekt bietet ein PHP-basiertes Directory Listing für ISO-Dateien, Images und Apps mit integrierter Suchfunktion, Ordnernavigation und einem automatischen Changelog-System. Änderungen wie hinzugefügte oder gelöschte Dateien werden automatisch protokolliert. Es eignet sich besonders für Server, auf denen man Dateien wie Linux-Distributionen, Windows-Images oder andere große Dateien zum Download anbieten möchte.

## Funktionen

- **Directory Listing**: Zeigt alle Dateien und Ordner im aktuellen Verzeichnis an.
- **Suche**: Ermöglicht die Suche nach bestimmten Dateien oder Ordnern.
- **Ordnernavigation**: Ermöglicht das Navigieren in Ordnern und deren Unterordnern.
- **Changelog**: Protokolliert automatisch die letzten 5 Änderungen (hinzugefügte/gelöschte Dateien) mit Zeitstempel.
- **Download und Direktlink**: Jeder Datei ist ein Download-Button und ein Button zum Kopieren des Direktlinks hinzugefügt.

## Installation

1. Lade das Repository als `.zip`-Datei herunter und entpacke es auf deinem Webserver.
2. Stelle sicher, dass PHP auf deinem Server läuft.
3. Lade alle Dateien in das gewünschte Verzeichnis auf deinem Webserver hoch.
4. Das Skript erstellt eine `changelog.json`-Datei, in der Änderungen protokolliert werden. Diese wird automatisch generiert.

## Nutzung

- **Datei- und Ordneranzeige**: Beim Aufruf des Verzeichnisses wird eine Liste aller Dateien und Ordner angezeigt. Ordner können durch Anklicken geöffnet werden.
- **Suche**: Mit der Suchleiste oben können gezielt Dateien oder Ordner im aktuellen Verzeichnis gefunden werden. Gebe den Namen oder Teile davon ein und drücke "Suchen".
- **Changelog**: Unter der Überschrift wird ein Changelog der letzten 5 Änderungen angezeigt, darunter hinzugefügte und gelöschte Dateien.
- **Download**: Jede Datei hat einen Button zum Herunterladen.
- **Direktlink kopieren**: Mit einem Klick auf "Direktlink kopieren" wird der Link zur Datei in die Zwischenablage kopiert.

## Anpassungen

- **Logo ändern**: Ersetze die Datei `ISO Server.svg` durch dein eigenes Logo.
- **Verzeichnis-Path**: Der Basisverzeichnis-Pfad ist aktuell auf `./` gesetzt. Du kannst dies in der PHP-Variable `$baseDirectory` anpassen.
- **Changelog-Größe**: Die Anzahl der Änderungen im Changelog ist aktuell auf 5 begrenzt. Dies kannst du in der Datei `index.php` anpassen, indem du die Zeile `array_slice(array_reverse($changelog['log']), 0, 5)` modifizierst.
- **Bootstrap-Design**: Das Projekt verwendet Bootstrap für die grafische Gestaltung. Du kannst das Styling einfach anpassen, indem du die eingebundene Bootstrap-Version änderst oder dein eigenes CSS hinzufügst.

## Anforderungen

- **PHP 7.0 oder höher**: Das Skript verwendet standardmäßige PHP-Funktionen wie `realpath()`, `json_encode()` und `opendir()`, die in neueren PHP-Versionen verfügbar sind.
- **Webserver**: Ein Webserver wie Apache oder Nginx wird benötigt, um die Dateien bereitzustellen.

## Screenshots

![Directory Listing Screenshot](https://example.com/screenshot.jpg) _(Bitte füge hier einen Screenshot des Verzeichnisses ein.)_

## Lizenz

Dieses Projekt steht unter der MIT-Lizenz. Siehe [LICENSE](LICENSE) für Details.
