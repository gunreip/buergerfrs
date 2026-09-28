<?php
if ($enabled) {
    prepareOperation();
    try {
        performOperation();
        recordSuccess();
    } catch (Throwable $error) {
        handleFailure($error);
    }
} else {
    recordSkipped();
}
continueProcess();
