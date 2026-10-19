# Übergang historischer Optionsbeobachtungen

Stand: 15.10.2026. Erzeuger: `plan-option-transition.php`; Einzelbelege und Quelltext-Hashes: `option-transition-plan.json`.

Der Abgleich liest PostgreSQL in einer `REPEATABLE READ READ ONLY`-Transaktion und scannt nur die gespeicherten Quellpfade mit dem Scanner im Speicher. Er ruft keine Optionsprovider, Synchronizer, Artisan-Pipeline oder Runtime-Collector auf. Es werden ausschließlich lokale Audit-Dateien geschrieben.

## Ergebnis

| Befund | Anzahl | Beobachtungs-IDs | Konsequenz |
| --- | ---: | --- | --- |
| Aktueller expliziter Aufruf, eindeutiges aktives Finding und exakt passender aktiver Key; gespeicherter Optionskontext fehlt/weicht ab | 15 | 1–10, 13–17 | Finding-Metadaten kontrolliert aktualisieren; Optionsherkunft zusätzlich prüfen |
| Kein unterstützter expliziter Aufruf mit denselben Options-/Key-/Labelvariablen | 8 | 11, 12, 20–25 | Einzeln prüfen; keine automatische Stilllegung oder Zuordnung |
| Pfad nicht im erfolgreichen Scanmanifest | 2 | 18, 19 | Ausschluss/Archivierung prüfen; kein Löschbeweis |

Die neun Beobachtungen mit aktiven Quellen besitzen insgesamt **zwölf aktive direkte Quellenverbindungen**. Keine dieser zwölf Verbindungen hat die Finding-ID des jeweils aktuell erfassten Aufrufs. Die Keys können trotzdem übereinstimmen. Dieser Befund belegt veraltete Verbindungsziele gegenüber den heutigen Aufrufen, nicht automatisch fehlende Übersetzungen oder die Ungültigkeit sämtlicher historischer Quellen. Die IDs bleiben im JSON sichtbar.

## Identität und Grenzen

- Verglichen werden Quellpfad, Options-/Key-/Labelvariable und der explizite Owner-Key des heutigen Aufrufs. Historische Zeilennummern und SuggestedKeys begründen keine Identität.
- Der Bezug zum gespeicherten aktiven Finding verlangt denselben Pfad und exakt denselben Ausdruck. Genau ein aktiver Key muss den expliziten Owner-Key tragen. Mehrdeutige Treffer bleiben ungeklärt.
- Auskommentierte und ausdrücklich nicht übersetzungsrelevante Aufrufe sind ausgeschlossen.
- Auch ein eindeutiges Finding beweist noch nicht die Herkunft oder Aktualität des historischen Optionspayloads. Der Plan gibt deshalb **keine Beobachtung zur automatischen Migration frei**.
- Direkte Quellenreferenzen sind erfasst. Die breiteren heuristischen Consumer-Treffer über Scope, SuggestedKey und Pfad sind damit nicht vollständig inventarisiert.

## Reihenfolge der Umstellung

1. Für die 15 eindeutigen Aufrufe den neuen Optionskontext über die reguläre Foundation-Erfassung bereitstellen. Vor einem produktiven Lauf den Schreibumfang im isolierten Übergangstest prüfen und die im Plan gespeicherten Datei-Hashes erneut vergleichen.
2. Optionsquellen anhand der Component-/View-Verbindung auflösen. Öffentliche Arrays, konfigurierte Provider und zur Laufzeit aus der Datenbank gebildete Optionen bleiben unterscheidbar. Keine Auswahl nur wegen Textgleichheit oder gleichem Variablennamen.
3. Bestehende Beobachtungs-IDs und Quellenverbindungen bewusst übernehmen oder historisch belassen. Vor allem darf eine verschobene Zeile nicht bloß eine neue Beobachtung erzeugen und die weiterhin konsumierte alte Beobachtung aktiv zurücklassen. Übersetzungswerte, Reviews und History müssen erhalten bleiben.
4. Die acht nicht eindeutig wiedergefundenen sowie zwei nicht gescannten Beobachtungen separat entscheiden. Fehlender Treffer dieser eng gefassten Prüfung bedeutet nicht automatisch, dass die UI keine dynamischen Optionen mehr verwendet.
5. Erst nach belegter Versorgung der aktiven Verbraucher die pauschale historische Freigabe in `DynamicOptionSynchronizer::appliesTo` entfernen. Bis dahin ist diese ausdrücklich eine verbleibende Übergangsregel.

Es wurden keine produktiven Metadaten, Quellenverbindungen, Übersetzungen oder Reviews geändert.

## Nachprüfung der zwölf aktiven Quellenverbindungen

Der Bericht enthält jetzt die bisherigen Findings einschließlich Status, Quellpfad, Ausdruck und Optionskontext. Alle zwölf bisherigen Findings sind `obsolete`. Das allein autorisiert noch keine Übernahme ihrer Verbindungen auf ein anderes Finding.

| Befund | Quellen-IDs | Folgerung |
| --- | --- | --- |
| Gleicher Dateipfad, Key und Options-/Key-/Labelvariable, gleiche Labelrolle und gleicher Ausdruckstyp; früher `__($label)`, heute expliziter Optionsaufruf | 73, 1, 3, 76, 77, 5, 24, 26 | Acht begründete Übergangskandidaten; Payloadherkunft und Verbraucher vor Apply prüfen |
| Andere View als die verknüpfte historische Beobachtung | 2, 25, 23 | Nicht auf das Finding der Beobachtung umhängen; Quellenidentität separat erhalten/klären |
| Bisheriger Aufruf war ein indexierter Zugriff ohne gespeicherten Optionskontext; Beobachtung beschreibt dagegen eine foreach-Schleife | 62 | Schleifen-Finding und Anzeige-Finding nicht verwechseln |

