enum class Status { Draft, Published, Unknown };
Status status = Status::Draft;

switch (status) {
    case Status::Draft:
        openEditor();
        break;
    case Status::Published:
        displayArticle();
        break;
}

// Continue here even when no CASE matches.
continueProcess();
