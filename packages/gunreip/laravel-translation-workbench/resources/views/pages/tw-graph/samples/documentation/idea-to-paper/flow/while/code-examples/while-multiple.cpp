auto items = loadItems(); // std::vector<Item>
std::size_t index = 0;
while (index < items.size()) {
    processItem(items[index]);
    index = index + 1;
}
showSummary();
