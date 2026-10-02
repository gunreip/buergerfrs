// Inside a class; using System.
static void FailWithCleanup()
{
    try
    {
        Console.WriteLine("original");
        throw new ArgumentException("Original failure");
    }
    finally
    {
        Console.WriteLine("cleanup");
    }
}

static void DemonstrateUnhandled()
{
    Console.WriteLine("call");
    FailWithCleanup(); // No local CATCH: propagates to the caller/host.
    Console.WriteLine("unreachable-after-call");
}
// FINALLY runs during normal exception unwinding to a handler.
// Do not rely on FINALLY when an unhandled exception terminates the process.
