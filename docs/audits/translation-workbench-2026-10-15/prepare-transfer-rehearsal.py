"""Offline rehearsal preparation; neither database access nor imports/reset operations."""
import argparse
import hashlib
import json
from pathlib import Path

FILES = {
    'checklist': 'transfer-checklist-2026-10-19.json',
    'conflicts': 'transfer-conflict-details-2026-10-19.json',
    'missing': 'transfer-missing-details-2026-10-19.json',
    'database_only': 'transfer-database-only-details-2026-10-19.json',
    'extra': 'transfer-extra-evidence-2026-10-19.json',
    'native': 'transfer-native-values-2026-10-19.json',
    'scope': 'archive-scope-2026-10-19.json',
}


def prepare(directory):
    evidence, reports = {}, {}
    for name, filename in FILES.items():
        raw = (directory / filename).read_bytes()
        reports[name] = json.loads(raw)
        evidence[name] = {'file': filename, 'sha256': hashlib.sha256(raw).hexdigest()}
    work = []

    def item(identity, topic, refs, proposal):
        work.append({'id': identity, 'topic': topic, 'evidence': refs,
                     'proposal': proposal, 'status': 'open', 'decision': None})

    for row in reports['conflicts']['items']:
        item('value-'+row['review_id'], 'translation_value',
             {'report': 'conflicts', 'review_id': row['review_id'], 'locale': row['locale'], 'key': row['key']},
             'Retain all distinct texts and provenance; choose in a reviewed value decision.')
    for variants in reports['checklist']['locale_variants']:
        identity = hashlib.sha256(json.dumps(variants).encode()).hexdigest()[:16]
        item('locale-'+identity, 'locale_mapping', {'report': 'checklist', 'variants': variants},
             'Define locale mapping and fallback separately; never silently normalize or overwrite values.')
    for row in reports['extra']['old_option_comparison']:
        item('old-option-'+str(row['old_id']), 'option_assignment',
             {'report': 'extra', 'old_value_id': row['old_id']},
             'Text-only candidates do not authorize a key assignment; retain the old record as evidence.')
    for row in reports['database_only']['items']:
        if row['assessment'] in ('same_text_other_option_key', 'separate_database_evidence'):
            item('db-only-'+row['review_id'], 'database_only_context',
                 {'report': 'database_only', 'review_id': row['review_id']},
                 'Retain exact text and identity; review usage before activation or reassignment.')
    for row in reports['extra']['package_differences']:
        identity = hashlib.sha256(json.dumps([row['path'], row['key']]).encode()).hexdigest()[:16]
        item('package-'+identity, 'package_override',
             {'report': 'extra', 'path': row['path'], 'key': row['key']},
             'Preserve project override and installed package value; replacement needs a reviewed decision.')
    for row in reports['native']['comparison']:
        if row['classification'] in ('no_current_text_witness', 'structured_payload_review'):
            item('native-'+str(row['native_id']), 'native_evidence',
                 {'report': 'native', 'native_id': row['native_id'], 'classification': row['classification']},
                 'Preserve the original payload; do not reactivate obsolete entries or turn containers into scalar translations.')
    item('finding-link-integrity', 'fresh_integrity_audit',
         {'report': 'checklist', 'counts': reports['checklist']['aggregate_checks_without_record_ids']},
         'Resolve exact affected IDs in the fresh copy; aggregate counts are not per-record repair instructions.')
    item('parent-source-status', 'dynamic_source_lifecycle',
         {'report': 'extra', 'counts': reports['extra']['source_value_parent_status_counts']},
         'Archive obsolete-parent evidence; child active status alone does not prove current usage.')
    gates = [
        ('consistent_backup', 'Capture and checksum a coherent DB/source/lang baseline; record excluded external dependencies.'),
        ('restore_verified', 'Restore that backup into a new isolated PostgreSQL cluster and prove row counts and value hashes.'),
        ('evidence_importer', 'Implement copy-only evidence staging with explicit legacy-to-new ID mapping and loss accounting.'),
        ('fresh_capture', 'Exercise actual scanner capture on the copied source; fixture capture is not a scanner replacement.'),
        ('unresolved_quarantine', 'Keep undecided evidence separate from active assignments, approvals and publication.'),
        ('workflow_replay', 'Test first/repeated scan, move, removal, copied/handmade, shared candidates, approvals and interrupted writes on the copy.'),
        ('comparison_report', 'Prove no unique text lost, no obsolete finding reactivated, no old history replayed into the new timeline.'),
    ]
    return {'mode': 'preparation_only', 'production_reset_authorized_by_this_manifest': False,
            'source_reports_are_one_consistent_backup': False, 'evidence': evidence,
            'policies': ['immutable_capture_and_suggested_key', 'findings_independent_of_keys',
                         'no_automatic_equal_literal_merge', 'no_obsolete_reactivation',
                         'retain_source_and_published_lang', 'old_history_archived_not_replayed',
                         'no_automatic_approval_or_publication'],
            'work_items': work, 'rehearsal_gates': [{'id': k, 'status': 'not_completed', 'requirement': v} for k, v in gates],
            'production_gates': ['single_writer_cutover', 'ui_and_command_integration', 'explicit_review_of_cutover_result']}


def validate(manifest, directory):
    if manifest['mode'] != 'preparation_only' or manifest['production_reset_authorized_by_this_manifest']:
        raise ValueError('This is not a reset authorization')
    if manifest['source_reports_are_one_consistent_backup']:
        raise ValueError('Separate audits are not a coherent backup')
    ids = [w['id'] for w in manifest['work_items']]
    if len(ids) != len(set(ids)):
        raise ValueError('Duplicate work item IDs')
    for entry in manifest['evidence'].values():
        path = directory / entry['file']
        if path.resolve().parent != directory.resolve():
            raise ValueError('Evidence path outside audit directory')
        if hashlib.sha256(path.read_bytes()).hexdigest() != entry['sha256']:
            raise ValueError('Evidence changed: '+entry['file'])
    for w in manifest['work_items']:
        if w['status'] != 'open' or w['decision'] is not None:
            raise ValueError('Preparation generator cannot authorize decisions')
    return {'evidence_files': len(manifest['evidence']), 'open_items': len(ids),
            'rehearsal_ready': False, 'reset_ready': False}


if __name__ == '__main__':
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument('mode', choices=['build', 'check'])
    parser.add_argument('manifest', type=Path)
    args = parser.parse_args()
    directory = Path(__file__).resolve().parent
    if args.mode == 'build':
        if args.manifest.exists():
            raise ValueError('Manifest already exists; inspect it instead of overwriting decisions')
        manifest = prepare(directory)
        result = validate(manifest, directory)
        args.manifest.write_text(json.dumps(manifest, ensure_ascii=False, indent=2)+'\n')
    else:
        result = validate(json.loads(args.manifest.read_text()), directory)
    print(json.dumps(result))
