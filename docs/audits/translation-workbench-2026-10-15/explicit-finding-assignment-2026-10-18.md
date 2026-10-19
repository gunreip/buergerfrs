# Explizite Key-Zuordnung einer Fundstelle

Im Modal „Edit translation key“, im bestehenden Prüfabschnitt für Key-Zuordnungen, kann für die geöffnete Fundstelle einer ihrer bereits aktiv verknüpften Keys ausgewählt werden. Die Aktion erscheint nur bei mehreren aktiven, statischen Key-Datensätzen mit demselben TranslationKey wie dem gefundenen Key.

Die Bestätigung bezieht sich ausschließlich auf die angezeigte Finding-ID. Die anderen Verknüpfungen dieser Fundstelle werden archiviert; die Key-Datensätze selbst, deren andere Findings und ihre Übersetzungswerte bleiben erhalten. Review und Timeline dokumentieren die Auswahl.

Die Entscheidung wird an der Fundstelle gespeichert und vom Scanner vor der allgemeinen Key-Auswahl ausgewertet. Sie bleibt bei einer eindeutigen Zeilenverschiebung erhalten. Ein veralteter oder ungültiger Ziel-Key sowie eine nachträglich widersprüchliche Zuordnung führen zum Abbruch statt zu einer stillen Neuauswahl. Neue Fundstellen erhalten keine Entscheidung einer alten oder obsoleten Fundstelle.

## Grenzen

Dies löst eine Mehrfachzuordnung für eine bestimmte Fundstelle. Es ist keine globale Key-Konsolidierung und keine Umbenennung des TranslationKeys. Die bisherigen Prüfungen mehrerer aktiver Eigentümer bei Veröffentlichung bleiben bestehen. Unterschiedliche Key-Texte, dynamische Keys und fehlende/archivierte Quelldateien werden durch diese Aktion nicht aufgelöst.

## Prüfgrundlage an der Auswahl

Jeder angebotene Key zeigt einen aufklappbaren Abschnitt mit Reviews, die die geöffnete Fundstelle direkt betreffen oder sie in `selected_finding_ids` einschließen. Reviews ohne diesen Bezug werden nicht als Begründung für diese Zuordnung angezeigt.

Die Tabelle unterscheidet „This finding“ und „Included in bulk selection“; Sammel-Freigaben tragen zusätzlich „Bulk review“. Angezeigt werden Review-IDs, Entscheidung, damaliger TranslationKey und Prüfzeitpunkt. Wiederholte Zeilen derselben Bulk-Operation werden nur bei gleichem Ziel-Key und Prüfzeitpunkt zusammengefasst; sämtliche Review-IDs bleiben erhalten. Ein Hinweis stellt klar, dass ein Bulk-Bezug keine exklusive Key-Zuordnung beweist.

Fehlen solche Nachweise, wird dies ausdrücklich angezeigt. Es bedeutet nicht, dass der Key insgesamt ungeprüft ist. Auch diese relevanten Review-Datensätze gehören zum verschlüsselten Stand: Neue oder geänderte Entscheidungen nach dem Öffnen machen die Auswahl veraltet und verlangen ein erneutes Öffnen.

## Gespeicherte Entscheidung und Modal-Ablauf

Nach der Auswahl werden sowohl die Prüftabelle als auch die Quellenliste desselben Modals neu geladen. Ein eigener Hinweis „Saved finding assignment“ zeigt die Finding-ID, gewählte Key-ID, Review-ID und das Prüfdatum, auch wenn die Auswahlknöpfe mangels Mehrfachzuordnung verschwunden sind.

Die Bestätigung ist nur grün, solange der gespeicherte Review zur Fundstelle und zum Key gehört, genau dieser Key aktiv zugeordnet ist und Finding-Ausdruck sowie TranslationKey weiterhin zur gespeicherten Entscheidung passen. Ein später archivierter Key oder eine widersprüchliche Zuordnung erscheint als Hinweis. Die Anzeige ersetzt keinen erneuten Quellcode-Scan.

Der Integrationstest durchläuft die Methoden des tatsächlichen Workbench-Modals: öffnen, prüfen, auswählen, Quellenliste kontrollieren, schließen, erneut öffnen und scannen. Ein Aufruf der Auswahl bei geschlossenem Modal wird abgewiesen. Zusätzliche View-Tests prüfen die gespeicherte und die ungültig gewordene Entscheidung. Dies ist eine serverseitige Ablaufprüfung; kein interaktiver Browser-Test.

## Fundstellen-Auswahl und globale Key-Zuständigkeit

Die Prüfung zeigt pro TranslationKey separat, ob mehrere aktive Key-Datensätze bestehen und welche aktiven Findings noch mehrfach aktiv zugeordnet sind. Historische Links und obsolete Findings zählen dabei nicht als aktuelle Mehrfachzuordnung. Auch ein aktiver Key ohne verbleibende aktive Fundstelle bleibt ein aktiver Key; er wird durch die Fundstellen-Auswahl nicht automatisch archiviert.

Der Warnhinweis „Translation key ownership is unresolved“ bleibt deshalb nach einer erfolgreichen Finding-Auswahl bestehen, solange mehrere aktive Keys denselben TranslationKey tragen. Die vorhandenen Sperren für Umbenennung und Veröffentlichung bleiben erhalten. Der Modal-Ablauftest prüft ausdrücklich, dass die Mehrfachzuordnung verschwindet, der Key-Konflikt jedoch bleibt und eine Umbenennung ohne neue Review-Zeile abgewiesen wird.

Noch offen ist eine eigene Entscheidung über diese Key-Datensätze: tatsächliche Duplikate ausdrücklich konsolidieren oder eigenständige Aufgaben bewusst trennen. Die vorhandene Konsolidierung dynamischer Optionsquellen ersetzt diese Entscheidung für skalare Keys nicht. Gleicher Text oder eine einzelne Finding-Auswahl reichen dafür nicht als Beleg aus.

## Absicherung

