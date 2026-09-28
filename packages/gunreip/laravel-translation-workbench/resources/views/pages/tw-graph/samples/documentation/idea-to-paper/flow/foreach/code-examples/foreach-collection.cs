var items = LoadItems();
foreach (var item in items)
{
    ProcessItem(item);
}
ShowSummary();
