var pending = LoadPendingItems(); // Queue<Item>
while (pending.Count > 0) {
    var item = pending.Dequeue();
    ProcessItem(item);
}
ShowSummary();
