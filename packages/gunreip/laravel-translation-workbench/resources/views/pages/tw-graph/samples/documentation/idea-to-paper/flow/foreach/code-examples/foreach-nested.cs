var groups = LoadGroups();
foreach (var group in groups)
{
    foreach (var item in group)
    {
        ProcessItem(item);
        RecordItemResult(item);
    }
    FinalizeGroup(group);
}
ShowSummary();
