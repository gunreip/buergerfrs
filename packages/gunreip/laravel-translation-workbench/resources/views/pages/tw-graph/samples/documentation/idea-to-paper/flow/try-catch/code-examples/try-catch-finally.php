<?php
try {
    performOperation();
    recordSuccess();
} catch (Throwable $error) {
    handleFailure($error);
} finally {
    cleanup();
}
continueProcess();
