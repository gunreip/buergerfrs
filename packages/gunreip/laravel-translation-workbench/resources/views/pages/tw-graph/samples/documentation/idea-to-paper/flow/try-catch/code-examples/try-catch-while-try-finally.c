// C equivalent: handle status and clean up once per iteration.
Queue *queue = load_queue();
prepare_queue(queue);
while (has_next(queue)) {
    Item item = next_item(queue);
    OperationStatus status = process_item(item);
    if (status == OP_OK) {
        record_item_success(item);
    } else {
        handle_item_failure(item, status);
    }
    cleanup_item(item);
}
show_summary();
