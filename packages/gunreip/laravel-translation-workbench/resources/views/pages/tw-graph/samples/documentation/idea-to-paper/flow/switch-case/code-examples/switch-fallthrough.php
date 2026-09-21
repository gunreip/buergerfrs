<?php
$status = 'draft';

switch ($status) {
    case 'draft':
        prepareArticle();
        // No break: continue with the next action without testing its case.
    case 'published':
        displayArticle();
        break;
    default:
        showStatusHint();
        break;
}

// Continue after the selected action sequence.
continueProcess();
