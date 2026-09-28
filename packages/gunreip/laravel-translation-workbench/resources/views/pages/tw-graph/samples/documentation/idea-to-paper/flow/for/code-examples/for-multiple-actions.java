var items = loadItems(); // Returns a list.
int count = items.size();
for (int index = 0; index < count; index++) {
    processItem(items.get(index));
    recordResult(items.get(index));
}
showSummary();
