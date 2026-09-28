auto items = loadItems(); // std::vector<Item>
std::size_t index = 0;
while (index < items.size()) {
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
    index = index + 1;
}
showSummary();
