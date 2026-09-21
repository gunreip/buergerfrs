enum Status { DRAFT, REVIEW, REVISION, PUBLISHED, UNKNOWN };
enum Status status = DRAFT;
enum Format { TEXT, IMAGE, OTHER };
enum Format format = TEXT;

enum Channel { WEB, PRINT, MANUAL };
enum Channel channel = WEB;

switch (status) {
    case DRAFT:
    case REVIEW:
    case REVISION:
        prepare_editing();
        switch (format) {
            case TEXT:
                open_text_editor();
                break;
            case IMAGE:
                open_image_editor();
                break;
            default:
                open_plain_editor();
                break;
        }
        break; // Exit the outer switch after the inner switch.
    case PUBLISHED:
        prepare_delivery();
        switch (channel) {
            case WEB:
                publish_online();
                break;
            case PRINT:
                queue_print_job();
                break;
            default:
                queue_manual_delivery();
                break;
        }
        break;
    default:
        show_status_hint();
        break;
}

// Continue after the selected action sequence.
continue_process();
