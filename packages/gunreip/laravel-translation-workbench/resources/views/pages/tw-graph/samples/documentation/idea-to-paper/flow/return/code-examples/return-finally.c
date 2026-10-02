/* C has no FINALLY. This normal-return equivalent stores the result first.
   The caller supplies a valid cleanup_count pointer. */
int double_positive(int value, int *cleanup_count)
{
    int pending_result;
    if (value > 0) {
        pending_result = value * 2;
    } else {
        pending_result = 0;
    }
    ++*cleanup_count;
    value = 0;
    return pending_result;
}
