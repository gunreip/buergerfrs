/* GroupList, ItemList and helpers are application-defined. */
GroupList groups = load_groups();
for (size_t groupIndex = 0; groupIndex < groups.count; groupIndex++) {
    ItemList items = groups.data[groupIndex];
    prepare_group(items);
    for (size_t itemIndex = 0; itemIndex < items.count; itemIndex++) {
        process_item(items.data[itemIndex]);
    }
    finalize_group(items);
}
show_summary();
