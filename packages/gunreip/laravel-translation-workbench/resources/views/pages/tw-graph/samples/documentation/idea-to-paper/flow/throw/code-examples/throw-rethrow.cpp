#include <stdexcept>
#include <string>
#include <vector>

void validateAndLog(std::vector<std::string>& events)
{
    try {
        events.push_back("inner");
        throw std::invalid_argument("Invalid input");
        events.push_back("unreachable-inner");
    } catch (const std::invalid_argument&) {
        events.push_back("inner-log");
        throw; // Rethrows the currently handled exception without copying it.
        events.push_back("unreachable-after-rethrow");
    }
}

void demonstrateRethrow(std::vector<std::string>& events)
{
    try {
        events.push_back("outer-try");
        validateAndLog(events);
        events.push_back("unreachable-after-call");
    } catch (const std::invalid_argument&) {
        events.push_back("outer-caught");
    }
    events.push_back("after");
}
