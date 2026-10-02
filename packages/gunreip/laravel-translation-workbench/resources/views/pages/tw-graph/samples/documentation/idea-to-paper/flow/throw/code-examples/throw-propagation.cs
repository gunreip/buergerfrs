// Inside a class; using System; using System.Collections.Generic.
static void ValidateInput(List<string> events)
{
    events.Add("inner");
    throw new ArgumentException("Invalid input");
    // events.Add("unreachable-inner"); // Would produce an unreachable-code warning.
}

static void DemonstratePropagation(List<string> events)
{
    try
    {
        events.Add("outer-try");
        ValidateInput(events);
        events.Add("unreachable-after-call");
    }
    catch (ArgumentException error)
    {
        events.Add("outer-caught");
    }
    events.Add("after");
}
