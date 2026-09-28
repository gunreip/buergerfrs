PrepareOperation();
Result result;
do {
    result = PerformAction();
} while (ShouldRepeat(result));
ShowSummary();
