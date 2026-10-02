#include <stdbool.h>

/* The caller supplies a valid notification_count pointer. */
void notify_if_enabled(bool enabled, int *notification_count)
{
    if (!enabled) {
        return;
    }
    ++*notification_count;
    return; /* Optional at the end of this void function. */
}
