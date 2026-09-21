string status = "draft";

switch (status) {
    case "draft":
    case "review":
    case "revision":
        OpenEditor();
        break;
    case "published":
        DisplayArticle();
        break;
    default:
        ShowStatusHint();
        break;
}

// Continue after the selected action sequence.
ContinueProcess();
