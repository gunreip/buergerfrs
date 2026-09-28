// C equivalent: explicit status handling, no native exceptions.
if (enabled) {
    prepare_operation();
    OperationStatus status = perform_operation();
    if (status == OP_OK) {
        record_success();
    } else {
        handle_failure(status);
    }
} else {
    record_skipped();
}
continue_process();
