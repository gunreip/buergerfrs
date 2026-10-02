#include <iostream>

int addOne(int value)
{
    int result = value + 1;
    return result; // 21: resumes doubleAdjusted().
}

int doubleAdjusted(int value)
{
    int adjusted = addOne(value);
    int result = adjusted * 2;
    return result; // 42: resumes the original caller.
}

int demonstrateNested()
{
    int value = 20;
    int result = doubleAdjusted(value);
    std::cout << result << '\n';
    return result;
}
