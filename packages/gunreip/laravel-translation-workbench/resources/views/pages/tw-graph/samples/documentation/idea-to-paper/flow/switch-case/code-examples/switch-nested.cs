string status = "editable";
string format = "text";

switch (status) {
    case "editable":
        PrepareEditing();
        switch (format) {
            case "text":
                OpenTextEditor();
                break;
            case "image":
                OpenImageEditor();
                break;
            default:
                OpenPlainEditor();
                break;
        }
        break; // Exit the outer switch after the inner switch.
    case "published":
        DisplayArticle();
        break;
    default:
        ShowStatusHint();
        break;
}

// Continue after the selected action sequence.
ContinueProcess();
