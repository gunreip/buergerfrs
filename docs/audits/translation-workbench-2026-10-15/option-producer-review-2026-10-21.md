# Herkunft der Optionswerte – 21.10.2026

## Ergebnis für die 28 Aufrufe

| Optionsgruppe | Aufrufe | Herkunft | Einordnung |
| --- | ---: | --- | --- |
| Rollen-Badge-Farben | 2 | RoleList → IconRegistry → config/buergerfrs-icons.php | 18 konfigurierte Farbwerte |
| Rollen-Badge-Varianten | 2 | RoleList → IconRegistry → config/buergerfrs-icons.php | 4 konfigurierte Varianten |
| Anrede | 2 | CreatePerson / EditPerson: salutationOptions | Je 3 explizite Werte |
| Geschlecht | 2 | CreatePerson / EditPerson: genderOptions | Je 4 explizite Werte |
| Familienstand | 3 | CreatePerson / EditPerson: maritalStatusOptions | Je 7 explizite Werte; zusätzlicher Aufruf zeigt den ausgewählten Wert |
| Kontaktbeziehung | 3 | CreatePerson / EditPerson: emergencyContactRelationshipOptions → PersonContact-Konstanten | Je 9 Werte; Schlüssel benötigen den Konstantennachweis |
| Dokumentart | 9 | PersonDocumentType::options() | Aktive, sortierte DB-Werte; Code-Defaults nur bei fehlender Tabelle oder leerem Ergebnis |
| Dokumentkategorie | 4 | EditPerson::refreshDocumentCategoryOptions() und UI-Erweiterung | Code-Defaults plus bestehende DB-Kategorien plus während der Bearbeitung ergänzte Werte |
| Rollenkategorie | 1 | UserList: zuweisbare Rollen → roleGroups | DB-Kategorien; leere Kategorie wird „other“, Fallback wird per Headline erzeugt |

Summe: 4 konfigurationsbasierte, 10 codebasierte und 14 datenbankabhängige bzw.
veränderliche Aufrufe. Dies sind Aufrufzahlen, nicht die Anzahl der Übersetzungen.
Wiederholte Verwendung einer Liste bleibt als eigene Fundstelle erhalten.

## Nachweis und Grenzen

`inspect-option-producers.php` liest neun aktuelle Quelldateien mit PHP-Parser,
ohne Laravel zu booten, Klassen zu laden, Provider aufzurufen oder eine Datenbank
zu öffnen. Der Bericht `option-producer-source-inventory-2026-10-21.json` enthält
Datei-Hashes, Zeilen, Property-Defaults, Methoden, Konstanten und einzelne
explizite String/Label-Paare. Die Dateien werden am Ende erneut auf unveränderte
Hashes geprüft. Methoden und Ausdrücke werden gespeichert, nicht ausgeführt.

Der Bericht ist eine aktuelle Quellcode-Inventur, **kein neuer Import in die
isolierte Generation**. Die Zuordnung in obiger Tabelle wurde anhand der
Aufrufstellen und ihrer Produzenten geprüft. Der Bericht behauptet ausdrücklich
keine vollständige Laufzeit-Wertemenge. Einzelne String-Paare aus dem AST dürfen
nicht ohne ihren Array-/Methodenkontext als Optionsliste importiert werden.

Insbesondere:

- Öffentliche Livewire-Properties sind überschreibbar. Ihr Default allein beweist
  nicht jeden späteren Laufzeitwert.
- Dokumentart-Defaults sind keine vollständige aktuelle Datenbankliste.
- Dokumentkategorien können durch `createNewDocumentCategory` während der Bearbeitung
  erweitert werden. Auch eine DB-Momentaufnahme deckt diese noch ungespeicherten
  Werte nicht zwingend ab.
- Ein Options-Fallback bleibt vom ursprünglichen Literal und von veröffentlichten
  Übersetzungen getrennt. Ein Gruppen-Key bleibt vom konkreten Optionskey getrennt.
- Die Erfassung eines Produzenten darf keine alten Workbench-Collector-Schreibwege
  als Nebenwirkung auslösen.

## Konsequenz für den nächsten Umsetzungsschritt

### UI-Entscheidung DatabaseValue

Nach ausdrücklicher Vorgabe des Nutzers kann die UI einen Wert als `DatabaseValue`
kennzeichnen: unverändert aus seiner Datenquelle anzeigen, Übersetzungsprüfung
erledigt. Das ist weder eine automatische Folge der DB-Herkunft noch eine
Übersetzungsfreigabe. Keine Translation, kein Locale-Export und kein neuer Key
werden dadurch erzeugt. Bestehende Übersetzungen werden nicht nebenbei gelöscht.

