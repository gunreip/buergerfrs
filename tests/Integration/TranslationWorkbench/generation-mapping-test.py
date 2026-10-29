"""Offline tests: mapping must fail closed when evidence coverage drifts."""
import copy
import importlib.util
from pathlib import Path
import unittest

ROOT = Path(__file__).resolve().parents[3]
SPEC = importlib.util.spec_from_file_location('mapping', ROOT/'docs/audits/translation-workbench-2026-10-15/build-generation-mapping.py')
module = importlib.util.module_from_spec(SPEC)
SPEC.loader.exec_module(module)


class MappingTest(unittest.TestCase):
    def setUp(self):
        self.manifest = {'state': 'backup_and_restore_verified', 'database_tables': {},
                         'database_structure': {'columns': []}}
        self.stage = {'state': 'evidence_staged_and_verified', 'production_connected': False,
                      'active_generation_created': False, 'source_read_only': 'on', 'cluster_stopped': True,
                      'backup_manifest_sha256': 'a'*64, 'staging_dump_sha256': 'b'*64, 'collections': {}}
        for group, tables in module.GROUPS.items():
            for table in tables:
                self.manifest['database_tables']['"public"."'+table+'"'] = {'rows': 2, 'row_multiset_sha256': 'c'*64}
                self.stage['collections'][table] = {'source_rows': 2, 'disposition':
                    'retained_in_restored_archive' if group == 'archive_only' else 'copied_as_evidence'}
        for rule in module.FIELDS.values():
            for table, columns in rule['legacy_candidates'].items():
                for column in columns:
                    self.manifest['database_structure']['columns'].append(
                        {'table_schema': 'public', 'table_name': table, 'column_name': column})

    def test_complete_plan_preserves_inputs_and_cannot_activate(self):
        before = copy.deepcopy((self.manifest, self.stage))
        plan = module.prepare(self.manifest, self.stage)
        self.assertEqual(before, (self.manifest, self.stage))
        self.assertEqual(len(plan['tables']), 33)
        self.assertFalse(any(row['automatic_active_import'] for row in plan['tables']))
        self.assertEqual(plan['capture_fields']['suggested_key']['on_missing'], 'null')
        self.assertEqual(plan['capture_fields']['raw_expression']['on_missing'], 'block_finding')

    def test_new_table_requires_explicit_mapping(self):
        self.manifest['database_tables']['"public"."translation_new"'] = {'rows': 0}
        with self.assertRaisesRegex(ValueError, 'Unclassified'):
            module.prepare(self.manifest, self.stage)

    def test_missing_collection_is_not_silently_discarded(self):
        del self.stage['collections']['translation_workbench_values']
        with self.assertRaises(ValueError):
            module.prepare(self.manifest, self.stage)

    def test_missing_column_is_not_silently_mapped(self):
        self.manifest['database_structure']['columns'] = [c for c in self.manifest['database_structure']['columns']
                                                         if c['column_name'] != 'literal_text']
        with self.assertRaisesRegex(ValueError, 'Missing evidence column'):
            module.prepare(self.manifest, self.stage)

    def test_old_history_cannot_be_treated_as_active_evidence(self):
        self.stage['collections']['translation_workbench_events']['disposition'] = 'copied_as_evidence'
        with self.assertRaisesRegex(ValueError, 'classification/count drift'):
            module.prepare(self.manifest, self.stage)

    def test_row_loss_is_rejected(self):
        self.stage['collections']['translation_workbench_findings']['source_rows'] -= 1
        with self.assertRaises(ValueError):
            module.prepare(self.manifest, self.stage)

    def test_live_or_unverified_state_is_rejected(self):
        for key, value in [('production_connected', True), ('active_generation_created', True),
                           ('source_read_only', 'off'), ('state', 'incomplete'), ('cluster_stopped', False)]:
            with self.subTest(key=key):
                staged = dict(self.stage, **{key: value})
                with self.assertRaises(ValueError):
                    module.prepare(self.manifest, staged)


if __name__ == '__main__':
    unittest.main()
