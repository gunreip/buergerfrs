-- Immutable provenance of baseline source usage, not a fabricated user review/event.
CREATE TABLE existing_key_imports (
    hash text PRIMARY KEY, manifest_sha256 text NOT NULL REFERENCES capture_imports,
    plan jsonb NOT NULL, UNIQUE(hash,manifest_sha256)
);
CREATE TABLE existing_key_observations (
    import_hash text NOT NULL, manifest_sha256 text NOT NULL, capture_id text NOT NULL,
    finding_id bigint NOT NULL REFERENCES translation_workbench_findings,
    state text NOT NULL CHECK(state IN ('eligible','blocked_structure')),
    key_id bigint REFERENCES translation_workbench_keys,
    link_id bigint REFERENCES translation_workbench_key_findings,
    PRIMARY KEY(import_hash,capture_id), UNIQUE(import_hash,finding_id),
    FOREIGN KEY(import_hash,manifest_sha256) REFERENCES existing_key_imports(hash,manifest_sha256),
    FOREIGN KEY(manifest_sha256,capture_id) REFERENCES capture_outcomes,
    CHECK ((state='eligible')=(key_id IS NOT NULL)), CHECK ((state='eligible')=(link_id IS NOT NULL))
);
CREATE TRIGGER immutable_key_import BEFORE UPDATE OR DELETE ON existing_key_imports FOR EACH ROW EXECUTE FUNCTION tw_immutable();
CREATE TRIGGER immutable_key_observation BEFORE UPDATE OR DELETE ON existing_key_observations FOR EACH ROW EXECUTE FUNCTION tw_immutable();
