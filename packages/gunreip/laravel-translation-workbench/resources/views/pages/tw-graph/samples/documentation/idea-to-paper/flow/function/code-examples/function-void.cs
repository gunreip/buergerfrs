using System;

class FunctionExample
{
    static void LogMessage(string message)
    {
        Console.WriteLine(message);
    }

    static void DemonstrateVoid()
    {
        Console.WriteLine("before");
        string message = "Processed item";
        LogMessage(message); // No result assignment.
        Console.WriteLine("after");
    }
}
