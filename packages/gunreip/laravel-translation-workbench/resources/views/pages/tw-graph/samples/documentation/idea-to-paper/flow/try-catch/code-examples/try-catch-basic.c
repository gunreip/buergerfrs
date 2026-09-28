// C equivalent: OperationStatus is an application-defined status enum.
OperationStatus status = perform_operation();
if (status == OP_OK) {
    record_success();
} else {
    handle_failure(status);
}
continue_process();
