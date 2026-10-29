import copy
import hashlib
import importlib.util
import json
from pathlib import Path
import tempfile
import unittest

root = Path(__file__).resolve().parents[3]
spec = importlib.util.spec_from_file_location('rehearsal', root / 'docs/audits/translation-workbench-2026-10-15/prepare-transfer-rehearsal.py')
module = importlib.util.module_from_spec(spec)
spec.loader.exec_module(module)


class RehearsalManifestTest(unittest.TestCase):
    def setUp(self):
        self.temp = tempfile.TemporaryDirectory()
        self.directory = Path(self.temp.name)
        self.raw = b'{"evidence":"unchanged"}'
        (self.directory / 'evidence.json').write_bytes(self.raw)
        self.manifest = {'mode': 'preparation_only', 'production_reset_authorized_by_this_manifest': False,
                         'source_reports_are_one_consistent_backup': False,
                         'evidence': {'test': {'file': 'evidence.json', 'sha256': hashlib.sha256(self.raw).hexdigest()}},
                         'work_items': [{'id': 'one', 'status': 'open', 'decision': None}]}

    def tearDown(self):
        self.temp.cleanup()

    def test_valid_preparation_never_claims_reset_or_rehearsal_ready(self):
        result = module.validate(self.manifest, self.directory)
        self.assertFalse(result['reset_ready'])
        self.assertFalse(result['rehearsal_ready'])

    def test_modified_evidence_is_rejected(self):
        (self.directory / 'evidence.json').write_text('{}')
        with self.assertRaisesRegex(ValueError, 'Evidence changed'):
            module.validate(self.manifest, self.directory)

    def test_duplicate_decision_ids_are_rejected(self):
        self.manifest['work_items'].append(copy.deepcopy(self.manifest['work_items'][0]))
        with self.assertRaisesRegex(ValueError, 'Duplicate'):
            module.validate(self.manifest, self.directory)

    def test_implicit_approval_is_rejected(self):
        self.manifest['work_items'][0]['decision'] = 'publish'
        with self.assertRaisesRegex(ValueError, 'cannot authorize'):
            module.validate(self.manifest, self.directory)

    def test_audits_cannot_be_declared_a_backup(self):
        self.manifest['source_reports_are_one_consistent_backup'] = True
        with self.assertRaisesRegex(ValueError, 'coherent backup'):
            module.validate(self.manifest, self.directory)

    def test_real_manifest_validates_and_is_reproducible(self):
        directory = root / 'docs/audits/translation-workbench-2026-10-15'
        actual = json.loads((directory / 'transfer-rehearsal-manifest-2026-10-19.json').read_text())
        self.assertEqual(module.prepare(directory), actual)
        self.assertEqual(module.validate(actual, directory)['open_items'], len(actual['work_items']))


if __name__ == '__main__':
    unittest.main()
