Für das RFC-Projekt sollte der Entwicklungsbot nicht nur allgemeine „Laravel Best Practices“ erhalten. Er braucht ein verbindliches Regelwerk, das Architektur, Arbeitsweise, Sicherheitsgrenzen und das fachliche RFC-Modell schützt.

Ich gehe dabei von einem Coding-Agenten aus, der Quellcode analysiert, Änderungen vorbereitet, Tests ausführt und nach Freigabe implementiert. Ein späterer Moderations- oder Diskussionsbot innerhalb der Anwendung bräuchte ein eigenes, deutlich restriktiveres Regelwerk.

## 1. Sinnvolle Aufteilung der Anweisungen

Nicht alle Regeln sollten in einer einzigen riesigen Datei stehen.

| Datei                      | Inhalt                                                   | Verbindlichkeit        |
| -------------------------- | -------------------------------------------------------- | ---------------------- |
| `AGENTS.md`                | Arbeitsweise, Verbote, Freigaben, Qualitätsanforderungen | verbindlich            |
| `docs/architecture.md`     | Architektur, Module, Schichten, Abhängigkeiten           | verbindlich            |
| `docs/domain/rfc-model.md` | RFC-Lebenszyklus und fachliche Invarianten               | verbindlich            |
| `docs/security.md`         | Autorisierung, Audit, Datenschutz, Uploads               | verbindlich            |
| `docs/testing.md`          | Pest-Strategie und erforderliche Testarten               | verbindlich            |
| `docs/ui-conventions.md`   | Flux-Pro-, Livewire- und Barrierefreiheitsregeln         | verbindlich            |
| `docs/i18n.md`             | Sprachen, Übersetzungen und Fallbacks                    | verbindlich            |
| `docs/operations.md`       | Deployment, Queue, Scheduler, Backups                    | verbindlich            |
| `docs/decisions/`          | Architecture Decision Records, kurz ADR                  | historisch verbindlich |
| `README.md`                | Installation und schneller Einstieg                      | informativ             |

Die `AGENTS.md` sollte kurz genug bleiben, dass der Bot sie bei jedem Auftrag vollständig berücksichtigen kann. Einzelheiten werden in den genannten Dokumenten nachgeschlagen.

---

# 2. Unverzichtbare Regeln in der `AGENTS.md`

## 2.1 Projektgrundlage

Der Bot muss die festgelegte technische Basis kennen:

- PHP 8.5
- Laravel 13
- Livewire 4
- Flux Pro 2
- Tailwind CSS 4
- PostgreSQL
- Redis für Queues und gegebenenfalls Cache
- Pest 4 mit Pest Laravel
- klassische autoinkrementierende BIGINT-Primärschlüssel
- keine UUID- oder ULID-Primärschlüssel
- Spatie Permission ohne Teams-Modus
- mehrsprachige Oberfläche
- Standardsprache Deutsch
- Speicherung von Zeitpunkten in UTC
- Anzeige in `Europe/Berlin`, sofern der Benutzer keine andere Zeitzone verwendet

Wichtig ist außerdem:

> Die Anwendung ist ein modular gegliederter Laravel-Monolith. Der Bot darf keine Microservices, alternative Frontend-Frameworks oder parallele Komponentenbibliotheken einführen.

---

## 2.2 Priorität der Quellen

Der Bot benötigt eine klare Rangfolge bei Widersprüchen:

1. aktuelle Benutzeranweisung,
2. freigegebenes Pflichtenheft,
3. `AGENTS.md`,
4. freigegebene ADRs,
5. Fach- und Architekturunterlagen,
6. bestehende Tests,
7. bestehender Quellcode,
8. Framework-Konventionen.

Dabei sollte gelten:

- Ein bestehender Codezustand darf nicht automatisch als fachlich richtig behandelt werden.
- Widerspricht der Code dem Pflichtenheft, muss der Bot den Widerspruch melden.
- Der Bot darf einen Widerspruch nicht stillschweigend zugunsten einer Seite auflösen.
- Sicherheits- und Datenschutzregeln dürfen nicht durch eine bequemere Implementierung abgeschwächt werden.

---

## 2.3 Arbeitsweise

Folgende Regeln sollten verbindlich sein:

