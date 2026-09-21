enum class Status { Draft, Published, Unknown };
Status status = Status::Draft;

switch (status) {
    case Status::Draft:
        prepareArticle();
        [[fallthrough]]; // C++17: intentional fall-through.
    case Status::Published:
        displayArticle();
        break;
    default:
        showStatusHint();
        break;
}

// Continue after the selected action sequence.
continueProcess();
