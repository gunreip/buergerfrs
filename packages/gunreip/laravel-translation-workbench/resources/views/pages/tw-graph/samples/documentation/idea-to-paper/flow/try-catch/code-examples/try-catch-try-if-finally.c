// C equivalent: handle the selected action status, then clean up.
OperationStatus status;
if (use_primary) {
    status = perform_primary();
} else {
    status = perform_secondary();
}
if (status == OP_OK) {
    record_success();
} else {
    handle_failure(status);
}
cleanup();
continue_process();
