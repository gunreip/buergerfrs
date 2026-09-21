<?php
$status = 'draft';
$format = 'text';

switch ($status) {
    case 'draft':
    case 'review':
    case 'revision':
        prepareEditing();
        switch ($format) {
            case 'text':
                openTextEditor();
                break;
            case 'image':
                openImageEditor();
                break;
            default:
                openPlainEditor();
                break;
        }
        saveEditingResult();
        break; // Exit the outer switch after the complete action sequence.
    case 'published':
        displayArticle();
        break;
    default:
        showStatusHint();
        break;
}

// Continue after the selected action sequence.
continueProcess();
