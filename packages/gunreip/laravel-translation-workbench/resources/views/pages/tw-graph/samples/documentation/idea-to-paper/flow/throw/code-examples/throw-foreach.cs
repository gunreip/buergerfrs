// Inside a class; using System; using System.Collections.Generic.
static List<string> ProcessItems(bool[] items)
{
    var events = new List<string>();
    int index = 0;
    try
    {
        foreach (bool valid in items)
        {
            if (!valid) throw new ArgumentException("Invalid item");
            events.Add("processed:" + index++);
        }
        events.Add("completed");
    }
    catch (ArgumentException)
    {
        events.Add("caught");
    }
    events.Add("after");
    return events;
}
// ProcessItems(new[]{true, false, true}) => [processed:0, caught, after]
