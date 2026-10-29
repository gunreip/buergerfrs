"""Derive a review checklist from a frozen audit; no app bootstrap or database access."""
import argparse
import hashlib
import json
from collections import Counter, defaultdict
from pathlib import Path


def selected(row):
    return ((row['source'] == 'legacy' and row['status'] == 'ok')
            or (row['source'] in ('workbench', 'dynamic_workbench') and row['status'] == 'active')
            or (row['source'] == 'file' and row['id'].startswith('lang/')))


def build(snapshot, digest):
    groups, history = defaultdict(list), defaultdict(list)
    files = {f['path']: f for f in snapshot['lang_files']}
    for row in snapshot['scalar_evidence']:
        identity = (row['locale'], row['key'])
        (groups if selected(row) else history)[identity].append(row)
    spellings = defaultdict(set)
    for locale, _ in groups:
        spellings[locale.lower().replace('_', '-')].add(locale)
    items = []
    for (locale, key), rows in sorted(groups.items()):
        hashes = {r['hash'] for r in rows if r['hash'] is not None}
        sources = {r['source'] for r in rows}
        if len(hashes) > 1:
            state = 'value_conflict'
        elif any(r['hash'] is None for r in rows):
            state = 'missing_value_evidence'
        elif sources == {'file'}:
            state = 'file_only'
        elif 'file' not in sources:
            state = 'database_only'
        else:
            state = 'matching_evidence'
        flags = []
        if any(r['source'] != 'dynamic_workbench' for r in rows) and len(key.split('.')) < 3:
            flags.append('key_requires_nested_array_review')
        if len(spellings[locale.lower().replace('_', '-')]) > 1:
            flags.append('locale_spelling_review')
        if 'dynamic_workbench' in sources and sources.intersection({'legacy', 'workbench'}):
            flags.append('scalar_option_identity_review')
        counts = Counter(r['source'] for r in rows)
        if any(n > 1 for n in counts.values()):
            flags.append('multiple_records_per_source')
        evidence = []
        for row in sorted(rows, key=lambda r: (r['source'], str(r['id']), r['hash'] or '')):
            entry = dict(row)
            if row['source'] == 'file':
                entry['file_sha256'] = files[row['id']]['sha256']
            evidence.append(entry)
        items.append({'review_id': hashlib.sha256(json.dumps([locale, key], ensure_ascii=False).encode()).hexdigest()[:16],
                      'locale': locale, 'key': key, 'state': state, 'flags': flags,
                      'decision': None, 'evidence': evidence,
                      'historical_evidence': history.get((locale, key), [])})
    return {'mode': 'review_only_no_import_decisions', 'source_captured_at': snapshot['captured_at'],
            'source_sha256': digest, 'summary': dict(sorted(Counter(i['state'] for i in items).items())),
            'flag_counts': dict(sorted(Counter(f for i in items for f in i['flags']).items())),
            'locale_variants': [sorted(v) for _, v in sorted(spellings.items()) if len(v) > 1],
            'blocked_files': [f for f in snapshot['lang_files'] if f['reader'] != 'supported'],
            'aggregate_checks_without_record_ids': snapshot['checks'],
            'dynamic_key_errors': snapshot.get('dynamic_key_errors', []),
            'historical_only_identity_count': len(set(history) - set(groups)),
            'items': items}


def cell(value):
    return str(value).replace('&', '&amp;').replace('<', '&lt;').replace('>', '&gt;').replace('|', '&#124;').replace('\n', ' ')


