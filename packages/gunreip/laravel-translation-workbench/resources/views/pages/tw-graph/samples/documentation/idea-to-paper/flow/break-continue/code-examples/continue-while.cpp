auto items = loadItems();
std::size_t index = 0;
while (index < items.size()) {
    const auto& item = items[index];
    index++;
    if (shouldSkip(item)) {
        continue;
    }
    processItem(item);
}
showSummary();
