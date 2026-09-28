const groups = loadGroups();
for (let groupIndex = 0; groupIndex < groups.length; groupIndex++) {
    const items = groups[groupIndex];
    prepareGroup(items);
    for (let itemIndex = 0; itemIndex < items.length; itemIndex++) {
        processItem(items[itemIndex]);
    }
    finalizeGroup(items);
}
showSummary();
