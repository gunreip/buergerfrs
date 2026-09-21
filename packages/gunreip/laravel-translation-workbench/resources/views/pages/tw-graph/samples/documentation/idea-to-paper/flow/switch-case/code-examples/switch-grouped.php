<?php
$status = 'draft';

switch ($status) {
    case 'draft':
    case 'review':
        openEditor();
        break;
    case 'published':
        displayArticle();
        break;
    default:
        showStatusHint();
        break;
}

// Continue after the selected action sequence.
continueProcess();
