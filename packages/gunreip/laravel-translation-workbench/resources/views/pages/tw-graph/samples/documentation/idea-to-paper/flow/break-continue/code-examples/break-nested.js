const groups = loadGroups();
let groupIndex = 0;
while (groupIndex < groups.length) {
    const items = groups[groupIndex];
    let itemIndex = 0;
    while (itemIndex < items.length) {
        const item = items[itemIndex];
        itemIndex++;
        if (!mayProcess(item)) {
            break; // Only the inner WHILE.
        }
        processItem(item);
    }
    groupIndex++;
}
showSummary();
