const items = loadItems();
let index = 0;
while (index < items.length) {
    const item = items[index];
    switch (item.status) {
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
