enum class Status { Draft, Published, Unknown };
Status status = Status::Draft;

switch (status) {
    case Status::Draft:
        openEditor();
        break;
    case Status::Published:
        displayArticle();
        break;
    default:
        showStatusHint();
        break;
}

continueProcess();
