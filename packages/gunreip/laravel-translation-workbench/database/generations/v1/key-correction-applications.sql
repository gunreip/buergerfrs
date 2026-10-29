CREATE TABLE baseline_key_correction_applications (
    operation_id uuid PRIMARY KEY REFERENCES translation_workbench_operations DEFERRABLE INITIALLY DEFERRED,
    proposal_id uuid NOT NULL UNIQUE REFERENCES baseline_key_correction_proposals(operation_id),
    key_id bigint NOT NULL REFERENCES translation_workbench_keys,
    links jsonb NOT NULL,
    created_at timestamptz NOT NULL DEFAULT now()
);
CREATE TRIGGER immutable_key_correction_application BEFORE UPDATE OR DELETE ON baseline_key_correction_applications
FOR EACH ROW EXECUTE FUNCTION tw_immutable();
