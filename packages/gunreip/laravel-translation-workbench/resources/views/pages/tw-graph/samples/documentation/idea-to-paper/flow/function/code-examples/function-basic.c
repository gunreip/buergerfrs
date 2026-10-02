#include <stdio.h>

int add_one(int value)
{
    int result = value + 1;
    return result;
}

int demonstrate_call(void)
{
    int value = 41;
    int result = add_one(value);
    printf("%d\n", result); /* 42, after add_one has returned. */
    return result;
}
