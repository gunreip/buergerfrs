# Ergänzende Übernahmenachweise: dynamische Altformate und Paketdateien

Schreibgeschützte Abfrage: 2026-10-19T09:42:10+02:00. Keine Daten verändert.

## Ergebnis und Grenzen

- 43 alte dynamische Übersetzungswerte vollständig mit Text, Optionsidentität, Locale, Entry-ID und Quellenposition erfasst.
- Keiner der sechs zugehörigen alten Entries hat einen translation_key. SuggestedKey bleibt unverändert und wird nicht ersatzweise als bestätigte Zuordnung verwendet.
- Für alle 43 Werte existieren aktuelle aktive Optionswerte mit gleicher Locale, Optionsidentität und Text. Das sind Vergleichskandidaten, keine bestätigten Zuordnungen. Gleicher Text allein verschmilzt keine Entries, Findings oder Keys.
- 96 dynamische Quellen, 247 Quellwerte, 121 Kandidaten und 26 Optionsbeobachtungen sind zusätzlich mit ihren jeweiligen IDs und gespeicherten Payloads erfasst. Diese Strukturen sind keine pauschal freigegebenen Übersetzungen.
- Alle 31 bislang ausgesparten lang/vendor/backup-Dateien wurden statisch gelesen: 1.054 Texte. 1.051 stimmen mit den korrespondierenden Dateien der installierten Paketversion überein; drei unterscheiden sich. Alle gelesenen Dateien waren nach dem Lesen unverändert.
- Die Dateiwerte bleiben im Namespace backup getrennt von gleichnamigen App-Keys. Der Provider setzt laravel-backup/hasTranslations; PackageTools registriert resources/lang unter shortName backup. Dies ist Quellcode-Nachweis, keine Erweiterung des produktiven Exporters.
- Kein Provider wurde zur Optionsgewinnung ausgeführt, keine PHP-Sprachdatei evaluiert. Keine Daten migriert, freigegeben, reaktiviert oder gelöscht.

## Die 43 alten dynamischen Werte

| Alte Value-ID | Entry-ID | Locale | Optionsidentität | Text | Textgleiche aktuelle Value-IDs / Key-IDs (keine Zuordnung) |
| --- | --- | --- | --- | --- | --- |
| 1 | 1401 | en | outline | Outline | #19 / Key #1988 |
| 2 | 1401 | en | pill | Pill | #21 / Key #1988 |
| 3 | 1401 | en | solid | Solid | #23 / Key #1988 |
| 4 | 1401 | en | subtle | Subtle | #25 / Key #1988 |
| 5 | 1407 | en | amber | Amber | #27 / Key #2006 |
| 6 | 1407 | en | blue | Blue | #29 / Key #2006 |
| 7 | 1407 | en | cyan | Cyan | #31 / Key #2006 |
| 8 | 1407 | en | emerald | Emerald | #33 / Key #2006 |
| 9 | 1407 | en | fuchsia | Fuchsia | #35 / Key #2006 |
| 10 | 1407 | en | green | Green | #37 / Key #2006 |
| 11 | 1407 | en | indigo | Indigo | #39 / Key #2006 |
| 12 | 1407 | en | lime | Lime | #41 / Key #2006 |
| 13 | 1407 | en | orange | Orange | #43 / Key #2006 |
| 14 | 1407 | en | pink | Pink | #45 / Key #2006 |
| 15 | 1407 | en | purple | Purple | #47 / Key #2006 |
| 16 | 1407 | en | red | Red | #49 / Key #2006 |
| 17 | 1407 | en | rose | Rose | #51 / Key #2006 |
| 18 | 1407 | en | sky | Sky | #53 / Key #2006 |
| 19 | 1407 | en | teal | Teal | #55 / Key #2006 |
| 20 | 1407 | en | violet | Violet | #57 / Key #2006 |
| 21 | 1407 | en | yellow | Yellow | #59 / Key #2006 |
| 22 | 1407 | en | zinc | Zinc | #61 / Key #2006 |
| 23 | 2099 | en | mr | Mr. | #63 / Key #3117 |
| 24 | 2099 | en | mrs | Mrs. | #65 / Key #3117 |
| 25 | 2099 | en | mx | Mx. | #67 / Key #3117 |
| 26 | 2101 | en | female | Female | #195 / Key #3120 |
| 27 | 2101 | en | male | Male | #197 / Key #3120 |
| 28 | 2101 | en | diverse | Diverse | #193 / Key #3120 |
| 29 | 2101 | en | unknown | Unknown | #79 / Key #3137; #109 / Key #3452; #199 / Key #3120 |
| 30 | 2110 | en | single | Single | #77 / Key #3137; #107 / Key #3452 |
| 31 | 2110 | en | married | Married | #71 / Key #3137; #101 / Key #3452 |
| 32 | 2110 | en | registered_partnership | Registered partnership | #73 / Key #3137; #103 / Key #3452 |
| 33 | 2110 | en | divorced | Divorced | #69 / Key #3137; #99 / Key #3452 |
| 34 | 2110 | en | widowed | Widowed | #81 / Key #3137; #111 / Key #3452 |
| 35 | 2110 | en | separated | Separated | #75 / Key #3137; #105 / Key #3452 |
| 36 | 2110 | en | unknown | Unknown | #79 / Key #3137; #109 / Key #3452; #199 / Key #3120 |
| 37 | 2318 | en | single | Single | #77 / Key #3137; #107 / Key #3452 |
| 38 | 2318 | en | married | Married | #71 / Key #3137; #101 / Key #3452 |
| 39 | 2318 | en | registered_partnership | Registered partnership | #73 / Key #3137; #103 / Key #3452 |
| 40 | 2318 | en | divorced | Divorced | #69 / Key #3137; #99 / Key #3452 |
| 41 | 2318 | en | widowed | Widowed | #81 / Key #3137; #111 / Key #3452 |
| 42 | 2318 | en | separated | Separated | #75 / Key #3137; #105 / Key #3452 |
| 43 | 2318 | en | unknown | Unknown | #79 / Key #3137; #109 / Key #3452; #199 / Key #3120 |

