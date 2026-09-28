// C equivalent: a failure is handled separately for each element.
size_t count = 0;
Item *items = load_items(&count);
for (size_t i = 0; i < count; ++i) {
    OperationStatus status = process_item(items[i]);
    if (status == OP_OK) {
        record_item_success(items[i]);
    } else {
        handle_item_failure(items[i], status);
    }
}
show_summary();
