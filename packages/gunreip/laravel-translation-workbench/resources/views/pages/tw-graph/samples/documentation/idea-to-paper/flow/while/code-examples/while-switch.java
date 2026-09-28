List<Item> items = loadItems();
int index = 0;
while (index < items.size()) {
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
    index = index + 1;
}
showSummary();
