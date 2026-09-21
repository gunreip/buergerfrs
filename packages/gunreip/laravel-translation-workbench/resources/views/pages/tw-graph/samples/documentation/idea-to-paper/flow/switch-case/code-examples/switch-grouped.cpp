enum class Status { Draft, Review, Published, Unknown };
Status status = Status::Draft;

switch (status) {
    case Status::Draft:
    case Status::Review:
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
