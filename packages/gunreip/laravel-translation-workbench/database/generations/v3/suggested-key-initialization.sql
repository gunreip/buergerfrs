CREATE OR REPLACE FUNCTION tw_capture_guard() RETURNS trigger LANGUAGE plpgsql AS $$
BEGIN
    IF TG_OP='DELETE' THEN RAISE EXCEPTION 'Immutable finding'; END IF;
    IF OLD.status='obsolete' AND NEW IS DISTINCT FROM OLD THEN RAISE EXCEPTION 'Archived finding cannot change'; END IF;
    IF OLD.suggested_key IS NULL AND NEW.suggested_key IS NOT NULL
       AND length(btrim(NEW.suggested_key))>0
       AND (to_jsonb(NEW)-'suggested_key')=(to_jsonb(OLD)-'suggested_key')
       AND EXISTS (SELECT 1 FROM translation_workbench_events e
           JOIN translation_workbench_scan_runs r ON r.id=e.operation_id
           WHERE e.event_type='suggested_key_initialized'
             AND e.details->>'finding'=OLD.id::text
             AND e.details->>'suggested_key'=NEW.suggested_key
             AND e.occurred_at=transaction_timestamp()
             AND r.scope_snapshot->>'mode'='generation_rescan') THEN
        RETURN NEW;
    END IF;
    IF (to_jsonb(NEW)-ARRAY['status','retired_at','retired_reason']) IS DISTINCT FROM
       (to_jsonb(OLD)-ARRAY['status','retired_at','retired_reason']) THEN RAISE EXCEPTION 'Immutable capture'; END IF;
    RETURN NEW;
END $$;
