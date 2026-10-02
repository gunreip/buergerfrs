auto items = loadItems();
for (std::size_t index = 0; index < items.size(); index++) {
    const auto& item = items[index];
    if (shouldSkip(item)) {
        continue;
    }
    processItem(item);
}
showSummary();
