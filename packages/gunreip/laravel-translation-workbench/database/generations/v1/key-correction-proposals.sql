-- A recorded draft decision, not a key assignment, approval, or publication.
CREATE TABLE baseline_key_correction_proposals (
    operation_id uuid PRIMARY KEY,
    baseline_hash text NOT NULL REFERENCES published_value_baselines(hash),
    source_key text NOT NULL,
    target_key text NOT NULL,
    actor_id bigint NOT NULL CHECK (actor_id > 0),
    reason text NOT NULL CHECK (length(trim(reason)) > 0),
    context_fingerprint text NOT NULL,
    context jsonb NOT NULL,
    created_at timestamptz NOT NULL DEFAULT now()
);
CREATE TRIGGER immutable_key_correction_proposal BEFORE UPDATE OR DELETE ON baseline_key_correction_proposals
FOR EACH ROW EXECUTE FUNCTION tw_immutable();
