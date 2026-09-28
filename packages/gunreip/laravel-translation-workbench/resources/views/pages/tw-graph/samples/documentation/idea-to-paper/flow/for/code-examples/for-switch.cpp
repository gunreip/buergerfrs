const auto items = loadItems();
const auto count = items.size();
for (std::size_t index = 0; index < count; index++) {
    const auto& item = items[index];
    switch (item.status) {
        case Status::Draft:
            editDraft(item);
            break;
        case Status::Published:
            displayItem(item);
            break;
        default:
            recordUnknownStatus(item);
            break;
    }
}
showSummary();
