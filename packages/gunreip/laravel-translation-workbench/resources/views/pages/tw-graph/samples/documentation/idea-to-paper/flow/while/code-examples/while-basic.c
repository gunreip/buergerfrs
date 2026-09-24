/* Queue helpers are application-defined. */
Queue pending = load_pending_items();
while (!queue_empty(&pending)) {
    Item item = queue_pop(&pending);
    process_item(item);
}
show_summary();
