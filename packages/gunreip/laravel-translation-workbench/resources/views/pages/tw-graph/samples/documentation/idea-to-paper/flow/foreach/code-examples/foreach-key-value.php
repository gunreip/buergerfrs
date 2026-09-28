<?php
$settings = loadSettings();
foreach ($settings as $key => $value) {
    processSetting($key, $value);
}
showSummary();
