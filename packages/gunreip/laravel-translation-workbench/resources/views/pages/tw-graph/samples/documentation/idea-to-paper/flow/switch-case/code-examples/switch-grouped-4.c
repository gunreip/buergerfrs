enum Status { DRAFT, REVIEW, REVISION, RETURNED, PUBLISHED, UNKNOWN };
enum Status status = DRAFT;

switch (status) {
    case DRAFT:
    case REVIEW:
    case REVISION:
    case RETURNED:
        open_editor();
        break;
    case PUBLISHED:
        display_article();
        break;
    default:
        show_status_hint();
        break;
}

// Continue after the selected action sequence.
continue_process();
