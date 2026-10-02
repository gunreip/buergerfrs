#include <stdio.h>

int add_one(int value)
{
    int result = value + 1;
    return result;
}

void demonstrate_multiple_calls(void)
{
    int first = add_one(10);  /* Call site 1 resumes here with 11. */
    int second = add_one(40); /* Call site 2 resumes here with 41. */
    printf("first=%d; second=%d\n", first, second);
}
