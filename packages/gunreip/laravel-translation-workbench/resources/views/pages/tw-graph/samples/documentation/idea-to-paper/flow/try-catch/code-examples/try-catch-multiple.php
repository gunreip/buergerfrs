<?php
try {
    performOperation();
    recordSuccess();
} catch (ValidationFailure $error) {
    handleValidation($error);
} catch (StorageFailure $error) {
    handleStorage($error);
} catch (Throwable $error) {
    handleOther($error);
}
continueProcess();
