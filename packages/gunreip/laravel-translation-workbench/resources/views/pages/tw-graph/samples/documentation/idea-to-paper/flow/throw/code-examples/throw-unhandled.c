#include <stdio.h>

/* C has no exceptions/FINALLY: propagate an explicit failure status. */
typedef enum { STATUS_OK, STATUS_INVALID_INPUT } Status;

static Status fail_with_cleanup(void)
{
    puts("original");
    Status status = STATUS_INVALID_INPUT;
    puts("cleanup");
    return status;
}

Status demonstrate_unhandled(void)
{
    puts("call");
    Status status = fail_with_cleanup();
    if (status != STATUS_OK) {
        return status; /* The caller must handle this failure. */
    }
    puts("normal continuation"); /* Not reached for this input. */
    return STATUS_OK;
}
