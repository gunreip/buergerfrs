#include <stdio.h>

int add_one(int value)
{
    int result = value + 1;
    return result; /* 21: resumes double_adjusted(). */
}

int double_adjusted(int value)
{
    int adjusted = add_one(value);
    int result = adjusted * 2;
    return result; /* 42: resumes the original caller. */
}

int demonstrate_nested(void)
{
    int value = 20;
    int result = double_adjusted(value);
    printf("%d\n", result);
    return result;
}
