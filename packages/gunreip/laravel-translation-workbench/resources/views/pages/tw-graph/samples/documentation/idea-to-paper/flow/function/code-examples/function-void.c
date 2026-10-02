#include <stdio.h>

void log_message(const char *message)
{
    puts(message);
}

void demonstrate_void(void)
{
    puts("before");
    const char *message = "Processed item";
    log_message(message); /* No result assignment. */
    puts("after");
}
