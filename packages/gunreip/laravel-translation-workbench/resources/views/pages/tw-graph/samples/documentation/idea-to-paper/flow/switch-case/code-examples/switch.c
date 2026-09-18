enum Status { DRAFT, PUBLISHED, UNKNOWN };
enum Status status = DRAFT;

switch (status) {
    case DRAFT:
        open_editor();
        break;
    case PUBLISHED:
        display_article();
        break;
    default:
        show_status_hint();
        break;
}

continue_process();
