<?php
try {
    performOperation();
    recordSuccess();
} catch (Throwable $error) {
    handleFailure($error);
}
continueProcess();
