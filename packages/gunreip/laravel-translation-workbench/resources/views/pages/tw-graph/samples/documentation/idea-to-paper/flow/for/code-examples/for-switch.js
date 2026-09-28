const items = loadItems();
const count = items.length;
for (let index = 0; index < count; index++) {
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
}
showSummary();
