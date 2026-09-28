var items = LoadItems(); // List<Item>
var index = 0;
while (index < items.Count) {
    var item = items[index];
    switch (item.Status) {
        case "draft":
            EditDraft(item);
            break;
        case "published":
            DisplayItem(item);
            break;
        default:
            RecordUnknownStatus(item);
            break;
    }
    index = index + 1;
}
ShowSummary();
