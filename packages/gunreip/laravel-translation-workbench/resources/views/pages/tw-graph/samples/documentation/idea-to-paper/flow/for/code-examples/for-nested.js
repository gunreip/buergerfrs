const groups = loadGroups();
for (let groupIndex = 0; groupIndex < groups.length; groupIndex++) {
    const items = groups[groupIndex];
    for (let itemIndex = 0; itemIndex < items.length; itemIndex++) {
        processItem(items[itemIndex]);
    }
}
showSummary();
