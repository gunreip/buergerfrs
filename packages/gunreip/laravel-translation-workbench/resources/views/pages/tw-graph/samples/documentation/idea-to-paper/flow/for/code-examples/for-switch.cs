var items = LoadItems(); // Returns an array.
var count = items.Length;
for (int index = 0; index < count; index++) {
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
}
ShowSummary();
