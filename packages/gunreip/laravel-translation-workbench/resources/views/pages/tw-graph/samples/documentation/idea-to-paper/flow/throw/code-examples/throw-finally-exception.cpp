#include <exception>
#include <stdexcept>
#include <string>
#include <vector>

// C++ has no FINALLY. Explicit exceptional-path cleanup, not a destructor.
void failAndCleanup(std::vector<std::string>& events)
{
    try {
        events.push_back("original");
        throw std::invalid_argument("Original failure");
    } catch (const std::invalid_argument&) {
        events.push_back("cleanup");
        std::throw_with_nested(std::runtime_error("Cleanup failed"));
    }
}

void demonstrateReplacement(std::vector<std::string>& events)
{
    try {
        events.push_back("outer-try");
        failAndCleanup(events);
        events.push_back("unreachable-after-call");
    } catch (const std::runtime_error& error) {
        events.push_back("outer-caught");
        try {
            std::rethrow_if_nested(error);
        } catch (const std::invalid_argument& original) {
            // Inspect original.what() as diagnostic context.
        }
    }
    events.push_back("after");
}
