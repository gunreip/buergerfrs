-- Explicit v2 installation only; not registered as a production migration.
CREATE TABLE workbench_review_cases (
    id TEXT PRIMARY KEY,
    subject TEXT NOT NULL UNIQUE,
    current_version TEXT NOT NULL
);
CREATE TABLE workbench_review_versions (
    case_id TEXT NOT NULL REFERENCES workbench_review_cases(id),
    version TEXT NOT NULL,
    evidence TEXT NOT NULL,
    reason TEXT NOT NULL,
    required_action TEXT NOT NULL,
    needs_review INTEGER NOT NULL CHECK (needs_review IN (0,1)),
    PRIMARY KEY (case_id, version)
);
CREATE TABLE workbench_review_observations (
    case_id TEXT NOT NULL,
    version TEXT NOT NULL,
    run_id TEXT NOT NULL,
    PRIMARY KEY (case_id, run_id),
    FOREIGN KEY (case_id, version) REFERENCES workbench_review_versions(case_id, version)
);
CREATE TABLE workbench_review_decisions (
    case_id TEXT NOT NULL,
    version TEXT NOT NULL,
    revision INTEGER NOT NULL CHECK (revision > 0),
    decision TEXT NOT NULL,
    actor TEXT NOT NULL,
    explanation TEXT NOT NULL,
    decided_at TEXT NOT NULL,
    PRIMARY KEY (case_id, version, revision),
    FOREIGN KEY (case_id, version) REFERENCES workbench_review_versions(case_id, version)
);
