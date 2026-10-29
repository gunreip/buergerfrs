"""Build a read-only mapping plan from verified backup metadata; never imports rows."""
import argparse
import hashlib
import json
from pathlib import Path

# Every old table is deliberately classified. New/unclassified tables fail closed.
GROUPS = {
    'fresh_capture_required': ['translation_workbench_findings', 'translation_workbench_entries',
                               'translation_workbench_occurrences', 'translation_usages',
                               'translation_workbench_source_files'],
    'key_identity_review': ['translation_keys', 'translation_workbench_keys', 'translation_workbench_key_inventory'],
    'assignment_review': ['translation_workbench_key_findings'],
    'value_revision_review': ['translation_values', 'translation_workbench_key_values',
                            'translation_workbench_lang_values', 'translation_workbench_dynamic_key_values'],
    'dynamic_context_review': ['translation_workbench_dynamic_source_candidates',
                              'translation_workbench_dynamic_source_values', 'translation_workbench_dynamic_sources',
                              'translation_workbench_dynamic_values', 'translation_workbench_option_discoveries'],
    'native_text_evidence': ['translation_workbench_values'],
    'recompute_candidates': ['translation_workbench_duplicate_candidates', 'translation_workbench_shared_key_candidates'],
    'configuration_review': ['translation_languages'],
    'historical_decision_only': ['translation_lang_ballast_decisions', 'translation_usage_audit_decision_usages',
                               'translation_usage_audit_decisions', 'translation_workbench_reviews'],
    'archive_only': ['translation_audit_events', 'translation_workbench_events', 'translation_workbench_timeline_events',
                     'translation_workbench_timeline_chains', 'translation_workbench_pipeline_runs',
                     'translation_workbench_pipeline_run_steps', 'translation_workbench_event_types'],
}

FIELDS = {
    'raw_expression': {
        'legacy_candidates': {'translation_workbench_findings': ['raw_expression'],
                              'translation_workbench_occurrences': ['raw_expression']},
        'required_evidence': 'Fresh static parser capture of the exact expression in a hashed copied source file.',
        'on_missing': 'block_finding',
    },
    'raw_source_code_literal': {
        'legacy_candidates': {'translation_workbench_findings': ['literal_text', 'found_translation_key']},
        'required_evidence': 'Fresh static parser capture of the actual argument, classified as literal or translation key; never concatenate legacy fields.',
        'on_missing': 'null',
    },
    'raw_literal': {
        'legacy_candidates': {'translation_workbench_findings': ['literal_text']},
        'required_evidence': 'Actual source text classified as a literal by fresh capture. A translation key is not a literal; ambiguity remains unresolved.',
        'on_missing': 'null',
    },
    'suggested_literal': {
        'legacy_candidates': {'translation_workbench_findings': ['literal_text_suggested'],
                              'translation_workbench_lang_values': ['locale', 'translation_key', 'value', 'source_path', 'source_hash']},
        'required_evidence': 'Unique scalar text at the exact captured key and explicit locale in the saved lang files; retain file hash, key and locale. No cross-key text matching or silent fallback.',
        'on_missing': 'null',
    },
    'suggested_key': {
        'legacy_candidates': {'translation_workbench_findings': ['suggested_key'],
                              'translation_workbench_entries': ['suggested_key'],
                              'translation_workbench_occurrences': ['suggested_key']},
        'required_evidence': 'New suggestion generated once by the fresh scanner for a literal, with generator version and inputs. An existing key is captured separately; old mutable suggestions are evidence only.',
        'on_missing': 'null',
    },
}


def sha(path):
    return hashlib.sha256(path.read_bytes()).hexdigest()


