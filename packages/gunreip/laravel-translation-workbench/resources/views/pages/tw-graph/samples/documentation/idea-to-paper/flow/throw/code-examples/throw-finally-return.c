#include <stdio.h>

/* No FINALLY/exceptions: explicitly check cleanup before publishing the result. */
typedef enum { STATUS_OK, STATUS_CLEANUP_FAILED } Status;

static Status cleanup(void)
{
    puts("cleanup");
    return STATUS_CLEANUP_FAILED;
}

static Status compute_and_cleanup(int *out_result)
{
    puts("pending-return");
    int pending_result = 42;
    Status status = cleanup();
    if (status != STATUS_OK) {
        return status;
    }
    *out_result = pending_result; /* Not reached on cleanup failure. */
    return STATUS_OK;
}

void demonstrate_finally_throw(void)
{
    int result = -1; /* Explicit unset sentinel for this example. */
    puts("outer-try");
    Status status = compute_and_cleanup(&result);
    if (status == STATUS_CLEANUP_FAILED) {
        puts("outer-caught");
    }
    puts("after");
    /* result is still -1, not 42. */
}
