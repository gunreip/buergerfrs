CREATE TABLE capture_imports (
    manifest_sha256 text PRIMARY KEY,
    candidates_sha256 text NOT NULL,
    scan_id uuid NOT NULL REFERENCES translation_workbench_scan_runs,
    totals jsonb NOT NULL
);
CREATE TABLE capture_outcomes (
    manifest_sha256 text NOT NULL REFERENCES capture_imports DEFERRABLE INITIALLY DEFERRED,
    capture_id text NOT NULL,
    disposition text NOT NULL CHECK(disposition IN ('captured','excluded','pending')),
    reason text NOT NULL,
    finding_id bigint REFERENCES translation_workbench_findings,
    CHECK ((disposition='captured')=(finding_id IS NOT NULL)),
    PRIMARY KEY(manifest_sha256,capture_id),
    UNIQUE(finding_id)
);
CREATE TRIGGER immutable_import BEFORE UPDATE OR DELETE ON capture_imports
FOR EACH ROW EXECUTE FUNCTION tw_immutable();
CREATE TRIGGER immutable_outcome BEFORE UPDATE OR DELETE ON capture_outcomes
FOR EACH ROW EXECUTE FUNCTION tw_immutable();
