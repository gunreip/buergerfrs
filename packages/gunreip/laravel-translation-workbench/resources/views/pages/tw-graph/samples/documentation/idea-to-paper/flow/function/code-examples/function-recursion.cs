using System;

class FunctionExample
{
    // Small non-negative integers; Factorial(0) = 1.
    static int Factorial(int n)
    {
        if (n == 0) {
            return 1;
        }
        int inner = Factorial(n - 1);
        return n * inner;
    }

    static void DemonstrateRecursion()
    {
        int result = Factorial(2);
        Console.WriteLine(result); // 2; frames unwind: 0, 1, 2.
    }
}