- Vor jeder Änderung ist der aktuelle Stand der betroffenen Dateien zu lesen.
- Es darf nicht mit veralteten Dateiausschnitten gearbeitet werden.
- Bestehende, nicht zum Auftrag gehörende Änderungen bleiben unangetastet.
- Änderungen müssen minimalinvasiv bleiben.
- Keine beiläufigen Umbenennungen, Formatierungen oder Refactorings.
- Neue Laravel-Artefakte werden, soweit möglich, mit `php artisan make:*` erzeugt.
- Datenbankänderungen erfolgen ausschließlich durch neue Migrationen.
- Bereits ausgeführte Migrationen werden nicht nachträglich verändert.
- Composer- und npm-Abhängigkeiten dürfen nur bei fachlicher Notwendigkeit ergänzt werden.
- Major-Upgrades benötigen eine ausdrückliche Freigabe.
- Lock-Dateien müssen bei Abhängigkeitsänderungen konsistent aktualisiert werden.
- Der Bot darf keine Secrets, Tokens, Zugangsdaten oder personenbezogenen Produktionsdaten ausgeben oder committen.
- Produktionsänderungen, Deployments und Datenmigrationen benötigen eine ausdrückliche Beauftragung.

---

# 3. Freigabe- und Änderungsprotokoll

Für dieses Projekt würde ich den bestehenden kontrollierten Änderungsprozess übernehmen.

## Analyseauftrag

Wenn nur eine Prüfung oder Diagnose verlangt wird, darf der Bot:

- Dateien lesen,
- Tests und statische Analysen ausführen,
- Fehlerursachen bestimmen,
- Änderungsvorschläge erstellen.

Er darf die vorgeschlagene Änderung aber noch nicht implementieren.

## Änderungsvorschlag

Vor der Implementierung sollte der Bot liefern:

- betroffene Datei,
- genaue Fundstelle,
- byte-identischen IST-Ausschnitt,
- vorgeschlagenen SOLL-Ausschnitt,
- fachliche Wirkung,
- technische Wirkung,
- mögliche Nebenwirkungen,
- erforderliche Tests,
- gegebenenfalls Migrations- oder Rollbackfolgen.

## Umsetzung

Erst nach Freigabe:

- Änderungen anwenden,
- Tests ausführen,
- Ergebnisse melden,
- keine zusätzlich entdeckten Probleme ungefragt „mitreparieren“.

Eine Ausnahme kann für rein mechanische, ausdrücklich beauftragte Änderungen gelten. Diese Ausnahme sollte ebenfalls in der `AGENTS.md` definiert werden.

---

# 4. Fachliche Regeln des RFC-Systems

Diese Regeln sind besonders wichtig, weil ein Coding-Agent sonst leicht eine technisch plausible, aber fachlich falsche Abkürzung baut.

## 4.1 RFC und Versionen

Der Bot muss beachten:

- Ein RFC besitzt eine stabile Identität.
- Eine RFC-Version ist eine eigenständige, adressierbare Fassung.
- Veröffentlichte Versionen sind unveränderlich.
- Inhaltliche Änderungen erzeugen eine neue Version.
- Alte Versionen dürfen nicht überschrieben oder gelöscht werden.
- Jede Veröffentlichung erhält mindestens:
  - Versionsnummer,
  - Ersteller,
  - Freigebenden,
  - Veröffentlichungszeitpunkt,
  - Inhaltshash,
  - Quellsprache.
- Abschnittsidentitäten müssen über Versionen hinweg nachvollziehbar bleiben.
- Kommentare müssen erkennen lassen, auf welche Version und welchen Abschnitt sie sich beziehen.

## 4.2 Statussystem

Statusänderungen dürfen nicht als beliebiges Setzen einer Zeichenkette implementiert werden.

Jeder Übergang benötigt:

- erlaubten Ausgangsstatus,
- erlaubten Zielstatus,
- erforderliche Rolle,
- fachliche Vorbedingungen,
- gegebenenfalls Begründung,
- Audit-Ereignis,
- atomare Datenbanktransaktion.

Der Bot darf keinen universellen Aufruf wie diesen einführen:

```php
$rfc->update(['status' => $status]);
```

Stattdessen braucht es fachlich benannte Aktionen, beispielsweise:

```php
PublishRfcVersion
OpenRfcDiscussion
BeginFinalCommentPeriod
MarkRfcAsDecisionReady
RecordRfcDecision
```

## 4.3 Diskussion und Beschluss

Verbindliche Invarianten:

