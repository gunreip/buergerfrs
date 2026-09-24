/* GroupList, ItemList and helpers are application-defined. */
GroupList groups = load_groups();
size_t groupIndex = 0;
while (groupIndex < groups.count) {
    ItemList items = groups.data[groupIndex];
    prepare_group(items);
    size_t itemIndex = 0;
    while (itemIndex < items.count) {
        process_item(items.data[itemIndex]);
        itemIndex = itemIndex + 1;
    }
    finalize_group(items);
    groupIndex = groupIndex + 1;
}
show_summary();
