<?php
prepareOperation();
do {
    $result = performAction();
} while (shouldRepeat($result));
showSummary();
