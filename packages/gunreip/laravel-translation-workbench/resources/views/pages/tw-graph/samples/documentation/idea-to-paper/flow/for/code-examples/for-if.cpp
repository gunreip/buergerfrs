const auto items = loadItems();
const auto count = items.size();
for (std::size_t index = 0; index < count; index++) {
    const auto& item = items[index];
    if (item.enabled) {
        processItem(item);
    } else {
        recordSkippedItem(item);
    }
}
showSummary();
