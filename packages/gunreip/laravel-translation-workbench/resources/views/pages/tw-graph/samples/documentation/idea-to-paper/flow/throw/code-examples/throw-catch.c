#include <stdio.h>

/* C has no THROW/CATCH. This equivalent flow explicitly returns a status. */
typedef enum { STATUS_OK, STATUS_INVALID_INPUT } Status;

static Status perform_operation(void)
{
    puts("try");
    return STATUS_INVALID_INPUT;
    /* puts("unreachable"); */
}

void demonstrate_throw(void)
{
    Status status = perform_operation();
    if (status == STATUS_INVALID_INPUT) {
        puts("caught");
    }
    puts("after");
}
