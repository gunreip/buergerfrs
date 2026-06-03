# Do Not Change Without Agreement

Die folgenden Punkte gelten als fachlich abgestimmt und dürfen nicht stillschweigend umgebaut werden:

- Die Grundmenge der Translation-List basiert auf `translation_keys` als Work-Items.
- `Target Language` darf Missing-Fälle nicht ausblenden.
- Counter und Tabelle müssen immer dieselbe Row-Menge verwenden.
- `Suggested Key` ist der einzige sichtbare Standard-Vorschlag in der UI.
- `Expected Key` gehört nicht in die Standard-UI.
- `obsolete` ist Nebenworkflow und darf den normalen Übersetzungsworkflow nicht dominieren.
- Änderungen an Translation-List, Suggested-Key-Logik oder Translation-Commands nur nach erneuter fachlicher Abstimmung.

# Statusdefinitionen

Im aktuellen Workflow müssen vier Ebenen klar getrennt werden:

1. `translation_keys.status`
- Fachlicher Maschinenstatus des Work-Items.
- `ok`: Ein echter Translation-Key existiert im Code und ist im Sprachbestand vorhanden.
- `missing`: Ein echter Translation-Key existiert im Code, aber der Eintrag fehlt im Sprachbestand.
- `obsolete`: Ein Eintrag ist noch im Bestand vorhanden, aber im aktuellen Code gibt es keinen passenden Translation-Key mehr. Das ist der Fall bei Code-/Key-Änderungen, entfernten Features oder anderen Umbauten, durch die der bisherige Key nicht mehr benutzt werden kann.
- `native`: Im Code wurde Literaltext statt eines Translation-Keys gefunden.
- `dynamic`: Im Code wird ein dynamischer Ausdruck verwendet, aus dem kein stabiler konkreter Translation-Key abgeleitet werden kann.
- `invalid`: Der Übersetzungsaufruf ist leer, kaputt oder aus Auditsicht nicht verwertbar.

2. `translation_keys.workflow_status`
- Menschlicher Bearbeitungsstatus des Work-Items.
- `open`: Das Work-Item ist noch im aktiven Bearbeitungsfluss.
- `reviewed`: Das Work-Item wurde bewusst geprüft oder aus dem aktiven Arbeitskorb genommen.
- Wichtig: `workflow_status` ist bewusst getrennt von `status`. Ein Eintrag kann also `obsolete` und gleichzeitig `open` sein.

3. `translation_keys.classification`
- Art bzw. Herkunft des Work-Items.
- `key`: Echter konkreter Translation-Key.
- `native`: Literaltext-Fund im Code.
- `dynamic`: Dynamischer Übersetzungsaufruf.
- `invalid`: Ungültiger Übersetzungsaufruf.
- `vendor`: Repo-spezifische Sonderklassifikation für Vendor-Übersetzungen.
- `backfill_by_translation`: Repo-spezifische Sonderklassifikation für rückgefüllte Einträge auf Basis vorhandener Übersetzungswerte.

4. `translation_values.status`
- Status eines konkreten Locale-Werts.
- `ok`: Für diese Sprache existiert ein nichtleerer verwendbarer Wert.
- `missing`: Für diese Sprache fehlt der Wert oder ist leer.
- `obsolete`: Der konkrete Sprachwert gehört zu einem Key, der insgesamt `obsolete` ist.

Merksatz:
- `status` beschreibt den fachlichen Maschinenzustand.
- `workflow_status` beschreibt den menschlichen Bearbeitungszustand.
- `classification` beschreibt die Art bzw. Herkunft des Eintrags.
- `translation_values.status` beschreibt den Zustand je Sprache.

## UI-Arbeitsbedeutung

Die Translation-List trennt zusätzlich zwischen aktueller Arbeitsmenge und historischer Restmenge:

