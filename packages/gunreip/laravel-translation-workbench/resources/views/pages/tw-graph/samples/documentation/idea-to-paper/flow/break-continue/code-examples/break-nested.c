/* Group contains Item *items and size_t count. */
size_t groupCount = 0;
Group *groups = load_groups(&groupCount);
size_t groupIndex = 0;
while (groupIndex < groupCount) {
    Group group = groups[groupIndex];
    size_t itemIndex = 0;
    while (itemIndex < group.count) {
        Item item = group.items[itemIndex];
        itemIndex++;
        if (!may_process(item)) {
            break; /* Only the inner WHILE. */
        }
        process_item(item);
    }
    groupIndex++;
}
show_summary();
