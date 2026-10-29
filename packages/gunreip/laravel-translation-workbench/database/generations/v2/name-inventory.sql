-- Namespace evidence is separate from active keys, assignments and translations.
CREATE TABLE baseline_name_inventories (
    baseline_hash text PRIMARY KEY REFERENCES published_value_baselines(hash),
    source_baseline_hash text NOT NULL,
    names jsonb NOT NULL CHECK(jsonb_typeof(names)='array'),
    names_sha256 text NOT NULL CHECK(names_sha256 ~ '^[a-f0-9]{64}$'),
    name_count integer NOT NULL CHECK(name_count>0),
    CHECK(jsonb_array_length(names)=name_count)
);
CREATE TRIGGER immutable_name_inventory BEFORE UPDATE OR DELETE ON baseline_name_inventories
FOR EACH ROW EXECUTE FUNCTION tw_immutable();
