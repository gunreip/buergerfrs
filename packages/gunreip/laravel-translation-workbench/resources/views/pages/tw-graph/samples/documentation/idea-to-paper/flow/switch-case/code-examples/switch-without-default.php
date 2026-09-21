<?php
$status = 'draft';

switch ($status) {
    case 'draft':
        openEditor();
        break;
    case 'published':
        displayArticle();
        break;
}

// Continue here even when no CASE matches.
continueProcess();
