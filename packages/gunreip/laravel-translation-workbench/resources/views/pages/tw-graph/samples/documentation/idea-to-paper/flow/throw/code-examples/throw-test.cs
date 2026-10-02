// Inside a class; using System; using System.Collections.Generic.
static List<string> ProcessItems(bool[] items)
{
    var events = new List<string>();
    int index = 0;
    foreach (bool valid in items)
    {
        try
        {
            if (!valid) throw new ArgumentException("Invalid item");
            events.Add("processed:" + index);
        }
        catch (ArgumentException)
        {
            events.Add("caught:" + index);
        }
        index++;
    }
    events.Add("completed");
    events.Add("after");
    return events;
}
// [true, false, true] => processed:0, caught:1, processed:2, completed, after