Die drei dateifremden Verbindungen betreffen RoleList modal-edit gegenüber modal-create sowie EditPerson gegenüber CreatePerson. Gleiche Optionsnamen und teilweise gemeinsam verwendete Keys erklären die historische Trefferbreite, beweisen aber keine identische Fundstelle.

Quelle 62 gehört zur Anzeige des ausgewählten Familienstands in EditPerson (`$maritalStatusOptions[$maritalStatus] ?? $emptyValue`). Im selben Abschnitt existiert zusätzlich die foreach-Schleife für das Auswahlfeld. Beide verwenden denselben Owner-Key, haben jedoch unterschiedliche Findings und Variablenrollen. Der aktuelle Schleifenaufruf wurde im Plan korrekt als Ziel der historischen Schleifenbeobachtung erkannt; daraus darf nicht folgen, dass auch Quelle 62 auf dieses Schleifen-Finding umgehängt wird. Für den indexierten Anzeigeaufruf ist seine eigene Zuordnung erforderlich.

Damit ist die pauschale Annahme „nur neue Finding-IDs nach einer Positionsänderung“ widerlegt. Acht Verbindungen lassen sich als Kandidaten für den Wechsel der Aufrufform eingrenzen; vier müssen getrennt behandelt werden. Es erfolgte kein produktives Umhängen oder Löschen.

## Optionsbestand und tatsächliche UI-Verwendung

`inspect-option-payloads.php` prüft die acht kontextgleichen Quellen in einer schreibgeschützten PostgreSQL-Transaktion. Es liest deklarierte Property-Defaults per Reflection ohne Component-Instanziierung und für die beiden zuvor geprüften IconRegistry-Reader direkt die entsprechende Konfiguration. Es ruft keine Provider-/Render-Methode auf. Statische View-Verbindungen grenzen die Propertyquellen ein. Dies ist ein auf diese geprüften Quellen begrenzter Audit, kein allgemeiner Providerresolver. Die Quellpfad-Hashes des Übergangsplans werden vorab überprüft.

Ergebnis: **8 von 8 identisch**, einschließlich Optionsschlüssel und exaktem Text. Die Reihenfolge wird für den Vergleich normalisiert. Verglichen wurden aktueller deklarierter/configurierter Bestand, Optionsbeobachtung und aktive DynamicSourceValues. Es gibt dort keine zusätzlichen inaktiven Quellwerte. Die Optionsanzahlen je Quelle sind: 73→4, 1→18, 3→4, 76→3, 77→4, 5→7, 24→4, 26→3. Übersetzungszeilen werden pro Key gezählt; diese Zahlen dürfen wegen gemeinsam genutzter Keys nicht einfach addiert werden.

Quelle **24** wird durch Kandidat **78** referenziert: `dynamic_source:24`, aktiv und bestätigt; Runtime-Quelle **40**. Der tatsächliche UI-Resolver `TranslationWorkbenchDynamicOptions` berücksichtigt diese Verknüpfung über den gemeinsamen Key auch für die heutigen Gender-Findings in CreatePerson und EditPerson. Die en/de-Ausgaben des Resolvers und die aufgelösten Runtime-IDs wurden als Vergleichsbasis in `option-payload-review.json` gespeichert. Alle acht Ausgaben enthalten die erwartete Optionsanzahl. Diese Prüfung bezieht sich auf den PHP-Resolver, nicht auf einen Browserdurchlauf.

Wichtig für die Übernahme: Der Klassifizierungsfingerprint enthält Finding-ID und Beobachtungszeile. Nur die Optionsbeobachtungs-ID zu erhalten genügt daher noch nicht, um nachfolgend auch die DynamicSource-ID zu behalten. Die Migration muss den aktuellen Klassifizierungsfingerprint berücksichtigen und einen anschließenden Klassifizierungslauf testen. Quelle 24 und Kandidat 78 müssen dabei mit ihren IDs, Review-Status und Optionssnapshot erhalten bleiben; Übersetzungen sind nicht erneut zu importieren. Bei geänderten Werten oder inzwischen belegtem Zielfingerprint muss der Übergang abbrechen statt Daten zusammenzuführen.

Die breiten heuristischen Matches der historischen Consumer sind weiterhin eine eigene offene Grenze. Der geprüfte UI-Resolver und direkte/vermerkte Kandidatenreferenzen ersetzen keine vollständige Inventur beliebiger Fremdverweise. Kein produktiver Datenbestand wurde geändert.

## Ausführbare Übernahme und produktive Vorprüfung

Der interne Dienst `TranslationWorkbenchOptionSourceTransition` ist jetzt in isolierten Tests abgesichert: Quell-/Beobachtungs-ID bleiben bestehen, referenzierende bestätigte Kandidaten behalten Review und Snapshot, der folgende Klassifizierungslauf verwendet dieselbe Quelle, Wiederholung ist idempotent. Abweichende Datei/Optionen/Keys, ein vorhandenes Ziel und ein Fehler in einem Folgeauftrag werden zurückgewiesen; der Batch wird vollständig zurückgerollt.

Die ausschließlich lesende `preview-option-source-transition.php` prüft den Dienst gegen den realen Bestand. Alle acht Anfragen liefern `ready`, siehe `option-source-transition-preflight.json`. Das ist die Freigängigkeit dieser engen Übernahmebedingungen, **keine Freigabe der gesamten Scan-/Discovery-Pipeline**. Insbesondere muss deren weiterhin mehrdeutige dateiübergreifende Arrayauflösung vor einem produktiven Apply abgesichert werden. Die Vorschau enthält keine Apply-Option und hat keine produktiven Daten geändert.
