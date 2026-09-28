PrepareOperation();
Result result;
do {
    result = PerformAction();
    RecordResult(result);
} while (ShouldRepeat(result));
ShowSummary();
