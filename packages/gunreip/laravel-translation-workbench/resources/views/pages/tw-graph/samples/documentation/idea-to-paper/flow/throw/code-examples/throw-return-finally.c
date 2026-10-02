#include <stdio.h>

/* C has no exceptions/FINALLY. Explicit status handling, not the graph's flow. */
typedef enum { STATUS_OK, STATUS_INVALID_INPUT } Status;

int fail_with_explicit_fallback(void)
{
    puts("original");
    Status status = STATUS_INVALID_INPUT;
    if (status == STATUS_INVALID_INPUT) {
        puts("handled");
    }
    puts("cleanup");
    return 7;
}
