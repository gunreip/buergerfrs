#include <stdio.h>

int adjust(int value, int factor)
{
    value = value + 1; /* Local parameter; no pointer or reference. */
    int result = value * factor;
    return result;
}

void demonstrate_arguments(void)
{
    int value = 20;
    int factor = 2;
    int result = adjust(value, factor);
    printf("value=%d; factor=%d; result=%d\n", value, factor, result);
    /* value=20; factor=2; result=42 */
}
