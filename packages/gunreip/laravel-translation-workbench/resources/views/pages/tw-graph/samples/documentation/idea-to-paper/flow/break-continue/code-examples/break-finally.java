var items = loadItems(); // List<Item>
int index = 0;
while (index < items.size()) {
    var item = items.get(index);
    try {
        if (mayProcess(item)) {
            processItem(item);
            index++;
        } else {
            break;
        }
    } finally {
        cleanupItem(item);
    }
}
showSummary();
