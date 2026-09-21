const status = 'draft';
const format = 'text';

const channel = 'web';

switch (status) {
    case 'draft':
    case 'review':
    case 'revision':
        prepareEditing();
        switch (format) {
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
        break; // Exit the outer switch after the inner switch.
    case 'published':
        prepareDelivery();
        switch (channel) {
            case 'web':
                publishOnline();
                break;
            case 'print':
                queuePrintJob();
                break;
            default:
                queueManualDelivery();
                break;
        }
        break;
    default:
        showStatusHint();
        break;
}

// Continue after the selected action sequence.
continueProcess();
