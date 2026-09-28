const groups = loadGroups();
for (const group of groups) {
    for (const item of group) {
        processItem(item);
        recordItemResult(item);
    }
    finalizeGroup(group);
}
showSummary();
