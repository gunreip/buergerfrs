"""Offline regression checks for the audit-derived review list."""
import importlib.util
from pathlib import Path
import unittest

root = Path(__file__).resolve().parents[3]
spec = importlib.util.spec_from_file_location('checklist', root / 'docs/audits/translation-workbench-2026-10-15/build-transfer-checklist.py')
module = importlib.util.module_from_spec(spec)
spec.loader.exec_module(module)


def row(source='workbench', identity=1, value='a', status='active', locale='en'):
    return dict(source=source, id=identity, key='ui.example.label', locale=locale, hash=value, status=status)


def report(rows):
    return module.build(dict(captured_at='snapshot', scalar_evidence=rows,
                            lang_files=[dict(path='lang/en/ui.php', sha256='file-hash', reader='supported')], checks={}), 'snapshot-hash')


class ChecklistTest(unittest.TestCase):
    def test_conflicting_values_inside_one_source_are_not_collapsed(self):
        result = report([row(value='a'), row(identity=2, value='b')])['items'][0]
        self.assertEqual(result['state'], 'value_conflict')
        self.assertEqual(len(result['evidence']), 2)
        self.assertIn('multiple_records_per_source', result['flags'])
        self.assertIsNone(result['decision'])

    def test_same_conflicting_sets_in_two_sources_are_still_conflicts(self):
        rows = [row(value='a'), row(identity=2, value='b'),
                row('legacy', 3, 'a', 'ok'), row('legacy', 4, 'b', 'ok')]
        self.assertEqual(report(rows)['items'][0]['state'], 'value_conflict')

    def test_null_is_not_an_empty_value_hash(self):
        self.assertEqual(report([row(value=None)])['items'][0]['state'], 'missing_value_evidence')
        self.assertEqual(report([row(value='empty-string-hash')])['items'][0]['state'], 'database_only')

    def test_historical_difference_does_not_replace_current_evidence(self):
        item = report([row(), row(identity=2, value='old', status='obsolete')])['items'][0]
        self.assertEqual(item['state'], 'database_only')
        self.assertEqual(item['historical_evidence'][0]['hash'], 'old')

    def test_file_provenance_and_matching_do_not_imply_approval(self):
        item = report([row(), row('file', 'lang/en/ui.php', 'a', 'observed')])['items'][0]
        self.assertEqual(item['state'], 'matching_evidence')
        self.assertIsNone(item['decision'])
        self.assertEqual(item['evidence'][0]['file_sha256'], 'file-hash')

    def test_locale_variants_remain_separate(self):
        result = report([row(locale='de-AT'), row(identity=2, locale='de_at')])
        self.assertEqual(len(result['items']), 2)
        self.assertTrue(all('locale_spelling_review' in i['flags'] for i in result['items']))

    def test_framework_is_not_an_application_transfer_candidate(self):
        result = report([row('file', 'vendor/framework/lang/en/ui.php', 'a', 'observed')])
        self.assertEqual(result['items'], [])

    def test_grouping_is_order_independent(self):
        rows = [row(), row('file', 'lang/en/ui.php', 'a', 'observed')]
        self.assertEqual(report(rows), report(list(reversed(rows))))


if __name__ == '__main__':
    unittest.main()
