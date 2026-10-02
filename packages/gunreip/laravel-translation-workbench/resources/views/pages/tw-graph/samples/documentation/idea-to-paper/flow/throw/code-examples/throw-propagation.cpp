#include <stdexcept>
#include <string>
#include <vector>

void validateInput(std::vector<std::string>& events)
{
    events.push_back("inner");
    throw std::invalid_argument("Invalid input");
    events.push_back("unreachable-inner");
}

void demonstratePropagation(std::vector<std::string>& events)
{
    try {
        events.push_back("outer-try");
        validateInput(events);
        events.push_back("unreachable-after-call");
    } catch (const std::invalid_argument& error) {
        events.push_back("outer-caught");
    }
    events.push_back("after");
}
