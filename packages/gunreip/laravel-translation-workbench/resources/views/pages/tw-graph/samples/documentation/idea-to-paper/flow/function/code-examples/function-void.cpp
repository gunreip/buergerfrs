#include <iostream>
#include <string>

void logMessage(const std::string& message)
{
    std::cout << message << '\n';
}

void demonstrateVoid()
{
    std::cout << "before\n";
    std::string message = "Processed item";
    logMessage(message); // No result assignment.
    std::cout << "after\n";
}