- Admin-/Super-Admin-Prüfung sowohl beim Öffnen als auch beim Anwenden.
- Authentifiziert verschlüsselter Stand von Finding, Keys, Verknüpfungen und Quelldatei-Hash.
- Erneuter Vergleich innerhalb einer Transaktion; PostgreSQL-Sperren auf den beteiligten Tabellen.
- Ungültige Key-ID, manipulierte Belege und zwischenzeitliche Änderungen werden zurückgewiesen.
- Keine produktiven Zuordnungen wurden während der Implementierung ausgewählt oder verändert.

## Validierung

Die vollständigen Dateien `TranslationWorkflowTest` und `ValueApprovalViewTest` bestehen mit 730 Tests und 4541 Assertions. Zusätzlich wurden nach der abschließenden Abgrenzung zur allgemeinen Konsolidierungsprüfung die betroffenen Scanner-Schutzprüfungen erneut ausgeführt. Abgedeckt sind insbesondere wiederholte Scans, Verschiebung innerhalb der Datei, fehlende Berechtigung, manipulierte/veraltete Belege, geänderte Quelldateien und ein später archivierter Ziel-Key. Die geprüfte Auswahl gilt nur für die konkrete Fundstelle; andere Fundstellen unterliegen weiterhin den allgemeinen Konfliktprüfungen.

## Ausdrückliche Archivierung eines zurückgebliebenen Key-Datensatzes

Im selben Prüfbereich erscheint nun eine Archivierungsaktion, wenn **alle** bisherigen Verknüpfungen eines skalaren Keys durch die Finding-Auswahl ausdrücklich auf **denselben** anderen aktiven Key mit identischem TranslationKey übertragen wurden. Die zugehörigen Reviews und die gespeicherten Finding-Entscheidungen müssen zusammenpassen; pro Finding muss nur der gewählte Ziel-Key aktiv verknüpft sein.

Kein Angebot entsteht bei verbleibenden aktiven Links, nicht belegten historischen Links, unterschiedlichen Ziel-Keys, dynamischen Keys oder eigenen Zeilen in `key_values` / `dynamic_key_values`. Insbesondere wird ein zufällig unbenutzter Key nicht allein aufgrund seines gleichen Textes zum Duplikat erklärt.

Die Bestätigung nennt alten Key, Ziel-Key und betroffene Findings. Der Server prüft Berechtigung und einen verschlüsselten Snapshot von Keys, Findings, Links, Reviews und keygebundenen Werten erneut unter PostgreSQL-Tabellensperren. Veraltete Belege werden abgewiesen. Der alte Key erhält `status=obsolete`, `reconciled_canonical_key_id` und einen Verweis auf den neuen Archivierungs-Review. Ein Timeline-Ereignis protokolliert vorher/nachher. Bestehende Links, Findings, Reviews und Übersetzungswerte bleiben erhalten; Quellcode und Sprachdateien werden nicht geschrieben.

Die bereits vorhandene Scanner-Regel für `reconciled_canonical_key_id` verhindert die Wiederbelebung des archivierten Keys. Der Integrationstest führt die Aktion über das Workbench-Modal aus und prüft zwei weitere Scans. Der Konflikthinweis verschwindet nur, wenn tatsächlich nur noch ein aktiver Key übrig ist.

Dies ist noch keine allgemeine Zusammenführung beliebiger Keys und keine Aufteilung gemeinsamer Übersetzungswerte auf eigenständige Aufgaben. Diese Fälle benötigen weiterhin eigene fachliche Entscheidungen. Es wurden keine produktiven Key-Datensätze über die neue Aktion archiviert.

## Vorschau für eigenständige Übersetzungsaufgaben

Bei mehreren aktiven Key-Datensätzen zum gespeicherten TranslationKey und einem abweichenden eingegebenen Key zeigt die neu geöffnete Zuordnungsprüfung eine Trennungsvorschau. Sie benennt den ausgewählten Key, sämtliche seiner aktiven Finding-Verknüpfungen mit Datei/Zeile/Status, die am alten TranslationKey verbleibenden Key-IDs und die Zahl der gemeinsamen gespeicherten Werte.

Hinweise kennzeichnen Mehrfachzuordnungen, inaktive Findings an aktiven Links, dynamische Keys, ungeeignete Zielsyntax, Container und bereits belegte Ziele. Diese Vorschau ist eine Umfangsprüfung, keine vollständige Schreibfreigabe: veröffentlichte Sprachdateien, tatsächlicher Quellcode und dessen späterer Übergang werden hier nicht validiert oder verändert.

Die normale Umbenennung verschiebt gemeinsame Werte und ist deshalb kein zulässiger Ersatz für eine Trennung. Die Vorschau hat keinen Anwenden-Button. Vor einer schreibenden Trennung fehlen noch der abgesicherte Übergang zwischen gefundenem altem und gewähltem neuem Key, die gezielte Quellcode-Aktualisierung sowie eine ausdrückliche Entscheidung über die Übernahme und erneute Prüfung von Übersetzungswerten. Insbesondere darf eine bestehende `explicit_key_assignment` nicht allein durch eine neue Key-Zeichenfolge widersprüchlich werden.

Der Test prüft Berechtigung, Modal-Anbindung, unveränderte Tabellen, Mehrfachzuordnungen, belegte Ziele und das Ausblenden bei eindeutiger Zuständigkeit. Der View-Test prüft den ausdrücklich schreibfreien Zustand und maskierte Dateiangaben.

## Schutz der Quellcode-Aktualisierung bei getrennten Aufgaben

Die bisherige Code-Planung zählte nur unterschiedliche bestätigte TranslationKey-Texte. Zwei bestätigte Key-Datensätze mit demselben Text galten dadurch als eindeutig. Jetzt zählt sie zusätzlich die bestätigten Key-IDs und meldet diesen Fall als `manual_review / multiple_reviewed_key_records`. Bei verschiedenen Key-Texten bleibt die vorhandene Meldung `multiple_reviewed_translation_keys` bestehen.

