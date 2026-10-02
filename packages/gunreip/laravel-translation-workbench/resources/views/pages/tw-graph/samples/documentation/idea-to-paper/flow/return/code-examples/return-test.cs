// Inside a class; using System.Collections.Generic.
static void NotifyIfEnabled(bool enabled, List<string> events)
{
    if (!enabled)
    {
        return;
    }
    events.Add("notified");
    return; // Optional at the end of this void method.
}
