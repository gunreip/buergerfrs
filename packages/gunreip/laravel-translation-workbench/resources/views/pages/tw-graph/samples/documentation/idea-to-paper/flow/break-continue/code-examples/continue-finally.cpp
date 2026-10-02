auto items = loadItems();
std::size_t index = 0;
while (index < items.size()) {
    const auto& item = items[index];
    index++;
    struct Cleanup {
        const Item& item;
        ~Cleanup() { cleanupItem(item); }
    } cleanup{item};
    if (shouldSkip(item)) {
        continue;
    }
    processItem(item);
}
showSummary();
