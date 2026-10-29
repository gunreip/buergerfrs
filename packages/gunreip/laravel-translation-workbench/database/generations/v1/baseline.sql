-- Isolated integration schema only. Not registered as a Laravel migration.
CREATE TABLE translation_workbench_operations (
    id uuid PRIMARY KEY, kind text NOT NULL, request_fingerprint text NOT NULL CHECK (request_fingerprint ~ '^[a-f0-9]{64}$'),
    result jsonb NOT NULL, completed_at timestamptz NOT NULL DEFAULT now()
);
CREATE TABLE translation_workbench_source_files (id bigint GENERATED ALWAYS AS IDENTITY PRIMARY KEY, path text NOT NULL UNIQUE);
CREATE TABLE translation_workbench_scan_runs (
    id uuid PRIMARY KEY, status text NOT NULL CHECK (status IN ('completed','failed')), scope_snapshot jsonb NOT NULL, created_at timestamptz NOT NULL DEFAULT now()
);
CREATE TABLE translation_workbench_findings (
    id bigint GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    source_file_id bigint NOT NULL REFERENCES translation_workbench_source_files,
    captured_scan_id uuid NOT NULL REFERENCES translation_workbench_scan_runs,
    predecessor_id bigint UNIQUE REFERENCES translation_workbench_findings,
    source_line integer NOT NULL CHECK (source_line > 0), source_column integer NOT NULL CHECK (source_column > 0),
    raw_expression text NOT NULL, raw_source_code_literal text, raw_literal text, suggested_literal text,
    suggested_key text, provenance jsonb NOT NULL,
    status text NOT NULL DEFAULT 'active' CHECK (status IN ('active','obsolete')),
    retired_at timestamptz, retired_reason text,
    CHECK ((status='active' AND retired_at IS NULL AND retired_reason IS NULL) OR
           (status='obsolete' AND retired_at IS NOT NULL AND length(trim(retired_reason))>0))
);
CREATE UNIQUE INDEX current_finding_position ON translation_workbench_findings(source_file_id,source_line,source_column) WHERE status='active';
CREATE TABLE translation_workbench_scan_findings (
    scan_run_id uuid NOT NULL REFERENCES translation_workbench_scan_runs,
    finding_id bigint NOT NULL REFERENCES translation_workbench_findings, PRIMARY KEY(scan_run_id,finding_id)
);
CREATE TABLE translation_workbench_keys (id bigint GENERATED ALWAYS AS IDENTITY PRIMARY KEY);
CREATE TABLE translation_workbench_key_names (
    id bigint GENERATED ALWAYS AS IDENTITY PRIMARY KEY, key_id bigint NOT NULL REFERENCES translation_workbench_keys,
    namespace text NOT NULL DEFAULT '', full_name text NOT NULL, valid_from timestamptz NOT NULL DEFAULT now(), valid_to timestamptz,
    CHECK (valid_to IS NULL OR valid_to>=valid_from)
);
CREATE UNIQUE INDEX current_key_name ON translation_workbench_key_names(namespace,full_name) WHERE valid_to IS NULL;
CREATE UNIQUE INDEX current_key_identity ON translation_workbench_key_names(key_id) WHERE valid_to IS NULL;
CREATE TABLE translation_workbench_key_findings (
    id bigint GENERATED ALWAYS AS IDENTITY PRIMARY KEY, finding_id bigint NOT NULL REFERENCES translation_workbench_findings,
    key_id bigint NOT NULL REFERENCES translation_workbench_keys, valid_from timestamptz NOT NULL DEFAULT now(), valid_to timestamptz,
    CHECK (valid_to IS NULL OR valid_to>=valid_from)
);
CREATE UNIQUE INDEX current_assignment ON translation_workbench_key_findings(finding_id) WHERE valid_to IS NULL;
CREATE TABLE translation_workbench_value_slots (
    id bigint GENERATED ALWAYS AS IDENTITY PRIMARY KEY, key_id bigint NOT NULL REFERENCES translation_workbench_keys,
    locale text NOT NULL CHECK (length(trim(locale))>0), current_revision_id bigint,
    kind text NOT NULL DEFAULT 'scalar' CHECK(kind IN ('scalar','option')),
    option_identity text,
    CHECK((kind='scalar' AND option_identity IS NULL) OR (kind='option' AND option_identity IS NOT NULL AND length(option_identity)>0))
);
CREATE UNIQUE INDEX scalar_slot_identity ON translation_workbench_value_slots(key_id,locale) WHERE kind='scalar';
CREATE UNIQUE INDEX option_slot_identity ON translation_workbench_value_slots(key_id,locale,option_identity) WHERE kind='option';
CREATE TABLE translation_workbench_value_revisions (
    id bigint GENERATED ALWAYS AS IDENTITY PRIMARY KEY, slot_id bigint NOT NULL REFERENCES translation_workbench_value_slots,
    revision_number integer NOT NULL CHECK(revision_number>0), value text NOT NULL,
    actor_id bigint NOT NULL CHECK(actor_id>0), reason text NOT NULL CHECK(length(trim(reason))>0),
    created_at timestamptz NOT NULL DEFAULT now(), UNIQUE(slot_id,revision_number), UNIQUE(id,slot_id)
);
ALTER TABLE translation_workbench_value_slots ADD CONSTRAINT revision_same_slot
    FOREIGN KEY(current_revision_id,id) REFERENCES translation_workbench_value_revisions(id,slot_id);
