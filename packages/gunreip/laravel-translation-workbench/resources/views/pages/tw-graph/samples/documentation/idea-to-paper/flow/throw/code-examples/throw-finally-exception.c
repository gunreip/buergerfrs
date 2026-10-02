#include <stdio.h>

/* C has no exceptions/FINALLY. Keep the active error and its cause explicitly. */
typedef enum { STATUS_OK, STATUS_INVALID_INPUT, STATUS_CLEANUP_FAILED } Status;
typedef struct { Status active; Status cause; } Failure;

static Failure fail_and_cleanup(void)
{
    puts("original");
    Status original = STATUS_INVALID_INPUT;
    puts("cleanup");
    return (Failure){ STATUS_CLEANUP_FAILED, original };
}

Failure demonstrate_replacement(void)
{
    puts("outer-try");
    Failure error = fail_and_cleanup();
    if (error.active == STATUS_CLEANUP_FAILED) {
        puts("outer-caught");
        /* error.cause retains STATUS_INVALID_INPUT. */
    }
    puts("after");
    return error;
}
