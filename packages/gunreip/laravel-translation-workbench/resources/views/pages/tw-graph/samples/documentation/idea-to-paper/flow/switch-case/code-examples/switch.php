<?php
$status = 'draft';

switch ($status) {
    case 'draft':
        openEditor();
        break;
    case 'published':
        displayArticle();
        break;
    default:
        showStatusHint();
        break;
}

continueProcess();
