`translation_values` ist vom Schema her bereits ziemlich nah an einer eigentlichen Übersetzungstabelle:

```text
translation_key_id
locale
value
status
source
reviewed_at
reviewed_by_user_id
```

Für den aktuellen Stand ist das als **Arbeits-/Review-Tabelle** völlig brauchbar. Für eine spätere „authoritative Export-Tabelle“ würde ich trotzdem eine eigene Tabelle bevorzugen, wie du sagst, weil wir dort sauberer mit Freigabe, Sortierung, Änderungsverlauf und Exportstatus arbeiten können.

Ich würde es fachlich so trennen:

```text
translation_keys
= authoritative Key-/Audit-/Review-Tabelle

translation_usages
= authoritative Fundstellen-/Ursprungsreferenz-Tabelle

translation_values
= aktueller Review-/Arbeitsstand je Key und Locale

spätere Tabelle, z. B. translation_entries oder translation_exports
= freigegebene/authoritative Übersetzungen für Datei-Export
```

## Warum eigene Tabelle für finale Übersetzungen sinnvoll ist

Gerade wenn später viele Sprachen dazukommen, brauchen wir vermutlich mehr als nur `locale/value/status`.

Mögliche spätere Felder:

```text
translation_key_id
locale
value
sort_value
collation
status
source
version
approved_at
approved_by_user_id
exported_at
created_at
updated_at
```

Für Sortierung wäre `sort_value` oder ein normalisierter Such-/Sortierwert sinnvoll. Verschiedene Sprachen sortieren unterschiedlich, und man sollte die Sortierlogik nicht zwingend aus dem reinen Translation-Value ableiten müssen.

Beispiel:

```text
value:      Ändern
sort_value: Aendern
locale:     de
```

Oder später locale-/collation-basiert:

```text
locale: de
collation: de-DE-u-co-phonebk
```

Das müssen wir aber jetzt noch nicht bauen.

## Nächster sinnvoller Schritt

Ich würde jetzt zuerst das Locale-/Language-Konzept vorbereiten, bevor wir die finale Translation-Tabelle bauen.

Minimaler nächster Block:

```text
1. Translation locales definieren
2. App locales getrennt davon betrachten
3. Command/Workflow planen: neue Locale hinzufügen
4. Danach erst Tabelle für finale Übersetzungen entwerfen
```

Für den Anfang könnten `app_general.availableLocales` weiterhin die App-Sprachen abbilden. Für Translation-Pflege würde ich später eher eigene Settings oder Tabelle nehmen:

```text
translation_languages
```

mit z. B.:

```text
id
locale
name
native_name
enabled_for_translation
enabled_for_app
sort_order
is_default
created_at
updated_at
```

Das wäre sauberer als `availableLocales` dauerhaft mit allem zu überladen.

## Empfehlung

Als nächstes würde ich **noch keine Migration für Translation-Entries** bauen, sondern zuerst ein kleines Konzept für `translation_languages` festziehen.

Danach wäre der technische Weg:

```text
1. Migration translation_languages
2. Seed/Settings-Migration für de/en
3. Translation-List liest Locales nicht mehr nur aus translation_values, sondern aus translation_languages
4. Später: Button „Add language“
5. Danach: fehlende translation_values für neue Locale erzeugen
```

Damit wäre der spätere Ausbau auf `fr`, `es`, `it` sauber vorbereitet.

Ja, genau. Damit brauchen wir fachlich mindestens **drei getrennte Zustände**:

```text
1. Sprache ist für Übersetzungspflege angelegt
2. Sprache hat bereits Werte in Bearbeitung
3. Sprache ist für App/UI-Ausgabe freigeschaltet
```

Deshalb sollte „neue Language etablieren“ nicht einfach bedeuten: `availableLocales` erweitern.

## Saubere Trennung

Ich würde künftig so denken:

```text
translation_languages
= Welche Sprachen existieren im Übersetzungsmanagement?

app_general.availableLocales
= Welche Sprachen darf die App/UI aktiv verwenden?
```

Beispiel:

```text
translation_languages:
de  active_for_translation=true   active_for_app=true
en  active_for_translation=true   active_for_app=true
fr  active_for_translation=true   active_for_app=false
es  active_for_translation=true   active_for_app=false
it  active_for_translation=true   active_for_app=false
```

Damit kannst du `fr`, `es`, `it` schon pflegen, prüfen und exportfähig vorbereiten, ohne dass ein Benutzer diese Sprache in der UI auswählen kann.

## Neue Tabelle: `translation_languages`

Ich würde dafür eine echte Tabelle nehmen, nicht nur Settings.

Vorschlag:

```text
id
locale
name
native_name
is_default
is_enabled_for_translation
is_enabled_for_app
sort_order
created_at
updated_at
```

Optional später:

```text
direction          ltr/rtl
fallback_locale    z. B. en
collation          z. B. de-DE, fr-FR
date_format
number_format
```

Für jetzt reichen die Kernfelder.

## Warum nicht nur Settings?

Settings sind gut für globale App-Konfiguration. Aber Sprachen werden später eigene Entitäten:

```text
- aktiv/inaktiv
- Sortierung
- Anzeige-Name
- Native Name
- Freigabe für App
- Freigabe für Translation-Workflow
- ggf. RTL
- ggf. Exportstatus
```

Das gehört eher in eine Tabelle.

## Translation-Werte in Bearbeitung

Für die eigentlichen Übersetzungen würde ich später ebenfalls eine eigene authoritative Tabelle bevorzugen, wie du gesagt hast. Nicht, weil `translation_values` unbrauchbar ist, sondern weil wir dort vermutlich bald mehr brauchen:

```text
translation_entries
- translation_key_id
- translation_language_id oder locale
- value
- draft_value
- status
- source
- reviewed_at
- reviewed_by_user_id
- approved_at
- approved_by_user_id
- exported_at
- created_at
- updated_at
```

Aber: Ich würde **nicht beide Tabellen sofort bauen**.

## Reihenfolge

Ich würde jetzt so vorgehen:

```text
Block 1: translation_languages
- Migration
- Model
- initiale Daten de/en
- später App-Settings/Translation-List liest daraus

Block 2: Language-Onboarding
- neue Sprache anlegen
- fehlende translation_values für alle Keys erzeugen
- Status = missing

Block 3: authoritative translation_entries
- erst wenn klar ist, ob translation_values nicht reicht
```

Für den nächsten technischen Schritt wäre also sinnvoll:

```text
Migration + Model für translation_languages
```

Danach können wir `de` und `en` initial eintragen und später `fr`, `es`, `it` sauber hinzufügen, ohne sie direkt in der App freizuschalten.

