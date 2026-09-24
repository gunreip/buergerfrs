/* ItemList and helper functions are application-defined. */
ItemList items = load_items();
size_t index = 0;
while (index < items.count) {
    process_item(items.data[index]);
    index = index + 1;
}
show_summary();
