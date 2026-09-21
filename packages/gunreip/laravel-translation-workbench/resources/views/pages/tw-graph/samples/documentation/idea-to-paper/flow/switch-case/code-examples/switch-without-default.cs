string status = "draft";

switch (status) {
    case "draft":
        OpenEditor();
        break;
    case "published":
        DisplayArticle();
        break;
}

// Continue here even when no CASE matches.
ContinueProcess();