- `Workflow > All`: Alle aktuell code-relevanten Translation-Einträge. Diese Zahl ändert sich, wenn neuer Code Translation-Stellen hinzufügt oder entfernt.
- `Workflow > Open`: Aktuell code-relevante Einträge, die noch offen im Workflow sind.
- `Workflow > Reviewed`: Aktuell code-relevante Einträge, die bereits reviewed markiert wurden.
- `Workflow > History`: Reviewte Historieneinträge, auch wenn sie nicht mehr zur aktuellen code-relevanten Arbeitsmenge gehören.
- `Workflow > Completed`: Aktuell code-relevante Einträge mit `status = ok`.
- `Status > OK`: Bereits stimmige und aktiv nutzbare Übersetzungen in den `lang/*`-Dateien.
- `Status > Native`: Literaltexte, die noch reviewed und meist durch echte TranslationKeys ersetzt werden müssen.
- `Status > Dynamic`: Laufzeit-dynamisch gebildete Spezialfälle, die häufig bewusst dynamisch bleiben.
- `Status > Obsolete`: Nicht mehr aktuell genutzte Einträge; fachlich gehören diese in `History` oder `Archive`.
- `Status > Invalid`: Inhaltlich oder technisch fehlerhafte Fälle, die auf jeden Fall korrigiert werden müssen.
- `Type > Archive`: Einträge ohne aktuelle Code-Relevanz. Sie gehören nicht zur aktiven Übersetzungsarbeit, bleiben aber für Nachvollziehbarkeit erhalten.

# Beschreib nochmal genau nach welchen Regeln die suggested-keys erstellt werden?

Im aktuellen Workflow werden Suggested Keys nicht überall auf dieselbe Weise erzeugt. Relevant sind hier zwei Pfade:

1. Für echte vorhandene Translation-Keys aus dem Compare-Audit
- Das passiert in SyncTranslationAudits.php in `suggestKeyForExistingKey(...)`.
- Diese Funktion normalisiert einen vorhandenen Key nach festen Regeln:

Regeln im Detail:
1. Trim:
   Führende und abschließende Leerzeichen werden entfernt.
