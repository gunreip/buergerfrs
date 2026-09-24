const groups = loadGroups();
let groupIndex = 0;
while (groupIndex < groups.length) {
    const items = groups[groupIndex];
    let itemIndex = 0;
    while (itemIndex < items.length) {
        processItem(items[itemIndex]);
        itemIndex = itemIndex + 1;
    }
    groupIndex = groupIndex + 1;
}
showSummary();
