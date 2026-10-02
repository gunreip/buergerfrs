#include <stddef.h>

int add_one(int value)
{
    return value + 1;
}

/* The caller provides output space for count integers. */
void map_each(const int *items, size_t count, int (*callback)(int), int *results)
{
    for (size_t i = 0; i < count; ++i) {
        int result = callback(items[i]);
        results[i] = result;
    }
}

void demonstrate_callback_loop(void)
{
    int items[] = {10, 20, 30};
    int results[3];
    map_each(items, 3, add_one, results); /* {11, 21, 31} */
}
