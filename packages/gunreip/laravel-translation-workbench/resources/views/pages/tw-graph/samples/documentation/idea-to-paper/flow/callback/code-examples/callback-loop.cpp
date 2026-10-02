#include <vector>

int addOne(int value)
{
    return value + 1;
}

std::vector<int> mapEach(const std::vector<int>& items, int (*callback)(int))
{
    std::vector<int> results;
    for (int item : items) {
        int result = callback(item);
        results.push_back(result);
    }
    return results;
}

std::vector<int> demonstrateCallbackLoop()
{
    return mapEach({10, 20, 30}, addOne); // {11, 21, 31}
}
