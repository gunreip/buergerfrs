-- Rehearsal only. Evidence is not a translation value, key assignment or approval.
CREATE TABLE source_argument_batches (
    report_sha256 text PRIMARY KEY CHECK (report_sha256 ~ '^[a-f0-9]{64}$'),
    manifest_sha256 text NOT NULL REFERENCES capture_imports(manifest_sha256),
    review_sha256 text NOT NULL,
    UNIQUE(report_sha256,manifest_sha256)
);
CREATE TABLE source_argument_producers (
    report_sha256 text NOT NULL REFERENCES source_argument_batches,
    name text NOT NULL,
    evidence jsonb NOT NULL CHECK (jsonb_typeof(evidence)='object'),
    PRIMARY KEY(report_sha256,name)
);
CREATE TABLE source_argument_evidence (
    report_sha256 text NOT NULL,
    manifest_sha256 text NOT NULL,
    capture_id text NOT NULL,
    kind text NOT NULL CHECK(kind IN ('option_arguments','overridable_default','runtime_value_required','producer_observations','literal_key_decision_required')),
    producer_name text,
    evidence jsonb NOT NULL CHECK (jsonb_typeof(evidence)='object'),
    PRIMARY KEY(report_sha256,capture_id),
    FOREIGN KEY(report_sha256,manifest_sha256) REFERENCES source_argument_batches(report_sha256,manifest_sha256),
    FOREIGN KEY(manifest_sha256,capture_id) REFERENCES capture_source_roles,
    FOREIGN KEY(report_sha256,producer_name) REFERENCES source_argument_producers,
    CHECK ((kind='producer_observations')=(producer_name IS NOT NULL))
);
CREATE FUNCTION tw_source_argument_guard() RETURNS trigger LANGUAGE plpgsql AS $$
DECLARE r capture_source_roles; b source_argument_batches;
BEGIN
    SELECT * INTO STRICT r FROM capture_source_roles WHERE manifest_sha256=NEW.manifest_sha256 AND capture_id=NEW.capture_id;
    SELECT * INTO STRICT b FROM source_argument_batches WHERE report_sha256=NEW.report_sha256;
    IF r.state <> 'pending' OR r.review_sha256 <> b.review_sha256 OR
       (NEW.kind='option_arguments' AND r.role<>'option_source') OR
       (NEW.kind<>'option_arguments' AND r.role<>'translation_source') THEN
        RAISE EXCEPTION 'Argument evidence contradicts capture role or review';
    END IF;
    RETURN NEW;
END $$;
CREATE TRIGGER argument_role_guard BEFORE INSERT ON source_argument_evidence FOR EACH ROW EXECUTE FUNCTION tw_source_argument_guard();
CREATE TRIGGER immutable_argument_batch BEFORE UPDATE OR DELETE ON source_argument_batches FOR EACH ROW EXECUTE FUNCTION tw_immutable();
CREATE TRIGGER immutable_argument_producer BEFORE UPDATE OR DELETE ON source_argument_producers FOR EACH ROW EXECUTE FUNCTION tw_immutable();
CREATE TRIGGER immutable_argument_evidence BEFORE UPDATE OR DELETE ON source_argument_evidence FOR EACH ROW EXECUTE FUNCTION tw_immutable();