Vor einer Code-Aktualisierung wird für gleiche Ausdrücke in derselben Datei geprüft, ob die bestätigten Fundstellen unterschiedliche Ziel-Keys haben oder eine mehrdeutige bestätigte Zuordnung enthalten. Ein Key-Filter oder Limit darf diese Nachbaraufgaben nicht verbergen: Der Applier lädt dafür die Planung der betroffenen Dateien ohne diese Einschränkungen. Eine ältere `duplicate_confirmed_same_key`-Entscheidung darf den aktuellen Konflikt nicht überstimmen. Das Ergebnis lautet `duplicate_expression / source_expression_has_competing_key_assignments`; für diese Ausdrücke wird nichts geschrieben.

Regressionstests prüfen Vorschau und Schreiben mit vollständiger Auswahl, Key-Filter und Limit. Dateien bleiben unverändert und es entstehen keine `code_update_applied`-Ereignisse. Der bestehende Test für mehrere bestätigte Key-Datensätze prüft nun auch gleiche Key-Texte als Konflikt.

Dieser Schutz ersetzt noch keine positionsgenaue Aktualisierung zweier gleicher Ausdrücke auf unterschiedliche Ziele. Er verhindert zunächst das bisher mögliche pauschale Überschreiben. Die eigentliche Trennungsaktion bleibt deshalb weiterhin eine Vorschau.

## Positionsgenaue Code-Aktualisierung

Der zuvor pauschal gesperrte Fall gleicher Quellcode-Ausdrücke mit unterschiedlichen bestätigten Ziel-Keys kann jetzt anhand einer vollständigen Fundstellen-Zuordnung aktualisiert werden. Der Planner liefert dafür `source_column` zusätzlich zu Datei und Zeile. Der bestehende Scanner liefert die aktiven Aufrufe und Byte-Offsets; Kommentare werden dabei nicht als Aufrufe behandelt.

Für betroffene Dateien prüft `TranslationWorkbenchPositionedCodeUpdates` die Zuordnung aller gleichlautenden Aufrufe zu den geprüften Fundstellen anhand der einbasierten Zeile und Byte-Spalte. Jeder Aufruf muss eindeutig belegt sein. Fehlende Positionen, zusätzliche Aufrufe, mehrdeutige Zuordnungen, überlappende Ausdrücke oder ausstehende manuelle Konfliktentscheidungen verhindern das Schreiben. Filter und Limits begrenzen nur die ausgewählten Änderungen, nicht die Prüfung ihrer Nachbarn. Die anderen vorhandenen Aktualisierungspfade bleiben bestehen.

Die ausgewählten Ersetzungen werden pro Datei in absteigender Offset-Reihenfolge vorbereitet und gemeinsam geschrieben. Vor dem Schreiben wird der gelesene Dateiinhalt erneut verglichen. Vorschau und Schreibdurchlauf verwenden dieselben einzelnen Ersetzungen auch für den Diff. Die Timeline enthält ein `code_update_applied` je geänderter Fundstelle mit dem Scope `single_verified_source_position`; ursprüngliche Spalte und Offset stehen im vorherigen Zustand.

Die Regressionstests prüfen verschiedene Zeilen, gleiche Zeile mit Unicode, vollständige Auswahl, Key-Filter und Limit sowie verschobene Positionen, fehlende Spalten, zusätzliche Aufrufe und ausdrücklichen manuellen Prüfbedarf. Vorschau-Diff und tatsächlicher Schreib-Diff müssen identisch sein. Getestet wird in isolierten Dateien, nicht im produktiven Quellcode.

Die Trennungsaktion im Modal bleibt weiterhin eine Vorschau: Noch offen ist der nachweisbare Übergang der Scanner-/Finding-Identität vom alten auf den neuen Ausdruck sowie die schreibende Trennung mit ihrer Werteentscheidung. Die positionsgenaue Aktualisierung ist dafür jetzt ein vorhandener Baustein.

## Scanner-Übergang nach positionsgenauem Schreiben

Positionsgenaue Updates protokollieren jetzt zusätzlich den SHA-256 des vollständig geschriebenen Dateiinhalt­es und die neue Zeile/Byte-Spalte jedes geänderten Aufrufs. Damit berücksichtigt der Nachweis auch Positionsverschiebungen durch längere vorhergehende Ersetzungen auf derselben Zeile.

`TranslationWorkbenchCodeUpdateTransition` prüft den letzten passenden Code-Update-Event pro aktiver Fundstelle. Nur wenn der alte Ausdruck zur bisherigen Fundstelle, der neue Ausdruck zur exakt protokollierten Position, der Datei-Hash zum aktuellen Inhalt und die alleinige aktive Key-Zuordnung zum Event passen, wird die alte Finding-ID weitergeführt. Bereits obsolete Findings werden nicht wiederbelebt. Mehrdeutige Zielpositionen oder zwischenzeitliche Änderungen führen zum Abbruch der Scan-Transaktion.

Der Nachweis wird als `code_update_assignment` mit Event-ID an der Fundstelle aufbewahrt. Wiederholte Scans behalten den zugehörigen Key und den ursprünglichen SuggestedKey. Eine vorherige explizite Finding-Auswahl bleibt mit ihrem Review verknüpft; ihre ursprünglichen Angaben werden zusätzlich im Übergangsnachweis erhalten. Der bestehende Review wird nicht umgeschrieben.

Bei gefilterten Updates behalten auch die noch unveränderten Literale ihre bereits eindeutige, bestätigte skalare Aufgabe. Diese Erhaltung gilt nur für unverändert oder eindeutig verschoben wiedererkannte Literale mit genau einer aktiven Zuordnung. Die allgemeinen Prüfungen mehrdeutiger TranslationKey-Eigentümer bleiben bestehen.

Integrationstests prüfen zwei vollständige Folgescans nach vollständiger und gefilterter Änderung, Finding-IDs, SuggestedKeys und Zuordnungen. Eine zwischenzeitlich geänderte Datei oder ein geänderter Ziel-Key verhindert den Übergang und lässt die Findings unverändert. Ältere Code-Update-Events ohne den neuen Positions-/Hashnachweis gelten nicht automatisch als ausreichender Übergangsbeleg.

