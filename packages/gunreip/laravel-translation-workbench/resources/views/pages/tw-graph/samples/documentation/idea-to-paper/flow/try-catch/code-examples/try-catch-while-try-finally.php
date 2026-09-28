<?php
$queue = loadQueue();
prepareQueue($queue);
while (hasNext($queue)) {
    $item = nextItem($queue);
    try {
        processItem($item);
        recordSuccess($item);
    } catch (Throwable $error) {
        handleItemFailure($item, $error);
    } finally {
        cleanupItem($item);
    }
}
showSummary();
