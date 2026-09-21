enum Status { DRAFT, PUBLISHED, UNKNOWN };
enum Status status = DRAFT;

switch (status) {
    case DRAFT:
        open_editor();
        break;
    case PUBLISHED:
        display_article();
        break;
}

// Continue here even when no CASE matches.
continue_process();
