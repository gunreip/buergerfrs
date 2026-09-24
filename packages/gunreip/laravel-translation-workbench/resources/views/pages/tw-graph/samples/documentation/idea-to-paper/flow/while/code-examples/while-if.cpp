auto items = loadItems(); // std::vector<Item>
std::size_t index = 0;
while (index < items.size()) {
    const auto& item = items[index];
    if (item.enabled) {
        processItem(item);
    } else {
        recordSkippedItem(item);
    }
    index = index + 1;
}
showSummary();
