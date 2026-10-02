#include <iostream>

int addOne(int value)
{
    int result = value + 1;
    return result;
}

void demonstrateMultipleCalls()
{
    int first = addOne(10);  // Call site 1 resumes here with 11.
    int second = addOne(40); // Call site 2 resumes here with 41.
    std::cout << "first=" << first << "; second=" << second << '\n';
}
