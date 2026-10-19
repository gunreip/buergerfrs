"""Static schema/reference inventory; no application boot or database access.
References are search evidence, not proof of runtime reachability or writes.
"""
import json
import re
from pathlib import Path

ROOT = Path(__file__).resolve().parents[3]
PACKAGE = ROOT / 'packages/gunreip/laravel-translation-workbench'
OUT = Path(__file__).with_name('schema-references-2026-10-19.json')
migrations = sorted((ROOT / 'database/migrations').glob('*.php')) + sorted((PACKAGE / 'database/migrations').glob('*.php'))
tables = {}
for path in migrations:
    source = path.read_text()
    for match in re.finditer(r"Schema::create\('((?:translation_)[^']+)'", source):
        tables[match[1]] = {'creation': str(path.relative_to(ROOT)), 'schema_files': [], 'models': [], 'references': []}
for path in migrations:
    source = path.read_text()
    for table, item in tables.items():
        if table in source:
            item['schema_files'].append(str(path.relative_to(ROOT)))
roots = [ROOT / 'app', ROOT / 'routes', ROOT / 'resources/views', PACKAGE / 'src', PACKAGE / 'routes', PACKAGE / 'resources/views']
files = sorted({p for root in roots for p in root.rglob('*.php')})
for path in files:
    source = path.read_text()
    match = re.search(r"protected\s+\$table\s*=\s*'([^']+)'", source)
    inferred = re.sub(r'(?<!^)(?=[A-Z])', '_', path.stem).lower() + 's'
    table = match[1] if match else inferred if 'Models' in path.parts else None
    if table in tables:
        tables[table]['models'].append(path.stem)
tokens = {}
for table, item in tables.items():
    for token in [table, *item['models']]:
        tokens.setdefault(token, set()).add(table)
pattern = re.compile(r'(?<![A-Za-z0-9_])(?:' + '|'.join(map(re.escape, sorted(tokens, key=len, reverse=True))) + r')(?![A-Za-z0-9_])')
for path in files:
    archived = any(part in {'_old', '-old', 'old'} or part.endswith('-old') for part in path.parts)
    for number, line in enumerate(path.read_text().splitlines(), 1):
        matched = {table for match in pattern.finditer(line) for table in tokens[match[0]]}
        for table in sorted(matched):
            tables[table]['references'].append({'file': str(path.relative_to(ROOT)), 'line': number, 'archived_path': archived, 'text': line.strip()})
OUT.write_text(json.dumps({'scope': 'Static migration and PHP/Blade references; excludes tests, vendor, audit scripts. Includes comments; non-archived does not imply registered or executed. Dynamic table-name construction can be missed. No live database inventory.', 'table_count': len(tables), 'tables': tables}, indent=2, ensure_ascii=False) + '\n')
print(f'{len(tables)} tables; output: {OUT.relative_to(ROOT)}')
for table, item in tables.items():
    refs = {r['file'] for r in item['references'] if not r['archived_path']}
    print(f'{table}: {len(refs)} non-archive source files')
