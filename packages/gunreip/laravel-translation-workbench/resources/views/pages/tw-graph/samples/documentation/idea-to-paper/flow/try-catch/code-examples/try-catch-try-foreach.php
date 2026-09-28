<?php
$items = loadItems();
try {
    foreach ($items as $item) {
        processItem($item);
        recordSuccess($item);
    }
} catch (Throwable $error) {
    handleFailure($error);
}
continueProcess();
