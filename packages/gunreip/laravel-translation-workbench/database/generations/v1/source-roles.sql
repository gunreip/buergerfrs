-- Additive rehearsal projection. Original capture outcomes and raw fields stay immutable.
CREATE TABLE capture_source_roles (
    manifest_sha256 text NOT NULL,
    capture_id text NOT NULL,
    review_sha256 text NOT NULL,
    role text NOT NULL CHECK (role IN ('translation_source','option_source','value_usage','excluded')),
    state text NOT NULL CHECK (state IN ('captured','pending','excluded')),
    evidence jsonb NOT NULL,
    finding_id bigint REFERENCES translation_workbench_findings,
    PRIMARY KEY (manifest_sha256,capture_id),
    FOREIGN KEY (manifest_sha256,capture_id) REFERENCES capture_outcomes,
    CHECK ((state='captured')=(finding_id IS NOT NULL)),
    CHECK (state<>'captured' OR role='translation_source'),
    CHECK ((state='excluded')=(role='excluded'))
);
CREATE TRIGGER immutable_source_role BEFORE UPDATE OR DELETE ON capture_source_roles
FOR EACH ROW EXECUTE FUNCTION tw_immutable();
