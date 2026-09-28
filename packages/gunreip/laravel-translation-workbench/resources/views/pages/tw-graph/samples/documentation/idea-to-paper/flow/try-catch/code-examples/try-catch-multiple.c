// C equivalent: OperationStatus is an application-defined status enum.
OperationStatus status = perform_operation();
switch (status) {
    case OP_OK:
        record_success();
        break;
    case OP_VALIDATION_FAILURE:
        handle_validation(status);
        break;
    case OP_STORAGE_FAILURE:
        handle_storage(status);
        break;
    default:
        handle_other(status);
        break;
}
continue_process();
