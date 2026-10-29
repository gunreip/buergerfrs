-- Immutable file baseline; deliberately independent of authored revisions and reviews.
CREATE TABLE published_value_baselines (
    hash text PRIMARY KEY, backup_manifest_sha256 text NOT NULL,
    excluded_sources jsonb NOT NULL, totals jsonb NOT NULL
);
CREATE TABLE published_value_sources (
    baseline_hash text NOT NULL REFERENCES published_value_baselines DEFERRABLE INITIALLY DEFERRED,
    path text NOT NULL, sha256 text NOT NULL CHECK(sha256 ~ '^[a-f0-9]{64}$'),
    locale text NOT NULL, file_group text NOT NULL,
    PRIMARY KEY(baseline_hash,path), UNIQUE(baseline_hash,path,locale)
);
CREATE TABLE published_value_evidence (
    baseline_hash text NOT NULL, path text NOT NULL, locale text NOT NULL,
    full_name text NOT NULL, value text NOT NULL,
    state text NOT NULL CHECK(state IN ('linked','blocked_structure','unassigned_key')),
    key_id bigint REFERENCES translation_workbench_keys,
    PRIMARY KEY(baseline_hash,locale,full_name),
    FOREIGN KEY(baseline_hash,path,locale) REFERENCES published_value_sources(baseline_hash,path,locale),
    CHECK((state='linked')=(key_id IS NOT NULL))
);
CREATE TRIGGER immutable_published_baseline BEFORE UPDATE OR DELETE ON published_value_baselines FOR EACH ROW EXECUTE FUNCTION tw_immutable();
CREATE TRIGGER immutable_published_source BEFORE UPDATE OR DELETE ON published_value_sources FOR EACH ROW EXECUTE FUNCTION tw_immutable();
CREATE TRIGGER immutable_published_value BEFORE UPDATE OR DELETE ON published_value_evidence FOR EACH ROW EXECUTE FUNCTION tw_immutable();
