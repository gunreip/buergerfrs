enum class Status { Draft, Review, Revision, Published, Unknown };
Status status = Status::Draft;
enum class Format { Text, Image, Other };
Format format = Format::Text;

enum class Channel { Web, Print, Manual };
Channel channel = Channel::Web;

switch (status) {
    case Status::Draft:
    case Status::Review:
    case Status::Revision:
        prepareEditing();
        switch (format) {
            case Format::Text:
                openTextEditor();
                break;
            case Format::Image:
                openImageEditor();
                break;
            default:
                openPlainEditor();
                break;
        }
        break; // Exit the outer switch after the inner switch.
    case Status::Published:
        prepareDelivery();
        switch (channel) {
            case Channel::Web:
                publishOnline();
                break;
            case Channel::Print:
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