## Trennung ohne automatische Werteübernahme

Die Vorschau besitzt jetzt für geeignete Fälle die bestätigungspflichtige Aktion **Separate without copying values**. Sie ändert den ausgewählten bestehenden Key-Datensatz auf den freien Ziel-Key; alle seine aktiven Fundstellen bleiben ihm zugeordnet. Die übrigen Key-Datensätze, sämtliche gemeinsamen `lang_values`, vorhandene Reviews sowie Quellcode und Sprachdateien bleiben unverändert. Es werden keine Übersetzungswerte kopiert oder veröffentlicht.

`TranslationWorkbenchKeySeparation` prüft Berechtigung, eindeutige aktive skalare Zuordnungen, aktuelle Quellcode-Signaturen, freie Zielpfade sowie eigene Werte und offene Übergänge. Bereits belegte Ziele – einschließlich archivierter Key-Datensätze und vorhandener Ziel-/Elternpfade in PHP-Sprachdateien – werden nicht wiederverwendet. Eigene `key_values` / `dynamic_key_values` erfordern vorerst eine gesonderte Prüfung. Ein verschlüsselter Snapshot bindet die Bestätigung an Keys, Findings, Links, Reviews, Werte und Datei-Hashes. Beim Anwenden werden diese erneut innerhalb einer Transaktion mit PostgreSQL-Tabellensperren geprüft.

Review und Timeline protokollieren `translation_task_separated` mit der Werteentscheidung `retain_shared_values_without_copy`. `pending_key_separation` hält pro Fundstelle fest, welcher alte Quellcode-Ausdruck zur gewählten neuen Aufgabe gehört. Wiederholte Scans dürfen diese Zuordnung nicht auf den alten Key zurücksetzen. Die normale Aktualisierung wird für diese Findings zwingend positionsgenau durchgeführt; nach dem belegten Code-Update übernimmt `code_update_assignment` und der Pending-Eintrag entfällt.

Der Integrationstest trennt zwei von drei Fundstellen, prüft unveränderte gemeinsame Werte und die verbleibende Aufgabe, führt wiederholte Scans vor dem Code-Update aus, speichert eigene englische und deutsche Übersetzungen über den ScalarEditor, prüft die Freigabe-/Veröffentlichungssperre und veröffentlicht die ausdrücklich freigegebenen Werte über den PublicationRunner. Anschließend aktualisiert er nur die beiden betroffenen Aufrufe. Weitere Scans behalten alle Finding-IDs. Negative Tests prüfen veraltete Quellen/Reviews, zusätzliche Zuordnungen, inaktive Findings, eigene Werte und belegte Ziele ohne Teiländerungen.

Die bewusste Übernahme bestehender Übersetzungswerte ist eine andere Operation und wird hier nicht angeboten. Produktive Trennungen wurden während der Implementierung nicht angewendet.

Abschließende Validierung der Trennungsaktion: `TranslationWorkflowTest` und `ValueApprovalViewTest` bestehen gemeinsam mit **759 Tests / 4.952 Assertions**. Nach Anwendung bleibt die Prüfung geöffnet und zeigt den ausstehenden Quellcode-Übergang mit Key- und Review-ID. Es handelt sich um serverseitige Workflow-/View-Tests; ein interaktiver Browser-Test wurde nicht durchgeführt.


## Vollständiger Bearbeitungsweg nach der Trennung

Der Integrationstest verwendet jetzt die regulären Workbench-Dienste statt direkter Datenbank-Inserts für die neuen Übersetzungen: ScalarEditor mit Zustands-Fingerprint → ValueApproval → PublicationPreview / PublicationRunner → positionsgenauer CodeUpdateApplier → zwei erneute Scans.

Vor der Freigabe werden beide neuen Werte als nicht freigegeben ausgewiesen; der Veröffentlichungsversuch wird abgewiesen und die bestehenden Sprachdateien bleiben unverändert. Nach der Freigabe enthalten beide Sprachdateien sowohl den bisherigen gemeinsamen Wert als auch den separat eingegebenen neuen Wert. Die ursprünglichen Datenbankwerte bleiben ebenfalls unverändert. Die Veröffentlichung selbst schreibt keinen Quellcode um; erst der anschließende gezielte Code-Update verändert die zwei zugeordneten Aufrufe. Der dritte Aufruf behält den bisherigen Key.

Diese Prüfung benötigt keine Änderung am produktiven Datenbestand und keine zusätzliche Sonderbehandlung im Workflow. Sie bestätigt den regulären Ablauf; sie behauptet keine neue Veröffentlichungssperre im CodeUpdatePlanner.

## Veröffentlichung vor der Quellcode-Ersetzung

Die anschließende Prüfung zeigte: Der CodeUpdatePlanner verlangte bislang passende Datenbankwerte, prüfte aber nicht die tatsächlich veröffentlichte Sprachdatei. Damit hätte ein separat gespeicherter Entwurf bereits einen funktionierenden Quellcode-Ausdruck ersetzen können.

Vor einer neuen Ersetzung prüft der Planner jetzt den konkreten Key-Pfad in der PHP-Sprachdatei der konfigurierten Quellsprache. Nur ein nichtleerer String erlaubt die Ersetzung. Fehlende Dateien/Pfade, Container, andere Datentypen sowie fehlerhafte Sprachdateien ergeben `missing_lang_values` mit `source_lang_value_not_published`. Die UI erläutert diesen Grund mit dem erforderlichen Veröffentlichungsschritt. Bereits unveränderte Key-Aufrufe werden weiterhin als `already_current` erkannt.