def markdown(report):
    lines = ['# Übernahme-Prüfliste Translation Workbench', '',
             f"Auditstand: {report['source_captured_at']}. Keine aktuelle Datenbankabfrage.",
             f"Snapshot-SHA256: `{report['source_sha256']}`", '',
             'Diese Liste entscheidet nichts automatisch. Übereinstimmende Hashes sind ein Inhaltsnachweis, keine Freigabe.',
             'Keine Texte werden aus Keys abgeleitet; NULL ist fehlender Nachweis und kein leerer Text.',
             'Die alten namespace-Spalten sind Herkunftsangaben, keine bestätigten Laravel-Paket-Namespaces.',
             'Historische/obsolete Einträge dienen nur als Nachweis; sie werden weder reaktiviert noch als aktuelle Gewinner gewählt.', '',
             '| Klasse | Identitäten (exakte Locale + Key) |', '| --- | ---: |']
    lines += [f'| {state} | {count} |' for state, count in report['summary'].items()]
    lines += ['', '| Zusätzlicher Prüfhinweis | Identitäten |', '| --- | ---: |']
    lines += [f'| {flag} | {count} |' for flag, count in report['flag_counts'].items()]
    lines += ['', 'Prüfhinweise können sich überschneiden; sie sind nicht zu den Klassen zu addieren.', '', '## Vorgehen', '',
              '1. Wertabweichungen mit den aufgeführten Datensatz-IDs und Dateien prüfen. Kein pauschales „DB gewinnt“ oder „Datei gewinnt“.',
              '2. DB-only-Werte sichern und Verwendung/Freigabe prüfen; file-only-Werte nicht als fehlende Übersetzung behandeln.',
              '3. Locale-Varianten, mehrfach vorhandene Datensätze und Scalar-/Optionsidentitäten getrennt klären.',
              '4. Nicht gelesene Paketdateien und dynamische Altformate ergänzen, bevor Vollständigkeit behauptet wird.',
              '5. Vor einer Generalprobe frischen Snapshot und Dateihashes vergleichen. Die Liste ist kein Importplan.', '',
              '## Offene Abdeckung', '',
              f"- {len(report['blocked_files'])} Dateien waren im Snapshot nicht als Wert-Evidenz lesbar.",
              f"- {report['historical_only_identity_count']} Identitäten besitzen ausschließlich historische/nicht ausgewählte Evidenz und sind keine aktuellen Übernahmekandidaten.",
              '- Alte dynamic_values, native Werte und strukturierte Optionsbeobachtungen sind hier noch nicht vollständig wertweise enthalten.',
              '- Die aggregierten Finding-/Link-Prüfungen enthalten keine einzelnen IDs; daraus werden keine erfundenen Korrekturaufträge erstellt.',
              '- Framework-Dateien sind Herkunftskontext und werden nicht als Anwendungseinträge übernommen.', '']
    for name, check in report['aggregate_checks_without_record_ids'].items():
        lines.append(f"- {name}: {check['count']} (Snapshotzählung, keine Übernahmeentscheidung).")
    lines += ['', '## Locale-Varianten', '']
    lines += [f"- {', '.join(v)}" for v in report['locale_variants']] or ['Keine Varianten in der ausgewählten Evidenz.']
    lines += ['', '## Nicht gelesene Dateien', '']
    lines += [f"- `{cell(f['path'])}`: {cell(f.get('reader_reason', ''))}" for f in report['blocked_files']]
    for state in ['value_conflict', 'missing_value_evidence', 'database_only', 'file_only', 'matching_evidence']:
        records = [i for i in report['items'] if i['state'] == state]
        lines += ['', f'## {state} ({len(records)})', '', '<details>', '<summary>Einträge mit Herkunft anzeigen</summary>', '',
                  '| Review-ID | Locale | Key | Herkunft / Datensatz / Wert-SHA256 (gekürzt) | Zusätzliche Prüfung |', '| --- | --- | --- | --- | --- |']
        for item in records:
            provenance = '; '.join(f"{r['source']} #{r['id']} [{(r['hash'] or 'NULL')[:12]}]" for r in item['evidence'])
            lines.append('| '+' | '.join(cell(v) for v in [item['review_id'], item['locale'], item['key'], provenance, ', '.join(item['flags'])])+' |')
        lines += ['', '</details>']
    lines += ['', 'Vollständige Hashes, Optionsidentitäten, Quell-IDs und historische Evidenz stehen in der zugehörigen JSON-Datei.', '']
    return '\n'.join(lines)


if __name__ == '__main__':
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument('snapshot', type=Path)
    parser.add_argument('output', type=Path, help='Output basename without extension')
    args = parser.parse_args()
    raw = args.snapshot.read_bytes()
    report = build(json.loads(raw), hashlib.sha256(raw).hexdigest())
    for suffix, content in [('.json', json.dumps(report, ensure_ascii=False, indent=2)+'\n'), ('.md', markdown(report))]:
        destination = args.output.with_suffix(suffix)
        if destination.resolve() == args.snapshot.resolve():
            raise ValueError('Cannot overwrite source snapshot')
        destination.write_text(content)
    print(json.dumps({'summary': report['summary'], 'flags': report['flag_counts'], 'items': len(report['items'])}))
