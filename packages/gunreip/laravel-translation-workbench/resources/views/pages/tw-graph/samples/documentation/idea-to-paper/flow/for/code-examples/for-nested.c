/* GroupList, ItemList and helpers are application-defined. */
GroupList groups = load_groups();
for (size_t groupIndex = 0; groupIndex < groups.count; groupIndex++) {
    ItemList items = groups.data[groupIndex];
    for (size_t itemIndex = 0; itemIndex < items.count; itemIndex++) {
        process_item(items.data[itemIndex]);
    }
}
show_summary();
