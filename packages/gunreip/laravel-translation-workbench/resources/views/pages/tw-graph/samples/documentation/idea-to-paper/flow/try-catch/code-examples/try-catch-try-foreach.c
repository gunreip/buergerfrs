// C equivalent: the first failure exits the whole traversal.
size_t count = 0;
Item *items = load_items(&count);
OperationStatus status = OP_OK;
for (size_t i = 0; i < count; ++i) {
    status = process_item(items[i]);
    if (status != OP_OK) {
        break;
    }
    record_item_success(items[i]);
}
if (status != OP_OK) {
    handle_failure(status);
}
continue_process();
