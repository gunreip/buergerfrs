#include <stdexcept>
#include <string>
#include <vector>

// C++ has no FINALLY. Explicit fallback handling, not the graph's control flow.
int failWithExplicitFallback(std::vector<std::string>& events)
{
    try {
        events.push_back("original");
        throw std::invalid_argument("Original failure");
    } catch (const std::invalid_argument&) {
        events.push_back("handled");
    }
    events.push_back("cleanup");
    return 7;
}