Diese Prüfung gilt allgemein, nicht nur für getrennte Aufgaben. Sie nutzt weder Translator-Cache noch einen zufälligen Fallback auf den Key selbst. Eine veröffentlichte Zielsprachen-Datei wird nicht zusätzlich verlangt; die Quellsprache sichert den vorgesehenen Fallback. Die bereits vorhandenen Anforderungen an Datenbankwerte bleiben unverändert. Ein gültiger älterer veröffentlichter Text bleibt nutzbar, auch wenn bereits ein neuer Datenbankentwurf existiert; Veröffentlichung und Entwurfsbearbeitung bleiben getrennte Schritte.

Der Applier erstellt seinen Plan beim Schreiben erneut. Isolierte Tests prüfen die Sperre bei fehlenden, leeren, numerischen, strukturierten und syntaktisch fehlerhaften Sprachdatei-Werten, einen anderen vorhandenen Key sowie die Erlaubnis bei ausschließlich veröffentlichter Quellsprache. Auch der vollständige Trennungsablauf prüft nun einen abgewiesenen Schreibversuch vor Veröffentlichung.

Validierung: Die 759 bisherigen Workflow-/View-Tests bestehen mit der neuen Prüfung. Nach Ergänzung des erforderlichen `source_path` in den neuen Testdaten bestehen auch die sieben neuen Veröffentlichungsfälle im gezielten Nachlauf. Die drei CodeUpdateWritePreflight-Tests bestehen ebenfalls. Keine produktiven Sprachdateien oder Datensätze wurden bearbeitet.

## Fehlende Zielübersetzung und Quellsprachen-Fallback

Die Folgeprüfung hat eine ältere, davon unabhängige Sperre im Planner gefunden: Zusätzlich zum Quellsprachenwert war immer ein Zielsprachenwert in der Datenbank erforderlich. Diese Voraussetzung widersprach dem vereinbarten Ablauf, in dem fehlende Zielübersetzungen auf die veröffentlichte Quellsprache zurückfallen dürfen.

Die zusätzliche Zielsprachen-Pflicht entfällt. Der aktive Quellsprachenwert in der Datenbank und der tatsächlich veröffentlichte Quellsprachen-String bleiben erforderlich. Der vorhandene `target_main_exists`-Wert bleibt eine Statusinformation und blockiert die Ersetzung nicht mehr. Die Vorschautexte unterscheiden fehlende Quellsprachenwerte von der ausstehenden Veröffentlichung und erläutern den Fallback.

Der Integrationstest führt den Weg mit fehlender Zielübersetzung über die echten Bearbeitungs-, Freigabe-, Veröffentlichungs- und Code-Update-Dienste aus. Vor der Veröffentlichung bleibt der Literal-Aufruf erhalten; nach ausschließlicher Veröffentlichung von Englisch darf er durch den Key ersetzt werden. Ein frischer Laravel-Translator mit deutscher Sprache und englischem Fallback liefert anschließend den veröffentlichten englischen Text, ohne deutsche Sprachdatei. Es wird kein Zielsprachenwert automatisch erzeugt.

Gemeinsamer abschließender Testlauf: **769 Tests / 5.031 Assertions bestanden** (`TranslationWorkflowTest`, `ValueApprovalViewTest`, `CodeUpdateWritePreflightTest`). Produktive Daten und Sprachdateien bleiben unverändert.

## Bereits aktueller Code bei späteren Entwurfsänderungen

Ein weiterer Integrationstest deckt die Zeit nach der Veröffentlichung ab: Ein aktiver Key-Aufruf besitzt einen freigegebenen englischen Sprachdatei-Wert. Danach wird der Quellsprachen-Entwurf über den ScalarEditor geändert oder geleert. Zwei Folgescans dürfen weder die Finding-ID verändern noch den veröffentlichten Text oder Quellcode umschreiben.

Der Fall „Entwurf geleert“ wurde bisher im CodeUpdatePlanner fälschlich als `missing_lang_values` statt `already_current` eingestuft. Ursache war die Prüfung auf vorhandene Datenbankwerte vor dem Vergleich des aktuellen Aufrufs mit dem Ziel-Ausdruck. Die Datenbankprüfung steht jetzt nach diesem Vergleich. Berechtigungs-/Zuordnungsfragen, dynamische Ausdrücke und der tatsächliche Quellcode werden weiterhin geprüft; nur bei einer erforderlichen Ersetzung folgen die Werte- und Veröffentlichungsbedingungen.

Die neue Reihenfolge ist keine Freigabe leerer Entwürfe und keine automatische Veröffentlichung. Sie unterscheidet den bereits erledigten Quellcode-Schritt vom aktuellen Übersetzungsentwurf. Die Tests prüfen zusätzlich mit einem frischen Laravel-Translator, dass die deutsche Ausgabe weiterhin den zuvor veröffentlichten englischen Fallback liefert, und dass kein neuer `code_update_applied`-Event entsteht.

Abschließende gemeinsame Validierung: **771 Tests / 5.063 Assertions bestanden**. Ausschließlich isolierte Testdaten und temporäre Sprach-/Quellcodedateien wurden verwendet.

## Erneute Veröffentlichung einer geänderten Fassung

Der Test für spätere Entwurfsänderungen umfasst jetzt auch den vollständigen zweiten Veröffentlichungszyklus. Nach Änderung über den ScalarEditor zeigt ValueApproval den Zustand `changed`. Die vor der Änderung erstellte Veröffentlichungsvorschau wird beim Schreiben als veraltet abgewiesen. Auch eine frisch erstellte Vorschau berechtigt ohne erneute Wertfreigabe nicht zum Veröffentlichen. Beide Versuche lassen die veröffentlichte Sprachdatei unverändert.

Nach ausdrücklicher erneuter Freigabe schreibt der PublicationRunner die neue Fassung. Die erste Freigabe bleibt unverändert erhalten; für beide Fassungen existieren eigene Freigabe- und Veröffentlichungsreviews. Ein frischer Laravel-Translator liefert anschließend die neue Fassung auch über den Quellsprachen-Fallback. Ein weiterer Scan erhält Finding-ID und Key-Zuordnung; der Code bleibt unverändert, ohne zusätzlichen Code-Update-Event.

