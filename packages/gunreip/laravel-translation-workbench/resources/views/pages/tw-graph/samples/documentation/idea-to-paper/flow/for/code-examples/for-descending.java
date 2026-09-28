var items = loadItems(); // Returns a list.
int count = items.size();
for (int index = count - 1; index >= 0; index -= 2) {
    processItem(items.get(index));
}
showSummary();
