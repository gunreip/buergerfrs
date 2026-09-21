String status = "draft";
String format = "text";

switch (status) {
    case "draft":
    case "review":
    case "revision":
        prepareEditing();
        switch (format) {
            case "text":
                openTextEditor();
                break;
            case "image":
                openImageEditor();
                break;
            default:
                openPlainEditor();
                break;
        }
        break; // Exit the outer switch after the inner switch.
    case "published":
        displayArticle();
        break;
    default:
        showStatusHint();
        break;
}

// Continue after the selected action sequence.
continueProcess();
