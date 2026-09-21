string status = "draft";

switch (status) {
    case "draft":
        PrepareArticle();
        goto case "published"; // C# requires an explicit transfer.
    case "published":
        DisplayArticle();
        break;
    default:
        ShowStatusHint();
        break;
}

// Continue after the selected action sequence.
ContinueProcess();