- Diskussion und formelle Entscheidung bleiben getrennt.
- Eine Online-Abstimmung ist standardmäßig nur ein Meinungsbild.
- Ein Meinungsbild verändert den formellen RFC-Status nicht automatisch.
- Nur ein zuständiges Entscheidungsorgan darf eine formelle Entscheidung erfassen.
- Die Entscheidung muss sich auf eine konkrete RFC-Version beziehen.
- Minderheitsvoten bleiben erhalten.
- Abgelehnte Alternativen und wesentliche Einwände müssen nachvollziehbar bleiben.

## 4.4 Änderungsanträge

Ein Änderungsantrag benötigt:

- eine konkrete Basisversion,
- betroffene Abschnitte,
- Ausgangstext,
- vorgeschlagenen Zieltext,
- Begründung,
- Status,
- Antragsteller,
- Entscheidung und Begründung.

Eine Übernahme darf niemals die veröffentlichte Basisversion ändern.

## 4.5 Moderation

Der Bot muss unterscheiden zwischen:

- politischer Redaktion,
- technischer Administration,
- Moderation,
- Fachprüfung,
- formeller Entscheidung.

Moderationsinhalte dürfen nicht physisch gelöscht werden, solange rechtliche oder dokumentarische Aufbewahrungsgründe bestehen. Öffentlich können sie ausgeblendet werden, intern bleiben Maßnahme und Begründung nachvollziehbar.

---

# 5. Laravel-Architekturregeln

## 5.1 Schichten

Eine Schreiboperation sollte grundsätzlich diesen Weg nehmen:

```text
Livewire-Komponente
→ Validierung
→ Policy/Gate
→ fachlich benannte Action
→ Datenbanktransaktion
→ Domain Event
→ asynchrone Nacharbeiten
```

Verbindliche Regeln:

- Livewire-Komponenten koordinieren UI-Zustand, enthalten aber keine umfangreiche Fachlogik.
- Fachliche Schreiboperationen gehören in Actions oder Services.
- Autorisierung findet serverseitig statt.
- Policies prüfen sowohl Rollen als auch fachliche Zuständigkeit.
- Eloquent-Modelle dürfen Invarianten schützen, sollen aber nicht zu unübersichtlichen „God Models“ werden.
- Mehrere zusammengehörende Schreiboperationen laufen in einer Transaktion.
- E-Mails, Exporte, Indexierung und umfangreiche Benachrichtigungen laufen über Queues.
- Queue-Jobs müssen idempotent sein.
- Externe Aufrufe dürfen keine offene Datenbanktransaktion unnötig verlängern.
- Fachliche Zustände werden durch Enums oder vergleichbar sichere Typen dargestellt.
- Erlaubte Statusübergänge gehören an eine zentrale, testbare Stelle.

## 5.2 Keine unnötigen Abstraktionen

Der Bot sollte ausdrücklich angewiesen werden:

- Keine Interfaces ohne tatsächlichen Austausch- oder Testzweck.
- Keine Repository-Schicht nur zur Verpackung von Eloquent.
- Keine generischen CRUD-Services für fachlich unterschiedliche Vorgänge.
- Keine Event-Sourcing-Architektur ohne gesonderte Entscheidung.
- Keine Microservices.
- Keine selbst entwickelte Authentifizierung.
- Keine eigene UI-Bibliothek.
- Keine vorauseilende Generalisierung für hypothetische Anforderungen.

---

# 6. Datenbankregeln

Verbindliche Regeln:

- Primär- und Fremdschlüssel sind BIGINT.
- Fremdschlüssel werden tatsächlich in PostgreSQL angelegt.
- Eindeutigkeit wird nicht nur in PHP, sondern mit Unique Constraints abgesichert.
- Fachlich begrenzte Werte erhalten Check Constraints, soweit praktikabel.
- JSONB wird nur für flexible, nicht relationale Zusatzdaten verwendet.
- Rollen, Berechtigungen, Statusübergänge und sicherheitsrelevante Daten gehören nicht in allgemeine JSONB-Einstellungen.
- Benutzerpräferenzen dürfen in einem kleinen, strukturierten JSONB-Feld liegen.
- Zeitpunkte werden als echte Zeitstempel gespeichert, nicht als formatierte Zeichenketten.
- Geldbeträge werden niemals als Float gespeichert.
- Indizes müssen aus realen Filter-, Sortier- und Beziehungszugriffen abgeleitet werden.
- PostgreSQL-spezifisches Verhalten wird mit PostgreSQL getestet.
- SQLite ist nicht die alleinige Grundlage für Integrationstests.
- N+1-Abfragen sind zu vermeiden und für Kernseiten zu kontrollieren.
- Soft Deletes werden nur nach fachlicher Entscheidung eingesetzt.
- RFC-Versionen, Entscheidungen und Audit-Ereignisse dürfen nicht soft-deleted werden.

