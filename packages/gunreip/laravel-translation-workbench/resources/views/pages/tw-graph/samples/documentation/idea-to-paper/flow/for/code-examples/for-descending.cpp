const auto items = loadItems();
// The item count must fit in std::ptrdiff_t.
const auto count = static_cast<std::ptrdiff_t>(items.size());
for (std::ptrdiff_t index = count - 1; index >= 0; index -= 2) {
    processItem(items[index]);
}
showSummary();
