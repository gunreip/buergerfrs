#include <stdbool.h>
#include <stddef.h>
#include <stdio.h>

/* C has no exceptions. Explicit status and break model the early loop exit. */
void process_items(const bool *items, size_t count)
{
    bool failed = false;
    for (size_t index = 0; index < count; ++index) {
        if (!items[index]) {
            failed = true;
            break;
        }
        printf("processed:%zu\n", index);
    }
    if (failed) {
        puts("caught"); /* Equivalent outer error-handling step. */
    } else {
        puts("completed");
    }
    puts("after");
}
/* {true, false, true} prints processed:0, caught, after. */