---

# 7. Livewire- und Flux-Pro-Regeln

## Unverzichtbar

- Die Anwendungsoberfläche wird ausschließlich mit Livewire und Flux Pro umgesetzt.
- Kein React, Vue, Inertia oder zusätzliches UI-Framework.
- Standardsteuerelemente werden mit Flux-Komponenten gebaut.
- Eigene Blade-Komponenten dürfen Flux kapseln.
- Alpine.js ist nur für lokale, kurzlebige Darstellungseffekte zulässig.
- Der kanonische fachliche Zustand verbleibt in Livewire beziehungsweise auf dem Server.
- Keine doppelte Zustandsführung in Livewire und Alpine.
- Keine Geschäftslogik in JavaScript.
- UI-Texte werden immer übersetzt.
- Native HTML5-Validierung ist nicht die primäre Fehlerbehandlung.
- Fehler erscheinen:
  - direkt am betroffenen Feld,
  - zusätzlich in einer fokussierbaren Fehlerübersicht,
  - mit Möglichkeit zum Anspringen des Feldes.
- Erfolgsmeldungen verwenden Flux-Toast.
- Kritische Aktionen verwenden bestätigungspflichtige Flux-Modals.
- Tabellen bieten, soweit relevant:
  - Suche,
  - Filter,
  - sichere Sortier-Whitelist,
  - Seitengrößen 10, 25, 50 und 100,
  - `resetPage()` nach Filteränderungen.

## Barrierefreiheit

Der Bot muss bei jeder UI-Änderung prüfen:

- Tastaturbedienbarkeit,
- sichtbarer Fokus,
- sinnvolle Fokusreihenfolge,
- Label-Zuordnung,
- Überschriftenhierarchie,
- Statusmeldungen über Live-Regionen,
- ausreichende Kontraste,
- Information nicht nur über Farben,
- Fokusfang und Fokusrückgabe bei Modalen,
- verständliche Kennzeichnung von Diff-Ergänzungen und -Löschungen.

---

# 8. Mehrsprachigkeitsregeln

## Oberfläche

- Alle sichtbaren Texte laufen über `__()` oder die festgelegte Übersetzungsschicht.
- Keine fest codierten deutschen Texte in Blade, Livewire, PHP oder JavaScript.
- Übersetzungsschlüssel sind stabil und fachlich gruppiert.
- Validierungs-, E-Mail-, Benachrichtigungs- und Audittexte sind ebenfalls übersetzbar.
- Fallback-Verhalten wird explizit getestet.

## RFC-Inhalte

- Jede RFC-Version hat eine Quellsprache.
- Übersetzungen beziehen sich auf eine konkrete Quellversion.
- Eine neue Quellversion überschreibt keine bestehende Übersetzung.
- Übersetzungen werden bei geänderter Quelle als veraltet markiert.
- Übersetzungsstatus sind beispielsweise:
  - `missing`
  - `draft`
  - `review`
  - `published`
  - `outdated`
- Sprachunabhängige Permalinks müssen erhalten bleiben.
- Eine Übersetzung darf nicht wie ein unabhängiger politischer Beschluss behandelt werden.

---

# 9. Autorisierung und Sicherheitsregeln

## Autorisierung

Der Bot muss für jede schreibende Aktion beantworten:

1. Wer darf die Aktion ausführen?
2. Für welche Gliederung oder welchen RFC?
3. In welchem Status?
4. Innerhalb welcher Frist?
5. Welche fachlichen Vorbedingungen gelten?
6. Welches Audit-Ereignis entsteht?

Zusätzlich:

- UI-Sichtbarkeit ersetzt keine Policy.
- Jede Policy erhält positive und negative Tests.
- Administratoren erhalten nicht automatisch politische Entscheidungsrechte.
- Spatie-Rollen reichen allein nicht für objektbezogene Zuständigkeiten.
- Kritische Aktionen erfordern gegebenenfalls eine erneute Authentifizierung.
- 2FA ist für privilegierte Rollen verpflichtend.

## Sichere Entwicklung

