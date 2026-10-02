var items = loadItems(); // List<Item>
int index = 0;
while (index < items.size()) {
    var item = items.get(index);
    index++;
    try {
        if (shouldSkip(item)) {
            continue;
        }
        processItem(item);
    } finally {
        cleanupItem(item);
    }
}
showSummary();
