<?php

// app/Support/Navigation/AdminAttention.php

namespace App\Support\Navigation;

use App\Models\HtmlViewAuditFinding;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Throwable;

class AdminAttention
{
    public static function administration(): bool
    {
        return self::reference();
    }

    public static function reference(): bool
    {
        return self::htmlViewAudit();
    }

    public static function htmlViewAudit(): bool
    {
        return Cache::remember(
            'admin_attention.html_view_audit.open',
            now()->addMinute(),
            fn(): bool => self::hasOpenHtmlViewAuditFindings(),
        );
    }

    private static function hasOpenHtmlViewAuditFindings(): bool
    {
        try {
            if (! Schema::hasTable('html_view_audit_findings')) {
                return false;
            }

            return HtmlViewAuditFinding::query()
                ->where('status', 'open')
                ->exists();
        } catch (Throwable) {
            return false;
        }
    }
}
