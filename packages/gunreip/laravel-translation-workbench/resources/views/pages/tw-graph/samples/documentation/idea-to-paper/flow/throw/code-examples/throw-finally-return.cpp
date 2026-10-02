#include <optional>
#include <stdexcept>
#include <string>
#include <vector>

// C++17: no FINALLY. Explicit equivalent, not a throwing destructor.
void cleanup(std::vector<std::string>& events)
{
    events.push_back("cleanup");
    throw std::runtime_error("Cleanup failed");
}

int computeAndCleanup(std::vector<std::string>& events)
{
    events.push_back("pending-return");
    int pendingResult = 42;
    cleanup(events);
    return pendingResult; // Never reached in this example.
}

std::optional<int> demonstrateFinallyThrow(std::vector<std::string>& events)
{
    std::optional<int> result;
    try {
        events.push_back("outer-try");
        result = computeAndCleanup(events);
        events.push_back("unreachable-after-call");
    } catch (const std::runtime_error&) {
        events.push_back("outer-caught");
    }
    events.push_back("after");
    return result; // Still empty.
}
