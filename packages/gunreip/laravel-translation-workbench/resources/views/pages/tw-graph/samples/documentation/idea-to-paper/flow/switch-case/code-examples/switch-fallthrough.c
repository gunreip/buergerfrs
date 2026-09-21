enum Status { DRAFT, PUBLISHED, UNKNOWN };
enum Status status = DRAFT;

switch (status) {
    case DRAFT:
        prepare_article();
        // No break: continue with the next action without testing its case.
    case PUBLISHED:
        display_article();
        break;
    default:
        show_status_hint();
        break;
}

// Continue after the selected action sequence.
continue_process();
