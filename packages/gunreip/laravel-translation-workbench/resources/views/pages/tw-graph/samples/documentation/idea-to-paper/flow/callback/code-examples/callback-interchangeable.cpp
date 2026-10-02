#include <iostream>

int addOne(int value)
{
    return value + 1;
}

int doubleValue(int value)
{
    return value * 2;
}

int apply(int value, int (*callback)(int))
{
    int result = callback(value);
    return result;
}

void demonstrateCallbacks()
{
    int first = apply(20, addOne);
    int second = apply(20, doubleValue);
    std::cout << "first=" << first << "; second=" << second << '\n'; // 21 and 40
}
