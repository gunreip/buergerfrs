auto items = loadItems();
std::size_t index = 0;
while (index < items.size()) {
    const auto& item = items[index];
    if (mayProcess(item)) {
        processItem(item);
        index++;
    } else {
        break;
    }
}
continueProcess();
