#include <stdexcept>
#include <string>
#include <vector>

void demonstrateThrow(std::vector<std::string>& events)
{
    try {
        events.push_back("try");
        throw std::invalid_argument("Invalid input");
        events.push_back("unreachable"); // Skipped after THROW.
    } catch (const std::invalid_argument& error) {
        events.push_back("caught");
    }
    events.push_back("after");
}