CREATE TABLE translation_workbench_events (
    id bigint GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    operation_id uuid NOT NULL REFERENCES translation_workbench_operations DEFERRABLE INITIALLY DEFERRED,
    event_index integer NOT NULL DEFAULT 0, event_type text NOT NULL,
    actor_id bigint NOT NULL CHECK(actor_id>0), reason text NOT NULL CHECK(length(trim(reason))>0),
    details jsonb NOT NULL, occurred_at timestamptz NOT NULL DEFAULT now(), UNIQUE(operation_id,event_index)
);
CREATE FUNCTION tw_immutable() RETURNS trigger LANGUAGE plpgsql AS $$
BEGIN RAISE EXCEPTION 'Immutable %', TG_TABLE_NAME; END $$;
CREATE TRIGGER immutable_revision BEFORE UPDATE OR DELETE ON translation_workbench_value_revisions FOR EACH ROW EXECUTE FUNCTION tw_immutable();
CREATE TRIGGER immutable_event BEFORE UPDATE OR DELETE ON translation_workbench_events FOR EACH ROW EXECUTE FUNCTION tw_immutable();
CREATE TRIGGER immutable_operation BEFORE UPDATE OR DELETE ON translation_workbench_operations FOR EACH ROW EXECUTE FUNCTION tw_immutable();
CREATE FUNCTION tw_capture_guard() RETURNS trigger LANGUAGE plpgsql AS $$
BEGIN
    IF TG_OP='DELETE' THEN RAISE EXCEPTION 'Immutable finding'; END IF;
    IF (to_jsonb(NEW)-ARRAY['status','retired_at','retired_reason']) IS DISTINCT FROM
       (to_jsonb(OLD)-ARRAY['status','retired_at','retired_reason']) THEN RAISE EXCEPTION 'Immutable capture'; END IF;
    IF OLD.status='obsolete' AND NEW IS DISTINCT FROM OLD THEN RAISE EXCEPTION 'Archived finding cannot change'; END IF;
    RETURN NEW;
END $$;
CREATE TRIGGER immutable_capture BEFORE UPDATE OR DELETE ON translation_workbench_findings FOR EACH ROW EXECUTE FUNCTION tw_capture_guard();

