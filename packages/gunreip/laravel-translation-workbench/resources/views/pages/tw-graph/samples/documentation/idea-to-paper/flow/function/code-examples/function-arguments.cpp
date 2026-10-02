#include <iostream>

int adjust(int value, int factor)
{
    value = value + 1; // Parameters are values, not references.
    int result = value * factor;
    return result;
}

void demonstrateArguments()
{
    int value = 20;
    int factor = 2;
    int result = adjust(value, factor);
    std::cout << "value=" << value << "; factor=" << factor
              << "; result=" << result << '\n'; // 20, 2, 42
}