Die spätere Entscheidung muss das betroffene Objekt und ihren Umfang benennen
(einzelner Wert oder ausdrücklich gewählte Quelle), einschließlich Herkunft,
Begründung, Benutzer und Zeitpunkt. Keine pauschale Erledigung aller dynamischen
Aufrufe. Unveränderte Folgescans behalten die Entscheidung; eine geänderte
Quellbedeutung/Zuordnung erfordert erneute Prüfung. Bloße neue Laufzeitwerte sind
von einer geänderten Quellbedeutung zu unterscheiden. Die Entscheidung muss über
die UI wieder geändert werden können. Rohdaten bleiben unverändert.

Dies ist im Target workflow UI als geplante Entscheidung ergänzt. Noch kein
Bedienelement, keine Datenbankmigration und keine produktive Erledigung gebaut.

### Kontextgebundene feste Listen

Die Inventur enthält jetzt `option_lists`: sechs Property-Listen mit vollständig
statischen String-Paaren plus zwei Config-Listen, insgesamt 50 Paare. Zwei weitere
Property-Listen enthalten 18 Kontaktbeziehungs-Einträge mit Konstanten-Schlüsseln;
deren Abhängigkeiten bleiben offen. Die Werte werden nicht zusammengelegt.

`StaticOptionListEvidence` liest ausschließlich die benannte Klasse/Property
oder den exakten Config-Pfad. Unbekannte Ausdrücke werden nicht ausgewertet;
doppelte Schlüssel und mehrdeutige Config-Pfade werden abgewiesen. Jede Liste
trägt Datei-Hash und Zeilen. `complete_static_default` bezeichnet nur den
geschriebenen Default, niemals die Vollständigkeit aller späteren Laufzeitwerte.
Fünf Regressionstests mit 13 Assertions bestanden.

Für feste Listen wird ein nachgewiesener Array-Kontext mit Schlüssel/Wert-Paaren
und den Hashes seiner Abhängigkeiten benötigt (inklusive Konstanten bzw. Config).
Bei datenbankabhängigen Listen braucht es dagegen eine ausdrücklich lesende,
datierte Optionsbeobachtung mit Herkunft, Auswahlbedingungen und Vollständigkeits-
angabe. Ein leerer oder fehlgeschlagener Beobachtungslauf darf keine vorhandenen
Findings stilllegen. Quellcode-Fundstelle, beobachteter Optionswert und Übersetzung
bleiben getrennte Objekte.

Die 28 Fälle werden deshalb noch nicht pauschal freigegeben. Die bestehende
Prüfvorschau bleibt unverändert bei 59 Fragen in 32 Dateien; produktive Daten und
Übersetzungen bleiben unangetastet.

## Prüfung

### Ergänzung: Konstantennachweise

Die beiden Kontaktbeziehungslisten sind nun ebenfalls statisch belegt. Die
18 Konstanten-Verwendungen referenzieren neun öffentliche String-Konstanten in
`App\Models\PersonContact`. Die zehn Listen enthalten damit insgesamt 68 Paare
(50 direkte String-Paare und 18 Paare mit Konstanten-Schlüssel). Wiederholte
Verwendungen in CreatePerson und EditPerson bleiben getrennte Nachweise.

Der Parser löst importierte Klassennamen/Aliase auf, verwendet aber ausschließlich
explizit gelieferte Quelldateien. Kein Autoload, keine Reflection, keine
Ausführung von Klassen oder Providern. `key_dependency` enthält Klasse,
Konstantenname, Wert, Datei, SHA256 und Zeile. Geänderte Konstanten ergeben einen
geänderten Wert und Nachweis; es gibt keinen stillen Rückgriff auf alte Werte.

Vererbte, nicht öffentliche, bedingt deklarierte oder wiederum aus Ausdrücken
berechnete Konstanten bleiben ungeklärt. Mehrdeutige Klassendefinitionen und
doppelte Optionsschlüssel (auch nach Konstantenauflösung) werden abgewiesen.
Dies ist bewusst eine begrenzte statische Erfassung, kein PHP-Auswerter.

12 Tests / 32 Assertions bestanden. Zusätzlich den erzeugten Bericht geprüft:
10 vollständige statische Defaults, 68 Paare, 18 Konstanten-Verwendungen mit
gültigem Quelldatei-Hash. Keine Behauptung vollständiger Laufzeitwerte,
keine automatische Übersetzungszuordnung oder DatabaseValue-Entscheidung.
Die Prüfvorschau bleibt unverändert; der Bericht ist noch kein Datenbankimport.

Inventur erfolgreich ausgeführt, neun Dateien erfasst. Aufrufverteilung gegen
den aktuellen verifizierten Review-Export gezählt: insgesamt 28. PHP/Pint und
Whitespace-Prüfung bestanden. Keine Browser- oder Datenbankänderung.