Die alten Entries betreffen Rollen-Badge-Varianten, Rollen-Badge-Farben, Anrede, Geschlecht und Familienstand. Ein Entry für Modal-Create trägt beispielsweise einen SuggestedKey mit modal_edit. Das ist Herkunftsevidenz, kein Anlass, den SuggestedKey nachträglich umzuschreiben.

## Strukturierte Quellen und Beobachtungen

Option-Payloads bleiben im JSON in ihrer gespeicherten Form erhalten: 14 Listen, zwölf Objekte. Listenindex, Optionsidentität, Label und Locale werden nicht nachträglich gleichgesetzt. Die 247 native_label-Werte sind Quellbeschriftungen ohne durchgängig explizite Sprachzuordnung und werden nicht automatisch zu en-Übersetzungen.

| Status der übergeordneten Quelle für die 247 Quellwerte | Anzahl |
| --- | ---: |
| active | 177 |
| obsolete | 70 |

Der eigene Status der 247 Quellwerte lautet jeweils active. Die Zuordnung zur übergeordneten Quelle muss deshalb separat berücksichtigt werden. Aus active allein darf weder aktuelle Verwendung noch Veröffentlichungsfreigabe abgeleitet werden.

## Die drei Paketabweichungen

| Datei | Key innerhalb backup | Projekt-Override | Installiertes Paket |
| --- | --- | --- | --- |
| lang/vendor/backup/de/notifications.php | notifications.backup_successful_body | Gute Nachrichten, ein neues Backup von :application_name wurde erfolgreich erstellt und in :disk_name gepeichert. | Gute Nachrichten, ein neues Backup von :application_name wurde erfolgreich erstellt und in :disk_name gespeichert. |
| lang/vendor/backup/de/notifications.php | notifications.healthy_backup_found_subject_title | Die Backups von :application_name sind Gesund | Die Backups von :application_name sind gesund |
| lang/vendor/backup/de/notifications.php | notifications.unhealthy_backup_found_full | Die Backups verbrauchen zu viel Platz. Aktuell wird :disk_usage belegt, dass ist höher als das erlaubte Limit von :disk_limit. | Die Backups verbrauchen zu viel Platz. Aktuell wird :disk_usage belegt, das ist höher als das erlaubte Limit von :disk_limit. |

