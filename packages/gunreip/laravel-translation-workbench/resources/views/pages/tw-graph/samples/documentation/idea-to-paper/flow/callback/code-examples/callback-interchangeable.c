#include <stdio.h>

int add_one(int value)
{
    return value + 1;
}

int double_value(int value)
{
    return value * 2;
}

int apply(int value, int (*callback)(int))
{
    int result = callback(value);
    return result;
}

void demonstrate_callbacks(void)
{
    int first = apply(20, add_one);
    int second = apply(20, double_value);
    printf("first=%d; second=%d\n", first, second); /* 21 and 40 */
}
