<?php

namespace Gunreip\TranslationWorkbench\Foundation;

use RuntimeException;

/** A file observation cannot decide which saved translation state should win. */
class TranslationWorkbenchLangImportConflict extends RuntimeException {}
