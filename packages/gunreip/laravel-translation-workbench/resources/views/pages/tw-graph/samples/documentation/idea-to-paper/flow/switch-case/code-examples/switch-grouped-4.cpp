enum class Status { Draft, Review, Revision, Returned, Published, Unknown };
Status status = Status::Draft;

switch (status) {
    case Status::Draft:
    case Status::Review:
    case Status::Revision:
    case Status::Returned:
        openEditor();
        break;
    case Status::Published:
        displayArticle();
        break;
    default:
        showStatusHint();
        break;
}

// Continue after the selected action sequence.
continueProcess();