Die Unterschiede sind „gepeichert/gespeichert“, „Gesund/gesund“ und „dass/das“. Das Paket enthält in diesen drei Fällen die sprachlich plausibleren Fassungen. Dennoch bleiben die Projekt-Overrides gesichert; es wurde nichts automatisch ersetzt.

## Paket-Dateiinventar

| Datei | Texte | Identisch mit Paket | Abweichend |
| --- | ---: | ---: | ---: |
| lang/vendor/backup/ar/notifications.php | 34 | 34 | 0 |
| lang/vendor/backup/bg/notifications.php | 34 | 34 | 0 |
| lang/vendor/backup/bn/notifications.php | 34 | 34 | 0 |
| lang/vendor/backup/cs/notifications.php | 34 | 34 | 0 |
| lang/vendor/backup/da/notifications.php | 34 | 34 | 0 |
| lang/vendor/backup/de/notifications.php | 34 | 31 | 3 |
| lang/vendor/backup/en/notifications.php | 34 | 34 | 0 |
| lang/vendor/backup/es/notifications.php | 34 | 34 | 0 |
| lang/vendor/backup/fa/notifications.php | 34 | 34 | 0 |
| lang/vendor/backup/fi/notifications.php | 34 | 34 | 0 |
| lang/vendor/backup/fr/notifications.php | 34 | 34 | 0 |
| lang/vendor/backup/he/notifications.php | 34 | 34 | 0 |
| lang/vendor/backup/hi/notifications.php | 34 | 34 | 0 |
| lang/vendor/backup/hr/notifications.php | 34 | 34 | 0 |
| lang/vendor/backup/id/notifications.php | 34 | 34 | 0 |
| lang/vendor/backup/it/notifications.php | 34 | 34 | 0 |
| lang/vendor/backup/ja/notifications.php | 34 | 34 | 0 |
| lang/vendor/backup/kk/notifications.php | 34 | 34 | 0 |
| lang/vendor/backup/ko/notifications.php | 34 | 34 | 0 |
| lang/vendor/backup/nl/notifications.php | 34 | 34 | 0 |
| lang/vendor/backup/no/notifications.php | 34 | 34 | 0 |
| lang/vendor/backup/pl/notifications.php | 34 | 34 | 0 |
| lang/vendor/backup/pt/notifications.php | 34 | 34 | 0 |
| lang/vendor/backup/pt_BR/notifications.php | 34 | 34 | 0 |
| lang/vendor/backup/ro/notifications.php | 34 | 34 | 0 |
| lang/vendor/backup/ru/notifications.php | 34 | 34 | 0 |
| lang/vendor/backup/sk/notifications.php | 34 | 34 | 0 |
| lang/vendor/backup/tr/notifications.php | 34 | 34 | 0 |
| lang/vendor/backup/uk/notifications.php | 34 | 34 | 0 |
| lang/vendor/backup/zh_CN/notifications.php | 34 | 34 | 0 |
| lang/vendor/backup/zh_TW/notifications.php | 34 | 34 | 0 |

## Was noch keine vollständige Reset-Sicherung ist

Dieser Bericht ist ein gezielter Inhaltsnachweis. Er enthält keine vollständige Datenbanksicherung, keine vollständige Historie und nicht sämtliche übrigen native Legacy-Werte. Die früher gezählten 952 nativen Altwerte sind noch auf exklusive Textinhalte zu prüfen. Ebenso bleibt für die 43 alten Optionswerte die konkrete Zuordnung offen, trotz textgleicher aktueller Kandidaten.
Vor dem Reset müssen eine vollständige Sicherung und eine Generalprobe auf Kopie erfolgen. Die produktive Namespace-Veröffentlichung bleibt bis zur ausdrücklichen Zielkonfiguration gesperrt.

Geändert: inspect-transfer-extra-evidence.php; dieser Bericht; transfer-extra-evidence-2026-10-19.json; database.md. Alle Entscheidungen im Bericht bleiben NULL.
