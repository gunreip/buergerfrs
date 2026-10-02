#include <stdio.h>

/* C has no FINALLY or exceptions: cleanup and status propagation are explicit. */
typedef enum { STATUS_OK, STATUS_INVALID_INPUT } Status;

static Status validate_input(void)
{
    puts("inner");
    return STATUS_INVALID_INPUT;
}

static Status validate_and_cleanup(void)
{
    Status pending_status = validate_input();
    if (pending_status == STATUS_INVALID_INPUT) {
        puts("inner-log");
    }
    puts("cleanup"); /* Runs before the pending error reaches the caller. */
    return pending_status;
}

static Status perform_outer_try(void)
{
    puts("outer-try");
    Status status = validate_and_cleanup();
    if (status != STATUS_OK) {
        return status;
    }
    puts("unreachable-after-call");
    return STATUS_OK;
}

void demonstrate_finally(void)
{
    Status status = perform_outer_try();
    if (status == STATUS_INVALID_INPUT) {
        puts("outer-caught");
    }
    puts("after");
}