- Keine dynamisch aus Benutzereingaben aufgebauten SQL-Fragmente.
- Sortierfelder ausschließlich über Whitelists.
- Eingabedaten werden validiert und normalisiert.
- Mass Assignment wird bewusst kontrolliert.
- Uploads werden nach Größe, Inhaltstyp und Dateistruktur geprüft.
- Uploads liegen nicht in ausführbaren Verzeichnissen.
- Dateinamen werden nicht ungeprüft übernommen.
- Rate Limits für Login, Passwort-Reset, Kommentare, Meldungen, Unterstützung, Suche und Exporte.
- Keine sensiblen Daten in Logs.
- Keine vollständigen Request-Payloads bei politischen oder personenbezogenen Inhalten protokollieren.
- Fehlerausgaben dürfen in Produktion keine Interna zeigen.

---

# 10. Audit-Regeln

Das normale Activity Log reicht für die fachlich kritische Historie nicht allein aus.

Verbindlich zu auditieren sind mindestens:

- RFC-Erstellung,
- Veröffentlichung einer Version,
- Statusübergänge,
- Friständerungen,
- Rollen- und Berechtigungsänderungen,
- Änderungsantragsentscheidungen,
- Prüfergebnisse,
- Moderationsmaßnahmen,
- Beschwerden,
- Meinungsbilder,
- formelle Entscheidungen,
- Minderheitsvoten,
- Exporte,
- sicherheitsrelevante Kontovorgänge.

Ein Audit-Ereignis sollte enthalten:

- Ereignistyp,
- Akteur,
- betroffenes Objekt,
- Objektversion,
- Zeitpunkt,
- Request-/Correlation-ID,
- fachliche Begründung,
- vorherigen und neuen Zustand oder deren Referenzen,
- Integritätsinformation.

Der Bot darf keine Funktion entwickeln, die Audit-Ereignisse regulär bearbeitet oder löscht.

---

# 11. Pest- und Testregeln

Für jede Änderung muss der Bot prüfen, welche Testebenen betroffen sind.

## Verbindliche Testarten

- Unit-Tests für isolierte Regeln
- Feature-Tests für HTTP- und Anwendungsabläufe
- Livewire-Komponententests
- Policy- und Berechtigungstests
- PostgreSQL-Integrationstests
- Architekturtests
- Browser-Tests für kritische Abläufe
- Regressionstest für jeden behobenen Fehler

## Testregeln

- Jeder Berechtigungstest benötigt mindestens einen positiven und einen negativen Fall.
- Zeitabhängige Tests verwenden eine eingefrorene Uhr.
- Externe Dienste werden kontrolliert gefälscht.
- Keine echten E-Mails, HTTP-Aufrufe oder Benachrichtigungen in Tests.
- Queue-Verhalten und Idempotenz werden getestet.
- Veröffentlichte Versionen benötigen Unveränderlichkeitstests.
- Statusübergänge benötigen Tests aller erlaubten und verbotenen Übergänge.
- Parallelitätsgefährdete Vorgänge benötigen Datenbank- oder Concurrency-Tests.
- Tests dürfen nicht von ihrer Ausführungsreihenfolge abhängen.
- Ein behobener Fehler gilt ohne Regressionstest nicht als abgeschlossen.
- Eine hohe Coverage-Zahl ersetzt keine fachlichen Szenarien.

## Mindestprüfungen vor Abschluss

```bash
composer test
vendor/bin/pint --test
vendor/bin/phpstan analyse
npm run build
php artisan migrate:fresh --seed --env=testing
```

Die tatsächlichen Befehle sollten zentral in Composer-Skripten oder CI-Jobs hinterlegt werden, damit Bot, Entwickler und CI identische Prüfungen verwenden.

---

# 12. Umgang mit Migrationen

Der Bot benötigt hierfür besonders strenge Regeln:

- Niemals eine produktiv verwendete Migration ändern.
- Keine Spalten löschen oder umbenennen, ohne Datenmigration und Rückfallbewertung.
- Neue NOT-NULL-Spalten auf bestehenden Tabellen benötigen einen sicheren Einführungsweg.
- Große Datenänderungen nicht unkontrolliert innerhalb einer blockierenden Schema-Migration ausführen.
- Vor riskanten Änderungen:
  - betroffene Datenmenge bestimmen,
  - Lock-Risiko bewerten,
  - Rückfallweg beschreiben,
  - produktionsnahe Probe durchführen.
