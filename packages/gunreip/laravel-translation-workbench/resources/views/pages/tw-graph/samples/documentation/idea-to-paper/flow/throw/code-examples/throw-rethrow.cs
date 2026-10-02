// Inside a class; using System; using System.Collections.Generic.
static void ValidateAndLog(List<string> events)
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
}

static void DemonstrateRethrow(List<string> events)
{
    try
    {
        events.Add("outer-try");
        ValidateAndLog(events);
        events.Add("unreachable-after-call");
    }
    catch (ArgumentException)
    {
        events.Add("outer-caught");
    }
    events.Add("after");
}
