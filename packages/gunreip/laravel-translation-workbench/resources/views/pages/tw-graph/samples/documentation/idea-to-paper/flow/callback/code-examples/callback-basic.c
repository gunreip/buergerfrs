#include <stdio.h>

int add_one(int value)
{
    return value + 1;
}

int apply(int value, int (*callback)(int))
{
    int result = callback(value);
    return result;
}

void demonstrate_callback(void)
{
    int result = apply(41, add_one); /* Pass a function pointer. */
    printf("%d\n", result); /* 42 */
}