- Änderungen mit mehreren Stufen verwenden bei Bedarf:
  - Expand,
  - Daten migrieren,
  - Anwendung umstellen,
  - Contract.
- Migration, Model-Casts, Validierung und Tests müssen zusammenpassen.

---

# 13. Verbotene Eigenmächtigkeiten

Dieser Abschnitt sollte sehr deutlich formuliert werden.

Der Bot darf ohne ausdrückliche Freigabe nicht:

- Pakete installieren oder entfernen,
- Major-Versionen aktualisieren,
- Datenbanktabellen oder produktive Daten löschen,
- Migrationen rückwirkend ändern,
- Rollen oder Berechtigungen erweitern,
- Authentifizierungsabläufe verändern,
- eine neue UI-Bibliothek einführen,
- Livewire durch ein JavaScript-Framework ersetzen,
- veröffentlichte RFC-Versionen veränderbar machen,
- Audit- oder Moderationsdaten löschen,
- Meinungsbilder als formelle Beschlüsse behandeln,
- Datenschutzfristen erfinden,
- die endgültige Domain festlegen,
- Deployment- oder Produktionszugriffe durchführen,
- externe Nachrichten oder E-Mails versenden,
- Tests abschwächen oder entfernen, nur damit eine Änderung „grün“ wird,
- fachfremde Probleme ungefragt mitändern.

Wenn eine notwendige Entscheidung fehlt, muss der Bot anhalten und die konkrete Entscheidung benennen.

---

# 14. Regeln für Diagnose und Fehlerbehebung

Der Bot sollte zwischen Diagnose und Umsetzung unterscheiden.

Bei einer Diagnose muss er liefern:

- beobachtetes Verhalten,
- reproduzierbaren Ablauf,
- erwartetes Verhalten,
- tatsächliches Verhalten,
- betroffene Schicht,
- nachgewiesene Ursache,
- relevante Logs oder Testergebnisse,
- minimalen Lösungsvorschlag,
- Risiken der Änderung.

Er darf eine Vermutung nicht als Ursache ausgeben.

Formulierungen sollten entsprechend unterscheiden:

- „nachgewiesen“,
- „durch Test bestätigt“,
- „wahrscheinliche Ursache“,
- „noch nicht geprüft“.

---

# 15. Definition of Done

Eine Änderung ist nur abgeschlossen, wenn:

- die Anforderung eindeutig erfüllt ist,
- die Änderung auf dem aktuellen Stand basiert,
- alle betroffenen Berechtigungen geprüft wurden,
- positive und negative Tests vorhanden sind,
- Pest vollständig erfolgreich ist,
- statische Analyse erfolgreich ist,
- Pint keine Abweichungen meldet,
- der Frontend-Build erfolgreich ist,
- Übersetzungen vollständig sind,
- Barrierefreiheit berücksichtigt wurde,
- Audit-Auswirkungen geprüft wurden,
- Migrationen und Rollbackrisiken bewertet wurden,
- keine Secrets oder Debug-Ausgaben enthalten sind,
- Dokumentation aktualisiert wurde,
- keine nicht beauftragten Änderungen enthalten sind.

---

# 16. Empfohlener Kerntext für die `AGENTS.md`

Folgender kompakter Kern wäre für das Projekt geeignet:

