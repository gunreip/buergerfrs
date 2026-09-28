/* ItemList and helpers are application-defined. */
ItemList items = load_items();
size_t index = 0;
while (index < items.count) {
    Item item = items.data[index];
    switch (item.status) {
        case STATUS_DRAFT:
            edit_draft(item);
            break;
        case STATUS_PUBLISHED:
            display_item(item);
            break;
        default:
            record_unknown_status(item);
            break;
    }
    index = index + 1;
}
show_summary();
