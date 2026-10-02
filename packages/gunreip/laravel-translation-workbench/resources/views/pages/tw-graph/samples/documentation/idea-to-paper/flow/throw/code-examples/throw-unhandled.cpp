#include <iostream>
#include <stdexcept>

// C++ has no FINALLY. This explicit handler only cleans up and rethrows.
void failWithCleanup()
{
    try {
        std::cout << "original\n";
        throw std::invalid_argument("Original failure");
    } catch (...) {
        std::cout << "cleanup\n";
        throw; // The same exception escapes; this is not recovery.
    }
}

void demonstrateUnhandled()
{
    std::cout << "call\n";
    failWithCleanup();
    std::cout << "unreachable-after-call\n";
}
// With no matching outer handler, std::terminate is invoked.
// Do not assume stack unwinding/destructor cleanup for an uncaught exception.