CREATE TABLE translation_workbench_reviews (
    id bigint GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    operation_id uuid NOT NULL UNIQUE REFERENCES translation_workbench_operations DEFERRABLE INITIALLY DEFERRED,
    revision_id bigint NOT NULL REFERENCES translation_workbench_value_revisions,
    key_name_id bigint NOT NULL REFERENCES translation_workbench_key_names,
    decision text NOT NULL CHECK(decision IN ('approved','revoked')),
    context_fingerprint text NOT NULL CHECK(context_fingerprint ~ '^[a-f0-9]{64}$'),
    context_snapshot jsonb NOT NULL,
    actor_id bigint NOT NULL CHECK(actor_id>0), reason text NOT NULL CHECK(length(trim(reason))>0),
    occurred_at timestamptz NOT NULL DEFAULT now()
);
CREATE INDEX reviews_revision_order ON translation_workbench_reviews(revision_id,id DESC);
CREATE TRIGGER immutable_review BEFORE UPDATE OR DELETE ON translation_workbench_reviews FOR EACH ROW EXECUTE FUNCTION tw_immutable();

CREATE TABLE translation_workbench_publication_runs (
    id uuid PRIMARY KEY, plan text NOT NULL, plan_hash text NOT NULL,
    state text NOT NULL CHECK(state IN ('prepared','writing','interrupted','complete','closed')), error text
);
CREATE TABLE translation_workbench_publication_files (
    run_id uuid NOT NULL REFERENCES translation_workbench_publication_runs,
    path text NOT NULL, before_hash text, after_hash text NOT NULL,
    PRIMARY KEY(run_id,path)
);
CREATE TABLE translation_workbench_file_reservations (
    path text PRIMARY KEY, run_id uuid NOT NULL,
    FOREIGN KEY(run_id,path) REFERENCES translation_workbench_publication_files(run_id,path)
);
CREATE TABLE translation_workbench_publication_outcomes (
    run_id uuid PRIMARY KEY REFERENCES translation_workbench_publication_runs,
    kind text NOT NULL CHECK(kind IN ('completed','closed_without_writing')),
    snapshot jsonb NOT NULL, actor bigint NOT NULL CHECK(actor>0),
    reason text NOT NULL CHECK(length(trim(reason))>0), occurred_at timestamptz NOT NULL DEFAULT now()
);
CREATE TRIGGER immutable_outcome BEFORE UPDATE OR DELETE ON translation_workbench_publication_outcomes FOR EACH ROW EXECUTE FUNCTION tw_immutable();
CREATE FUNCTION tw_plan_guard() RETURNS trigger LANGUAGE plpgsql AS $$
BEGIN
    IF NEW.id IS DISTINCT FROM OLD.id OR NEW.plan IS DISTINCT FROM OLD.plan OR NEW.plan_hash IS DISTINCT FROM OLD.plan_hash THEN
        RAISE EXCEPTION 'Immutable publication plan';
    END IF;
    RETURN NEW;
END $$;
CREATE TRIGGER immutable_plan BEFORE UPDATE ON translation_workbench_publication_runs FOR EACH ROW EXECUTE FUNCTION tw_plan_guard();
CREATE TRIGGER immutable_publication_file BEFORE UPDATE OR DELETE ON translation_workbench_publication_files FOR EACH ROW EXECUTE FUNCTION tw_immutable();

CREATE FUNCTION tw_slot_identity_guard() RETURNS trigger LANGUAGE plpgsql AS $$
BEGIN
    IF ROW(NEW.id,NEW.key_id,NEW.locale,NEW.kind,NEW.option_identity) IS DISTINCT FROM ROW(OLD.id,OLD.key_id,OLD.locale,OLD.kind,OLD.option_identity) THEN
        RAISE EXCEPTION 'Immutable slot identity';
    END IF;
    RETURN NEW;
END $$;
CREATE TRIGGER immutable_slot_identity BEFORE UPDATE ON translation_workbench_value_slots FOR EACH ROW EXECUTE FUNCTION tw_slot_identity_guard();
