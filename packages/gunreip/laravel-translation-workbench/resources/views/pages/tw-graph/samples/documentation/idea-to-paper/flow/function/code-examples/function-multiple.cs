using System;

class FunctionExample
{
    static int AddOne(int value)
    {
        int result = value + 1;
        return result;
    }

    static void DemonstrateMultipleCalls()
    {
        int first = AddOne(10);  // Call site 1 resumes here with 11.
        int second = AddOne(40); // Call site 2 resumes here with 41.
        Console.WriteLine($"first={first}; second={second}");
    }
}
