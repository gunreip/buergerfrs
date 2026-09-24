const groups = loadGroups();
let groupIndex = 0;
while (groupIndex < groups.length) {
    const items = groups[groupIndex];
    prepareGroup(items);
    let itemIndex = 0;
    while (itemIndex < items.length) {
        processItem(items[itemIndex]);
        itemIndex = itemIndex + 1;
    }
    finalizeGroup(items);
    groupIndex = groupIndex + 1;
}
showSummary();
