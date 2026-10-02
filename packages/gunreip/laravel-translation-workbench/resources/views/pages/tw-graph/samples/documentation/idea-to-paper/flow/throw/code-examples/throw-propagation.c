#include <stdio.h>

/* C has no exception propagation: callers must explicitly forward/check errors. */
typedef enum { STATUS_OK, STATUS_INVALID_INPUT } Status;

static Status validate_input(void)
{
    puts("inner");
    return STATUS_INVALID_INPUT;
    /* puts("unreachable-inner"); */
}

static Status perform_outer_try(void)
{
    puts("outer-try");
    Status status = validate_input();
    if (status != STATUS_OK) {
        return status; /* Explicit propagation skips the following action. */
    }
    puts("unreachable-after-call");
    return STATUS_OK;
}

void demonstrate_propagation(void)
{
    Status status = perform_outer_try();
    if (status == STATUS_INVALID_INPUT) {
        puts("outer-caught");
    }
    puts("after");
}
