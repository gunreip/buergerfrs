string status = "draft";
string format = "text";

switch (status) {
    case "draft":
    case "review":
    case "revision":
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
        SaveEditingResult();
        break; // Exit the outer switch after the complete action sequence.
    case "published":
        DisplayArticle();
        break;
    default:
        ShowStatusHint();
        break;
}

// Continue after the selected action sequence.
ContinueProcess();
