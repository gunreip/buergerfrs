var items = loadItems(); // List<Item>
int index = 0;
while (index < items.size()) {
    var item = items.get(index);
    if (mayProcess(item)) {
        processItem(item);
        index++;
    } else {
        break;
    }
}
continueProcess();
