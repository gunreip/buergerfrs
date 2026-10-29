CREATE TABLE baseline_value_draft_imports (
    operation_id uuid PRIMARY KEY REFERENCES translation_workbench_operations DEFERRABLE INITIALLY DEFERRED,
    revision_id bigint NOT NULL UNIQUE REFERENCES translation_workbench_value_revisions,
    application_id uuid NOT NULL REFERENCES baseline_key_correction_applications(operation_id),
    evidence jsonb NOT NULL
);
CREATE TRIGGER immutable_value_draft_import BEFORE UPDATE OR DELETE ON baseline_value_draft_imports
FOR EACH ROW EXECUTE FUNCTION tw_immutable();
