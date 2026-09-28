// C has no native foreach; traverse the array using its length.
size_t count = 0;
Item *items = load_items(&count);
for (size_t i = 0; i < count; ++i) {
    process_item(items[i]);
}
show_summary();
