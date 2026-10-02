#include <stdexcept>
#include <string>
#include <vector>

void validateAndCleanup(std::vector<std::string>& events)
{
    // C++ has no FINALLY. This guard records cleanup when the scope is left.
    // The example assumes cleanup completes without throwing.
    struct CleanupGuard {
        std::vector<std::string>& events;
        ~CleanupGuard() noexcept { events.push_back("cleanup"); }
    } cleanup{events};
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

void demonstrateFinally(std::vector<std::string>& events)
{
    try {
        events.push_back("outer-try");
        validateAndCleanup(events);
        events.push_back("unreachable-after-call");
    } catch (const std::invalid_argument&) {
        events.push_back("outer-caught");
    }
    events.push_back("after");
}
