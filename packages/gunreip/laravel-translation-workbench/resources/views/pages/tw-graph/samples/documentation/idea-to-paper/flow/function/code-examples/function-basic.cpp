#include <iostream>

int addOne(int value)
{
    int result = value + 1;
    return result;
}

int demonstrateCall()
{
    int value = 41;
    int result = addOne(value);
    std::cout << result << '\n'; // 42, after addOne has returned.
    return result;
}
