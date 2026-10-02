using System;
using System.Collections.Generic;

class CallbackExample
{
    static int AddOne(int value) => value + 1;

    static List<int> MapEach(IEnumerable<int> items, Func<int, int> callback)
    {
        var results = new List<int>();
        foreach (int item in items) {
            int result = callback(item);
            results.Add(result);
        }
        return results;
    }

    static List<int> DemonstrateCallbackLoop()
    {
        return MapEach(new[] {10, 20, 30}, AddOne); // {11, 21, 31}
    }
}
