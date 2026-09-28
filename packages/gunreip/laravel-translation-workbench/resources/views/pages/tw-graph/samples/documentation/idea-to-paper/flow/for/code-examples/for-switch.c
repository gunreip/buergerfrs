// loadItems supplies an array and writes its element count.
size_t count = 0;
const Item *items = loadItems(&count);
for (size_t index = 0; index < count; index++) {
    Item item = items[index];
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
}
showSummary();
