// Inside a class; using System; using System.Collections.Generic.
static void ValidateAndCleanup(List<string> events)
{
    try
    {
        events.Add("inner");
        throw new ArgumentException("Invalid input");
        // events.Add("unreachable-inner");
    }
    catch (ArgumentException)
    {
        events.Add("inner-log");
        throw; // Preserves the original stack trace; do not use throw error.
        // events.Add("unreachable-after-rethrow");
    }
    finally
    {
        events.Add("cleanup");
    }
}

static void DemonstrateFinally(List<string> events)
{
    try
    {
        events.Add("outer-try");
        ValidateAndCleanup(events);
        events.Add("unreachable-after-call");
    }
    catch (ArgumentException)
    {
        events.Add("outer-caught");
    }
    events.Add("after");
}
