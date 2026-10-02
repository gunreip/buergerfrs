#include <stdbool.h>
#include <stddef.h>
#include <stdio.h>

/* C has no exceptions. Handle the failure separately inside each iteration. */
void process_items(const bool *items, size_t count)
{
    for (size_t index = 0; index < count; ++index) {
        if (!items[index]) {
            printf("caught:%zu\n", index);
        } else {
            printf("processed:%zu\n", index);
        }
    }
    puts("completed");
    puts("after");
}
/* {true, false, true} => processed:0, caught:1, processed:2, completed, after */
