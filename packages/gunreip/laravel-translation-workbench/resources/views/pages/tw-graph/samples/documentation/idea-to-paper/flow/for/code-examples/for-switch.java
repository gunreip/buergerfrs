var items = loadItems(); // Returns a list.
int count = items.size();
for (int index = 0; index < count; index++) {
    Item item = items.get(index);
    switch (item.getStatus()) {
        case "draft":
            editDraft(item);
            break;
        case "published":
            displayItem(item);
            break;
        default:
            recordUnknownStatus(item);
            break;
    }
}
showSummary();