```md
# AGENTS.md

## Auftrag

Diese Anwendung implementiert ein versioniertes politisches RFC-Verfahren.
Diskussion, Meinungsbild und formeller Parteibeschluss sind strikt getrennt.
Veröffentlichte RFC-Versionen, Entscheidungen, Minderheitsvoten und
Audit-Ereignisse bleiben dauerhaft nachvollziehbar.

## Technische Basis

- PHP 8.5
- Laravel 13
- Livewire 4
- Flux Pro 2
- Tailwind CSS 4
- PostgreSQL
- Redis-Queues
- Pest 4
- BIGINT-Primärschlüssel
- keine UUID-/ULID-Primärschlüssel
- Spatie Permission ohne Teams-Modus

Die Anwendung bleibt ein modularer Laravel-Monolith.
Keine zusätzlichen Frontend- oder UI-Frameworks einführen.

## Arbeitsweise

1. Vor Änderungen aktuelle Dateien und relevante Dokumentation lesen.
2. Nur die beauftragte Änderung bearbeiten.
3. Bestehende fremde Änderungen nicht verändern.
4. Neue Laravel-Artefakte mit `artisan make:*` erzeugen.
5. Schemaänderungen nur durch neue Migrationen.
6. Keine Pakete, Major-Upgrades oder Architekturwechsel ohne Freigabe.
7. Diagnoseaufträge berechtigen nicht automatisch zur Implementierung.
8. Fehlende fachliche Entscheidungen nicht selbst treffen.
9. Sicherheits-, Datenschutz- oder Berechtigungsregeln nicht abschwächen.
10. Keine Secrets oder personenbezogenen Produktionsdaten ausgeben.

## Fachliche Invarianten

- Veröffentlichte RFC-Versionen sind unveränderlich.
- Änderungen erzeugen eine neue Version.
- Jeder Statusübergang wird autorisiert, validiert und auditiert.
- Kommentare und Änderungsanträge referenzieren konkrete Versionen.
- Meinungsbilder sind nicht automatisch formelle Beschlüsse.
- Entscheidungen referenzieren eine konkrete RFC-Version.
- Minderheitsvoten bleiben erhalten.
- Moderation, Redaktion, Fachprüfung, Administration und Entscheidung
  sind getrennte Verantwortlichkeiten.
- Kritische Fachereignisse werden append-only auditiert.

## Architektur

Schreiboperationen folgen grundsätzlich:

Livewire → Validierung → Policy → Action → Transaktion
→ Domain Event → Queue-Nacharbeit

Livewire-Komponenten enthalten keine umfangreiche Fachlogik.
Alpine verwaltet nur lokalen Darstellungszustand.
Jede schreibende Aktion benötigt serverseitige Autorisierung.

## UI

- ausschließlich Flux Pro für Standardkomponenten
- alle UI-Texte über Übersetzungsschlüssel
- Feldfehler plus fokussierbare Fehlerübersicht
- Flux-Toast für Erfolg
- Flux-Modal für kritische Bestätigungen
- Tastaturbedienung und WCAG-2.2-AA-Anforderungen berücksichtigen
- Information nie ausschließlich durch Farbe vermitteln

## Datenbank

- PostgreSQL ist verbindlich
- Fremdschlüssel und fachliche Constraints in der Datenbank absichern
- JSONB nicht für relationale oder sicherheitskritische Daten missbrauchen
- keine Geldbeträge als Float
- UTC für gespeicherte Zeitpunkte
- keine Änderung bereits ausgeführter Migrationen
- keine Löschung veröffentlichter Versionen, Entscheidungen oder Audits

## Tests

Jede Änderung benötigt passende Pest-Tests.
Berechtigungen immer positiv und negativ testen.
Jeder Bugfix benötigt einen Regressionstest.
Zeit in Tests einfrieren.
Keine realen externen Dienste verwenden.
PostgreSQL-spezifisches Verhalten mit PostgreSQL testen.

Vor Abschluss mindestens:

- `composer test`
- `vendor/bin/pint --test`
- statische Analyse
- `npm run build`

Fehlgeschlagene Prüfungen dürfen nicht verschwiegen oder durch
Abschwächung bestehender Tests umgangen werden.

## Abschlussbericht

Nenne:

- geänderte Dateien,
- fachliche Wirkung,
- ausgeführte Prüfungen,
- konkrete Testergebnisse,
- nicht ausgeführte Prüfungen mit Grund,
- verbleibende Risiken oder offene Entscheidungen.
```

## 17. Zusätzlich empfehlenswert: maschinenprüfbare Regeln

Textanweisungen allein reichen nicht. Kritische Regeln sollten technisch durchgesetzt werden:

- Pest-Architekturtests für Schichtgrenzen
- Datenbank-Constraints für fachliche Integrität
- Branch Protection
- verpflichtende CI-Prüfungen
- CODEOWNERS für Sicherheit, Datenbank und RFC-Kern
- Dependabot oder vergleichbare Updatehinweise
- Composer-Audit und npm-Audit
- Secret-Scanning
- statische Analyse mit Larastan
- Formatprüfung mit Pint
- Migrationstest auf PostgreSQL
- Komponenten- und Übersetzungsscans
- automatisierte Prüfung fehlender Policy-Tests
- Release-Checkliste
- ADR-Pflicht für Architekturänderungen

Der wichtigste Grundsatz lautet:

> Was für Versionierung, Autorisierung, Audit oder formelle Entscheidungen kritisch ist, darf nicht nur in einer Bot-Anweisung stehen. Es muss zusätzlich durch Datenbankschema, Policies, Tests und CI abgesichert werden.
