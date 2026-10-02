var items = LoadItems(); // List<Item>
var index = 0;
while (index < items.Count) {
    var item = items[index];
    if (MayProcess(item)) {
        ProcessItem(item);
        index++;
    } else {
        break;
    }
}
ContinueProcess();