2. Pfadtrenner vereinheitlichen:
   `\` und `/` werden zu `.`
3. Bindestriche ersetzen:
   `-` wird zu `_`
4. CamelCase auflösen:
   Großbuchstaben mitten im Wort bekommen ein vorangestelltes `_`
   Beispiel: `confirmPassword` wird zu `confirm_Password`
5. Alles klein schreiben:
   Danach wird komplett in lowercase umgewandelt
6. Unerlaubte Zeichen bereinigen:
   Alles außer `a-z`, `0-9`, `_` und `.` wird zu `_`
7. Mehrfache Unterstriche zusammenfassen:
   `___` wird zu `_`
8. Mehrfache Punkte zusammenfassen:
   `...` wird zu `.`
9. Unterstriche direkt an Segmentgrenzen entfernen:
   Also `._foo`, `foo_.`, `foo_` werden bereinigt
10. Abschließende und führende `.` oder `_` entfernen

Praktisch heißt das:
- `Layouts/Sidebar-Administration/Countries` wird zu `layouts.sidebar_administration.countries`
- `pages.auth.confirmPassword.confirm-password` wird zu `pages.auth.confirm_password.confirm_password`

2. Für Native- und Dynamic-Einträge
- In SyncTranslationAudits.php und SyncTranslationAudits.php wird `suggested_key` nicht lokal berechnet, sondern direkt aus dem Audit übernommen:
  `suggestedKey: $entry['suggested_key'] ?? null`
- Das heißt:
  Für `native` und `dynamic` entsteht der Suggested Key schon früher im Audit-Prozess, und `translations:sync-audits` übernimmt ihn nur in die Datenbank.

Wichtig für dein aktuelles Verhalten:
- Wenn du im Review den Suggested Key übernimmst, wird daraus jetzt:
  - `key = suggested_key`
  - `classification = key`
  - `status` wird auf Key-Logik normalisiert
- Spätere `sync-audits` behalten diesen gesetzten Key jetzt bei.

Wenn du willst, kann ich dir als Nächstes noch die Suggested-Key-Erzeugung für Native/Dynamic upstream im Audit-Code genau aufdröseln, also nicht nur die Sync-Übernahme, sondern die eigentliche Herkunft dieser Werte.

# Ja

Ich verfolge jetzt die Suggested-Key-Erzeugung upstream im Audit-Code, damit ich dir nicht nur die Sync-Übernahme, sondern die tatsächliche Herkunft für `native` und `dynamic` präzise erklären kann.

Searched for regex `suggested_key|suggestedKey|suggestKeyFor|literal.*key|generate.*key` (`**/app/Console/Commands/**/*.php`), 20 results

Searched for regex `suggested_key|suggestedKey` (`**/app/**/*.php`), 20 results

Searched for files matching `**/app/Console/Commands/*.php`, 26 matches

Die relevante Quelle liegt in TranslationsAuditCode.php; ich lese jetzt genau die Stelle, an der für `native` Suggested Keys aufgebaut werden, und prüfe außerdem, ob `dynamic` dort absichtlich keinen Vorschlag bekommt.

Searched for regex `private function suggest|buildSuggested|suggested_key|classification === 'native'|classification === 'dynamic'` (`**/app/Console/Commands/TranslationsAuditCode.php`), 3 results

Read [](file:///home/gunreip/code/buergerfrs/app/Console/Commands/TranslationsAuditCode.php#80-80), lines 80 to 180

Read [](file:///home/gunreip/code/buergerfrs/app/Console/Commands/TranslationsAuditCode.php#180-180), lines 180 to 360

Read [](file:///home/gunreip/code/buergerfrs/app/Console/Commands/TranslationsAuditCode.php#356-356), lines 356 to 460

Für `native` und `dynamic` läuft die Suggested-Key-Erzeugung upstream so:

In TranslationsAuditCode.php wird beim Scan jeder gefundene Translation-Call zuerst klassifiziert:
- `key`, wenn der String schon wie ein echter Translation-Key aussieht
- `native`, wenn es ein Literaltext ist, aber kein Key
- `dynamic`, wenn der erste Parameter nicht als Literalstring vorliegt
- `invalid`, wenn der Wert leer ist

Die eigentliche Regel für Suggested Keys ist dann sehr strikt:
- Nur bei `classification === 'native'` wird ein `suggested_key` erzeugt
- Bei `dynamic` wird bewusst `null` gesetzt
- `sync-audits` übernimmt diesen Wert später nur noch in die DB, siehe SyncTranslationAudits.php und SyncTranslationAudits.php

Die Erzeugung selbst passiert in TranslationsAuditCode.php in `suggestKey(...)` und besteht aus zwei Teilen:

1. Namespace aus dem Dateipfad
- Die Basis kommt aus `namespaceFromPath(...)` in TranslationsAuditCode.php
- Regeln:
  - Verzeichnispräfixe werden abgeschnitten:
    - components
    - livewire
    - views
    - Livewire
    - app
    - routes
  - Dateiendungen `.blade.php` und `.php` werden entfernt
  - Das Zeichen `⚡` wird entfernt
  - `/` wird zu `.`
  - `-` wird zu `_`
  - CamelCase wird mit `_` getrennt
  - alles wird lowercase
  - führende/trailing Punkte werden entfernt
  - `_` direkt an Segmentgrenzen wird bereinigt

Beispiel:
- confirm-password.blade.php
  wird ungefähr zu
  `pages.auth.confirm_password`

2. Slug aus dem Literaltext
- Der Textteil wird in `suggestKey(...)` so normalisiert:
  - lowercase
  - alles außer `a-z` und `0-9` wird zu `_`
  - führende und abschließende `_` werden entfernt
  - auf 80 Zeichen begrenzt
  - wenn danach leer: Fallback `text`

Beispiel:
- `"Confirm Password"` wird zu `confirm_password`
- `"Administration / Countries"` wird zu `administration_countries`

Das Endergebnis ist dann:
- `suggested_key = namespace + '.' + slug`

Beispiel:
- Datei: confirm-password.blade.php
- Literal: `"Confirm Password"`
- Suggested Key:
  `pages.auth.confirm_password.confirm_password`

Wichtig für `dynamic`:
- Ein Aufruf wie `__($someVariable)` oder `trans($keyFromConfig)` bekommt keinen Suggested Key.
- Grund: Der Audit-Code kennt nur den Ausdruck, nicht den endgültigen Laufzeitwert.
- Deshalb wird `dynamic` nur markiert, aber nicht automatisch vorgeschlagen.

Und für bereits vorhandene echte Keys:
- Dort wird nicht `suggestKey(...)` verwendet, sondern `suggestKeyForExistingKey(...)` in SyncTranslationAudits.php
- Das ist die andere Regelkette, die einen bestehenden Key nur normalisiert.

Wenn du willst, schreibe ich dir das als kurze, saubere Doku in key-rules.md.

## Beispiele

| Input | Pfad / Typ | Ergebnis |
| --- | --- | --- |
| `Confirm Password` | `resources/views/pages/auth/confirm-password.blade.php` | `pages.auth.confirm_password.confirm_password` |
| `Sidebar-Administration/Countries` | bestehender Key | `sidebar_administration.countries` |
| `__($labelFromConfig)` | dynamisch | kein Suggested Key |

