auto groups = loadGroups();
for (const auto& group : groups) {
    for (const auto& item : group) {
        processItem(item);
        recordItemResult(item);
    }
    finalizeGroup(group);
}
showSummary();