Hier war keine Änderung der produktiven Dienste notwendig. Gezielte Validierung der erweiterten Szenarien: **2 Tests / 48 Assertions bestanden**. Der zuletzt vollständige Regressionstest (771 Tests) wurde bei dieser reinen Testerweiterung nicht erneut ausgeführt. Produktive Daten bleiben unverändert.

## Geleerte Werte sind keine Löschfreigabe

Für beide Sprachrollen (Quellsprache Englisch und Zielsprache Deutsch) ist jetzt der vollständige Fall abgesichert: veröffentlichen → gespeicherten Wert über ScalarEditor leeren → Vorschau → Veröffentlichungsversuch. Der Exportplan erkennt die mögliche Entfernung; der PublicationRunner verweigert sie. Sprachdateien, Quellcode und vorhandene Historie bleiben beim abgewiesenen Versuch unverändert. Der vorherige Speichervorgang bleibt regulär protokolliert.

Die Sperre bestand bereits, war in der UI aber hauptsächlich als Zähler sichtbar. Ein bedingter amber Flux-Callout erklärt nun, dass das Leeren eines gespeicherten Werts keinen veröffentlichten Text entfernt und diese Vorschau wegen der geplanten Löschungen hier nicht veröffentlicht werden kann. Der Runner liefert dafür eine konkrete Fehlermeldung statt der allgemeinen Meldung zu Freigaben, Konflikten und Löschungen.

Ein frischer Laravel-Translator bestätigt insbesondere: Nach Leeren des deutschen Datenbankwerts wird weiterhin der veröffentlichte deutsche Text ausgegeben, nicht automatisch der englische Fallback. Eine ausdrückliche Entfernung veröffentlichter Übersetzungen ist weiterhin ein gesonderter Vorgang; diese Änderung führt keine neue Löschaktion ein.

Gemeinsame Validierung: **773 Tests / 5.099 Assertions bestanden**, einschließlich gerenderter Hinweisbox. Produktive Datensätze und Sprachdateien wurden nicht verändert.

## Wiederholte Bedienaktionen ohne neue Veröffentlichung

Unverändertes Speichern über ScalarEditor und erneute Freigabe derselben Wertrevision waren bereits wiederholbar: Es entstehen weder neue Wertrevisionen noch zusätzliche Speichern-/Freigabe-Events. Ein erneuter Veröffentlichungsaufruf ohne neue oder geänderte Werte legte dagegen bisher einen weiteren Veröffentlichungslauf samt Beleg an, obwohl keine Sprachdatei geschrieben wurde.

Die Vorschau deaktiviert jetzt bei null neuen/geänderten Werten den Veröffentlichungsbutton und erklärt „No new or changed translations to publish.“ Der PublicationRunner prüft diese Bedingung serverseitig nach der regulären Vorschau- und Zulässigkeitsprüfung, aber vor Vorbereitung/Journalanlage. Ein solcher Aufruf erzeugt keinen weiteren Lauf oder Veröffentlichungsbeleg. Der Hinweis behauptet bewusst nicht, dass alle Übersetzungen vollständig seien; auch eine leere Auswahl kann keine Änderungen enthalten.

Die Prüfung gilt nur beim Start einer neuen Veröffentlichung, nicht bei der Wiederaufnahme eines unterbrochenen Laufs. Dort muss die bestehende Wiederherstellungslogik auch nach bereits geschriebenen Dateien die Historie abschließen können.

Ein Integrationstest führt unverändertes Speichern, erneutes Freigeben und den erneuten Veröffentlichungsversuch zweimal aus und vergleicht den vollständigen Wertdatensatz, Reviews, Timeline, Sprachdatei und Laufübersicht. Ein View-Test prüft den Hinweis und das tatsächlich deaktivierte gerenderte Button-Element.

Gemeinsamer Abschlusslauf: **775 Tests / 5.118 Assertions bestanden**, einschließlich Wiederaufnahme- und UI-Tests. Produktive Daten wurden nicht verändert.

## Zwei Dialoge: veraltete Freigabe und geprüfte Veröffentlichungsvorschau

Bestehende Tests sichern bereits, dass ein älterer Editor keine neueren Übersetzungen überschreibt und dabei seine ungespeicherten Eingaben behält. Die neue Prüfung verbindet diesen Fall mit einer zuvor geprüften Veröffentlichungsvorschau: Dialog B gibt gespeicherte Werte frei und prüft die Veröffentlichung, während Dialog A anschließend eine neuere Übersetzung speichert.

Der Freigabeversuch des älteren Dialogs wurde korrekt abgewiesen. Seine alte Veröffentlichungsvorschau und der Hinweis „geprüft“ blieben jedoch bestehen. `approveStoredValue` setzt diese beiden UI-Zustände jetzt vor dem Aufruf des Freigabedienstes zurück, also auch bei einer abgewiesenen veralteten Revision. Der eigentliche Übersetzungsentwurf im Editor bleibt unverändert.

Der Integrationstest prüft die Abweisung ohne zusätzliche Historie und ohne Sprachdatei-Schreibzugriff. Anschließend öffnet derselbe Dialog die gespeicherten Werte erneut, sieht die neuere Revision, gibt diese ausdrücklich frei und veröffentlicht sie mit neuer Vorschau. Nur der tatsächlich gespeicherte neuere Text gelangt in die Sprachdatei; der davon abweichende, ungespeicherte Editor-Text bleibt weiterhin erhalten.

Abschließende gemeinsame Validierung: **776 Tests / 5.133 Assertions bestanden**. PHP-Syntax und Whitespace-Prüfung der bearbeiteten Dateien sind sauber. Produktive Daten wurden nicht verändert.

## UI-Prüfung Finding #891: IDs, Graph-Stand und History

