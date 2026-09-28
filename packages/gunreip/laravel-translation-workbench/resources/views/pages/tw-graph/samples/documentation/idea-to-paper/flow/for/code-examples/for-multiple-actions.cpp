const auto items = loadItems();
const auto count = items.size();
for (std::size_t index = 0; index < count; index++) {
    processItem(items[index]);
    recordResult(items[index]);
}
showSummary();
