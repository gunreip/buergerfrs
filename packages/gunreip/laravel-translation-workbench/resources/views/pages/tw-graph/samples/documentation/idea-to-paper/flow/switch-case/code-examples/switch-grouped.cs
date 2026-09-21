string status = "draft";

switch (status) {
    case "draft":
    case "review":
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
