#include <iostream>

int addOne(int value)
{
    return value + 1;
}

int apply(int value, int (*callback)(int))
{
    int result = callback(value);
    return result;
}

void demonstrateCallback()
{
    int result = apply(41, addOne); // Pass a function pointer.
    std::cout << result << '\n'; // 42
}