Lesende Prüfung am 18. Oktober: Finding #891 (`admin.permissions.modals.roles.changes`) ist aktiv mit Key #1236 verknüpft. Die früheren Links zu #1249, #1263, #1283 und #1858 stehen auf `merged`. Die passende Timeline-Chain/Dataset-ID ist #252. Dataset #891 gehört dagegen zu Key #1496 / Finding #6134 (`ui.filters.missing-capital`). Finding-, Key- und Dataset-IDs sind verschiedene Identitäten.

Die exakte History-Abfrage des Workbench-Modals (Finding #891 oder aktueller Key #1236) umfasst 145 Ereignisse; die UI zeigt maximal 100. Die zuerst weiter gefasste Audit-Abfrage einschließlich sämtlicher historisch verknüpfter Keys ergab 174 Ereignisse. Davon sind 134 `finding_changed`. Die jüngste Finding-Änderung für #891 stammt vom 24. Juli 2026. Stichproben zeigen das wiederholte Wechseln derselben Finding-ID zwischen unterschiedlichen Dateien/Scopes innerhalb damaliger Scanläufe. Diese historischen Ereignisse wurden nicht gelöscht oder umgeschrieben.

Dataset #252 wurde zuletzt am 7. August 2026 aktualisiert und enthält einen gespeicherten Zähler von 31 Ereignissen. Es ist keine aktuelle Live-Abbildung der Workbench-History. Der Graph-Header zeigt nun Dataset-, Root-Finding- und Root-Key-ID sowie den Aktualisierungszeitpunkt des gespeicherten Graph-Stands. Die History-Beschreibung nennt ihre Begrenzung auf die neuesten 100 Ereignisse.

Der gemeinsame Dataset-Selector bietet eine ausdrückliche ID-Art (`dataset`, `finding`, `key`). Finding/Key werden anhand der Root-ID und gespeicherten Mitglieds-IDs aufgelöst. Genau ein Treffer wird geladen; kein Treffer oder mehrere Treffer führen zu einer Meldung, ohne willkürlichen Wechsel. Nach erfolgreicher Auflösung zeigt die Auswahl wieder die tatsächlich geladene Dataset-ID. Random-, History-, Critical- und Raw-Data-Zeilenauswahl bleiben explizite Dataset-Auswahlen.

Die serverseitigen Detailabfragen für Dataset #252, Key #1236 und Finding #891 funktionieren und liefern 35/31/33 Felder. Die bestehende Interaktion erfolgt über Rechtsklick auf das TextLabel; das Info-Icon ist ein nicht anklickbarer Indikator. Ein Hinweis erläutert dies. Ein Fehler der tatsächlichen Browser-Interaktion ist damit noch nicht ausgeschlossen; die Rückfrage, ob Rechtsklick-Menü oder anschließendes Detailmodal ausbleibt, bleibt offen.

Ein zusätzlicher isolierter Regressionstest scannt denselben Key in fünf verschiedenen Dateien wiederholt und bestätigt fünf stabile Finding-Identitäten ohne zusätzliche `finding_changed`-Events bei unveränderten Quellen. Kein produktiver Scan, Graph-Neuaufbau oder Datenbank-Schreibvorgang wurde ausgeführt.

### Finding graph scope and visible record identities

The data-driven datasets page now separates Timeline chain (cyan) from Finding history (violet). Finding lookup still resolves the enclosing dataset; the selected finding is retained for the explicitly selected Finding history view. Finding history reads current timeline events by `finding_id` only, ordered by timestamp and ID, with Flux pagination of 20 events. It does not import other findings' events merely because they share a key, or silently truncate the history to 100 entries. Existing flow-start/flow-step and context-menu components render the view.

Event labels identify the event, finding and associated key. Selected recorded before/after fields expose assignment/location/status changes; additional changes remain available through the context menu. The label's maximum five lines is respected. The existing chain event labels now also show their referenced record ID (including Key created → Key #…). Event detail access in Finding mode validates dataset membership and the event's finding ID instead of requiring a possibly stale snapshot event list. Chain-mode access remains snapshot-scoped.

Validation: focused selection, rendering, context and finding-history tests passed (66 tests / 483 assertions); the added host-view rendering assertion also passed with the finding-history suite (5 tests / 25 assertions). No production database, source translations or stored history were changed.

## Wiederaufnahme der Bestandsprüfung nach der Graph-Ansicht

Erneuter lesender Datenbank-/Quellcode-Abgleich am 18. Oktober 2026 mit `audit-remaining-owner-decisions-2026-10-18.php`. Ergebnis: weiterhin 29 aktive Findings mit je zwei aktiven Key-Verknüpfungen, sieben Key-Paare. Alle 29 Quellcode-Zuordnungen ergeben `position_refined`; keine dieser untersuchten Fundstellen wird als fehlend eingestuft. Die vorgeschlagenen Positionspräzisierungen wurden nicht angewendet.

| TranslationKey | Aktive Key-IDs | Findings |
| --- | --- | --- |
| `ui.states.all` | #5, #243 | #2965, #2966 |
| `packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.dynamic` | #335, #4582 | #4949 |
| `packages.gunreip.laravel_translation_workbench.resources.views.livewire.entries.review.modal_states.translation_key_missing` | #421, #4597 | #4951 |
| `ui.type` | #656, #1212 | #6007, #6009, #6022, #6032, #6033, #6061, #6062, #6064, #6066, #6072, #6077, #6097, #6100, #6103, #6115, #6116, #6124, #6126 |
| `ui.button.cancel.cancel` | #699, #3560 | #4763 |
| `ui.filters.yes` | #809, #993 | #5293, #5294, #5296, #5302, #5701 |
| `ui.button.reset.reset` | #3145, #5240 | #4617 |

Nächster konkreter UI-Prüffall: Finding #4617 in `resources/views/components/management/people/create-person/⚡actions.blade.php`, Zeile 12. Der Quellcode verwendet `ui.button.reset.reset`. Aktive Keys sind #3145 (pending) und #5240 (reviewed). Reviews #1134–#1137 von #5240 betreffen ausschließlich #7936/#7937/#7938; #4617 ist auch nicht in deren Bulk-Auswahl enthalten. Daher darf der grüne Key-Status keine automatische Entscheidung für #5240 begründen. Die vorhandene Zuordnungsprüfung muss für beide Kandidaten fehlende Review-Evidenz zu dieser konkreten Fundstelle anzeigen.

Die geprüften 14 Keys haben keine eigenen Einträge in `key_values` oder `dynamic_key_values`. Das bedeutet ausdrücklich nicht, dass keine gemeinsamen Lang-Werte oder veröffentlichten Übersetzungen existieren. Es ist keine Lösch- oder Zusammenführungsfreigabe.

Keine produktiven Datensätze, Zuordnungen, Quellen oder Sprachdateien verändert. `ui.type` bleibt wie vereinbart eine ausdrücklich in der UI zu bearbeitende Aufgabe.

## Explicit ownership conflict dialog

The key inspection now opens a separate Flux modal for a translation key shared by active scalar key records. It lists the owners, their active findings and source locations, original literal text, shared translations and reviews. An identical, nonempty original literal on every finding produces a recommendation only; missing originals are not replaced with translation values for this comparison.

Two explicit decisions are available:

- Consolidation: select the surviving key, inspect the affected findings and archived owners, acknowledge the context comparison, then confirm. Active links are reassigned with per-finding reviews; retired keys point to the canonical survivor. Previous aliases are flattened. Obsolete findings and historical links remain archived. Shared values, original literals, suggested keys and source files are not rewritten. Owner-specific values, dynamic owners, outside assignments, pending source transitions and stale findings block this operation.
- Separation: select one owner and a free nested translation key, preview the existing `TranslationWorkbenchKeySeparation` plan, then confirm. Shared values remain at the old key. Values, publication and source updates continue through the established workflow.

Consolidation uses an encrypted snapshot of database rows and source hashes, revalidated under transaction locks. Changing the source, values, reviews or assignments invalidates the preview. Repeated application is rejected. No production records were changed while implementing the dialog.

## Finding identity and source-position succession

The agreed policy now distinguishes findings from translation tasks. Separate source occurrences remain separate findings even when they share one key record. An unchanged rescan updates the current finding's observation counters without creating another finding.

An unambiguous move within a source file creates a new finding, archives the predecessor and records reciprocal `predecessor_finding_id` / `successor_finding_id` references. `root_finding_id` identifies the start of this succession. The predecessor retains its old source coordinates and original capture. Active key links are inherited by the successor with recorded link lineage; no key record is merged or generated merely because the position changed. Multiple existing assignments remain an explicit conflict. Obsolete findings are never reused as current occurrences.

Both foundation synchronization and reviewed source-repair plans use `TranslationWorkbenchFindingVersioner`. Historical line-only records receiving their first precise column remain position refinements, not inferred moves. Ambiguous moves still abort the transaction. Cross-file relocation is not inferred from text similarity. A verified Workbench code replacement retains its existing transition handling; an untouched neighbouring call physically shifted by that replacement receives a position successor.

Dynamic option observations may follow the reciprocal finding succession while retaining their own observation/source identity and translation values. An equal literal without that recorded lineage does not authorize reuse. Existing history is not retroactively rewritten. No production scan or repair was executed for this change.

### Read-only succession navigation

The key editor's Source callout and the finding Timeline modal show the immediate predecessor, selected finding and successor, including F# IDs, source line/column, status and K# assignment IDs. Only reciprocal succession references become navigation targets. History buttons open the selected finding's timeline without changing the key editor selection or unsaved input. Archived source positions are explicitly labelled historical. No archive entry is reactivated. Findings without succession metadata do not display an empty section.

Validation: 1 database-backed navigation test (17 assertions), including three generations, invalid targets and unchanged database rows; 24 view tests (176 assertions), including escaped source paths and distinct finding/key identities.

## Key records versus linked findings — 2026-10-19

Read-only verification for finding #4763: `ui.button.cancel.cancel` has active key records #699 and #3560; #3721 is obsolete. Active finding links are #2367 and #9214–#9219 to key #699, and #4763 to key #3560. This is a duplicate key-record conflict, not a conflict caused by eight findings sharing a translation.

The inspector counts active key records independently of links. UI headings now state “Multiple active key records for the same translation key”; the action says “Resolve duplicate key records”. “Opened finding” replaces “Selected finding”. The inspection displays separate counts for key records and uniquely linked active findings. Consolidation wording explicitly states that findings remain distinct and do not own or absorb each other.

No production assignments were changed. Validation: one database-backed regression (8 assertions) establishes that one key with three findings is valid, a second active key creates the warning even without new findings, and an archived key is excluded. All 24 view tests pass (176 assertions).

## Immutable initial suggestions — 2026-10-19

Read-only audit of finding #4763: its first `finding_discovered` event #89681 (2026-07-24 20:16:44) already records `ui.button.cancel.cancel` as both suggested and found translation key. The capture metadata identifies `existing_key_at_source`, with no literal text. Equality of these fields therefore does not demonstrate a later change. Key record #4763 is a different identity; the finding currently links to key #3560.

Ordinary foundation rescans now preserve an existing finding's initial suggestion, its context fields and suggestion provenance, including originally absent metadata. Key synchronization uses that retained suggestion rather than a newly calculated scanner suggestion, avoiding duplicate key records caused solely by changed suggestion rules. Existing key-record suggestions are also retained.

The key editor and review details show the finding's suggestion. Copy, transform and accept actions consistently use that same value, independent of the linked key record's suggestion. The tooltip explains the initial-capture meaning; capture provenance distinguishes a key already present in source from a suggestion generated for a literal. No historical values are guessed or retroactively repaired, and no production records were changed.

Regression tests cover repeated scans with changed scanner suggestions/provenance and differing finding/key-record suggestions across editor actions. Publication test fixtures now select their nested publication key through the review service instead of modifying a suggestion to steer the accept action.

Validation: workflow and value-approval view suites passed together: 799 tests, 5,316 assertions. PHP syntax and changed-file whitespace checks passed.
