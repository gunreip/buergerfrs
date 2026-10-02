#include <stdexcept>
#include <string>
#include <vector>

std::vector<std::string> processItems(const std::vector<bool>& items)
{
    std::vector<std::string> events;
    int index = 0;
    try {
        for (bool valid : items) {
            if (!valid) throw std::invalid_argument("Invalid item");
            events.push_back("processed:" + std::to_string(index++));
        }
        events.push_back("completed");
    } catch (const std::invalid_argument&) {
        events.push_back("caught");
    }
    events.push_back("after");
    return events;
}
// processItems({true, false, true}) => {"processed:0", "caught", "after"}
