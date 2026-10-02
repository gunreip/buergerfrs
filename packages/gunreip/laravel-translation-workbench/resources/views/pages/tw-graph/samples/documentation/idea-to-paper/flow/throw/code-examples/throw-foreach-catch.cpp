#include <stdexcept>
#include <string>
#include <vector>

std::vector<std::string> processItems(const std::vector<bool>& items)
{
    std::vector<std::string> events;
    int index = 0;
    for (bool valid : items) {
        try {
            if (!valid) throw std::invalid_argument("Invalid item");
            events.push_back("processed:" + std::to_string(index));
        } catch (const std::invalid_argument&) {
            events.push_back("caught:" + std::to_string(index));
        }
        ++index;
    }
    events.push_back("completed");
    events.push_back("after");
    return events;
}
// {true, false, true} => processed:0, caught:1, processed:2, completed, after
