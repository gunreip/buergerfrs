/* ItemList and helpers are application-defined. */
ItemList items = load_items();
size_t index = 0;
while (index < items.count) {
    Item item = items.data[index];
    if (item.enabled) {
        process_item(item);
    } else {
        record_skipped_item(item);
    }
    index = index + 1;
}
show_summary();
