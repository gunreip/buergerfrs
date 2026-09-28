<?php
$attempt = 0;
$maxAttempts = 3;
do {
    $attempt++;
    $success = tryOperation();
} while (!$success && $attempt < $maxAttempts);
reportOutcome($success, $attempt);
