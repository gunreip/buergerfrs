string status = "draft";

switch (status)
{
    case "draft":
        OpenEditor();
        break;
    case "published":
        DisplayArticle();
        break;
    default:
        ShowStatusHint();
        break;
}

ContinueProcess();
