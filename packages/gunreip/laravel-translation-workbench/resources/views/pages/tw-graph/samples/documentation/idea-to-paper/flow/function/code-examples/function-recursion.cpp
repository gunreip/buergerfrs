#include <iostream>

// Small non-negative integers; factorial(0) = 1.
int factorial(int n)
{
    if (n == 0) {
        return 1;
    }
    int inner = factorial(n - 1);
    return n * inner;
}

void demonstrateRecursion()
{
    int result = factorial(2);
    std::cout << result << '\n'; // 2; frames unwind: 0, 1, 2.
}
