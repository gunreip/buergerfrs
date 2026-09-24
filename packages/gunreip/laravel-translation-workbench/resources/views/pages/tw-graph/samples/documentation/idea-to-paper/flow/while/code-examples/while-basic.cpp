auto pending = loadPendingItems(); // std::queue<Item>
while (!pending.empty()) {
    auto item = pending.front();
    pending.pop();
    processItem(item);
}
showSummary();