def prepare(manifest, staged):
    if manifest['state'] != 'backup_and_restore_verified' or staged['state'] != 'evidence_staged_and_verified':
        raise ValueError('Verified backup and staging required')
    if staged['production_connected'] or staged['active_generation_created'] or staged['source_read_only'] != 'on' or not staged['cluster_stopped']:
        raise ValueError('Unexpected staging state')
    tables = {name.split('"')[3]: info for name, info in manifest['database_tables'].items()
              if name.startswith('"public"."translation') or name.startswith('"public"."dynamic_translation')}
    classified = [t for names in GROUPS.values() for t in names]
    if len(classified) != len(set(classified)) or set(tables) != set(classified) or set(tables) != set(staged['collections']):
        raise ValueError('Unclassified, duplicate or missing source table')
    columns = {}
    for col in manifest['database_structure']['columns']:
        if col['table_schema'] == 'public':
            columns.setdefault(col['table_name'], set()).add(col['column_name'])
    for rule in FIELDS.values():
        for table, names in rule['legacy_candidates'].items():
            if not set(names) <= columns.get(table, set()):
                raise ValueError('Missing evidence column in '+table)
    rows = []
    for purpose, names in GROUPS.items():
        for table in names:
            evidence = staged['collections'][table]
            disposition = 'retained_in_restored_archive' if purpose == 'archive_only' else 'copied_as_evidence'
            if evidence['disposition'] != disposition or evidence['source_rows'] != tables[table]['rows']:
                raise ValueError('Evidence classification/count drift: '+table)
            rows.append({'source_table': table, 'source_rows': tables[table]['rows'],
                         'source_multiset_sha256': tables[table]['row_multiset_sha256'],
                         'purpose': purpose, 'automatic_active_import': False})
    return {
        'mode': 'mapping_only', 'active_generation_created': False,
        'backup_manifest_sha256': staged['backup_manifest_sha256'],
        'staging_dump_sha256': staged['staging_dump_sha256'],
        'tables': rows, 'capture_fields': FIELDS,
        'identity_mapping': {
            'evidence_identity': ['batch_id', 'source_table', 'source_id'],
            'target_identity': ['generation_id', 'target_table', 'target_id'],
            'rule': 'Allocate new IDs. Record explicit evidence-to-target links with decision provenance; never equate finding IDs and key IDs or match by numeric ID alone.',
            'finding_rule': 'Fresh capture creates distinct findings per source position. Old findings remain archived; matching literal text does not merge findings.',
            'key_rule': 'Findings link to independent keys. Equal literals do not automatically merge keys. Name/namespace conflicts require a decision.',
        },
        'activation_requirements': [
            'Fresh capture from the hashed copied source, with exact line and column and completed scan scope.',
            'Explicit key identity and finding assignment decisions; no legacy ownership semantics.',
            'Reviewed locale, namespace and scalar/option identity; no silent normalization.',
            'New revisions carry actual import actor and reason; old approvals never authorize publication.',
            'Old audit and timeline events remain archived; new history begins with real new operations.',
            'Every staged row is accounted for as linked evidence, pending review or archived; none silently discarded.',
        ],
        'open_work': ['fresh_source_capture_adapter', 'per_record_identity_map', 'reviewed_value_import',
                      'loss_accounting', 'workflow_replay'],
    }


if __name__ == '__main__':
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument('output', type=Path)
    args = parser.parse_args()
    directory = Path(__file__).resolve().parent
    root = directory.parents[2]
    backup_result = json.loads((directory/'backup-restore-result-2026-10-19.json').read_text())
    manifest_path = root/backup_result['backup_manifest']
    if sha(manifest_path) != backup_result['backup_manifest_sha256']:
        raise ValueError('Backup manifest changed')
    staging_path = directory/'evidence-staging-result-2026-10-19.json'
    staged = json.loads(staging_path.read_text())
    if staged['backup_manifest_sha256'] != sha(manifest_path) or sha(root/staged['staging_dump']) != staged['staging_dump_sha256']:
        raise ValueError('Staging evidence changed')
    plan = prepare(json.loads(manifest_path.read_text()), staged)
    plan['staging_report_sha256'] = sha(staging_path)
    with args.output.open('x') as stream:
        stream.write(json.dumps(plan, indent=2, ensure_ascii=False)+'\n')
    print(json.dumps({'classified_tables': len(plan['tables']), 'active_imports': 0}))
