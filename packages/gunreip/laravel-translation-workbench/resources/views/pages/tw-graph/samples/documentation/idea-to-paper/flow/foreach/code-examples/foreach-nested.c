// C equivalent: Group has items and count fields.
size_t group_count = 0;
Group *groups = load_groups(&group_count);
for (size_t g = 0; g < group_count; ++g) {
    Group *group = &groups[g];
    for (size_t i = 0; i < group->count; ++i) {
        process_item(group->items[i]);
        record_item_result(group->items[i]);
    }
    finalize_group(group);
}
show_summary();
