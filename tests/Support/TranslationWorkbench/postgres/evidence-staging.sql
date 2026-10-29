-- Isolated evidence archive only. No active findings, keys, reviews, or timeline tables.
CREATE TABLE evidence_batches (
    id text PRIMARY KEY CHECK (id ~ '^[a-f0-9]{64}$'),
    backup_manifest_sha256 text NOT NULL,
    source_dump_sha256 text NOT NULL,
    created_at timestamptz NOT NULL DEFAULT now()
);
CREATE TABLE evidence_collections (
    batch_id text NOT NULL REFERENCES evidence_batches(id),
    source_table text NOT NULL,
    disposition text NOT NULL CHECK(disposition IN ('copied_as_evidence','retained_in_restored_archive')),
    source_rows bigint NOT NULL,
    source_multiset_sha256 text NOT NULL,
    PRIMARY KEY(batch_id,source_table)
);
CREATE TABLE evidence_records (
    id bigint GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    batch_id text NOT NULL,
    source_table text NOT NULL,
    source_id text NOT NULL,
    raw_payload text NOT NULL CHECK(jsonb_typeof(raw_payload::jsonb)='object'),
    payload_sha256 text NOT NULL CHECK(payload_sha256 ~ '^[a-f0-9]{64}$'),
    UNIQUE(batch_id,source_table,source_id),
    FOREIGN KEY(batch_id,source_table) REFERENCES evidence_collections(batch_id,source_table)
);
CREATE TABLE evidence_files (
    batch_id text NOT NULL REFERENCES evidence_batches(id),
    source_path text NOT NULL,
    file_sha256 text NOT NULL CHECK(file_sha256 ~ '^[a-f0-9]{64}$'),
    file_bytes bigint NOT NULL CHECK(file_bytes>=0),
    PRIMARY KEY(batch_id,source_path)
);
CREATE FUNCTION prevent_evidence_change() RETURNS trigger LANGUAGE plpgsql AS $$
BEGIN
    RAISE EXCEPTION 'Evidence is immutable';
END
$$;
CREATE TRIGGER immutable_records BEFORE UPDATE OR DELETE ON evidence_records FOR EACH ROW EXECUTE FUNCTION prevent_evidence_change();
CREATE TRIGGER immutable_collections BEFORE UPDATE OR DELETE ON evidence_collections FOR EACH ROW EXECUTE FUNCTION prevent_evidence_change();
CREATE TRIGGER immutable_files BEFORE UPDATE OR DELETE ON evidence_files FOR EACH ROW EXECUTE FUNCTION prevent_evidence_change();
CREATE TRIGGER immutable_batches BEFORE UPDATE OR DELETE ON evidence_batches FOR EACH ROW EXECUTE FUNCTION prevent_evidence_change();
