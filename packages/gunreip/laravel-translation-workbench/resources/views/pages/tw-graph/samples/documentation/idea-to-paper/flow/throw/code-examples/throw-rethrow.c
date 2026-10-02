#include <stdio.h>

/* C has no exceptions: explicitly log and forward the error status. */
typedef enum { STATUS_OK, STATUS_INVALID_INPUT } Status;

static Status validate_input(void)
{
    puts("inner");
    return STATUS_INVALID_INPUT;
}

static Status validate_and_log(void)
{
    Status status = validate_input();
    if (status == STATUS_INVALID_INPUT) {
        puts("inner-log");
        return status; /* Equivalent to forwarding, not a language-level RETHROW. */
    }
    return status;
}

static Status perform_outer_try(void)
{
    puts("outer-try");
    Status status = validate_and_log();
    if (status != STATUS_OK) {
        return status;
    }
    puts("unreachable-after-call");
    return STATUS_OK;
}

void demonstrate_rethrow(void)
{
    Status status = perform_outer_try();
    if (status == STATUS_INVALID_INPUT) {
        puts("outer-caught");
    }
    puts("after");
}
