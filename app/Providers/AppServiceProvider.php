<?php

namespace App\Providers;

use App\Observers\AdminMutationAuditObserver;
use App\Observers\LegalDocumentObserver;
use App\Observers\PricingRuleCacheObserver;
use App\Models\FormDefinition;
use App\Models\LegalDocument;
use App\Models\Courier;
use App\Models\CourierDocument;
use App\Models\CourierAvailability;
use App\Models\DeliveryProof;
use App\Models\OrderProof;
use App\Models\OrderAssignment;
use App\Models\DispatchDecision;
use App\Models\CorporateAccount;
use App\Models\CorporateAccountAddress;
use App\Models\CourierWalletEntry;
use App\Models\NotificationDispatch;
use App\Models\NotificationTemplate;
use App\Models\OrderTrackingEvent;
use App\Models\Order;
use App\Models\PaymentReconciliation;
use App\Models\PaymentRefund;
use App\Models\PricingRule;
use App\Models\SettlementBatch;
use App\Models\SitemapEntry;
use App\Models\SupportTicket;
use App\Models\SupportTicketMessage;
use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\RateLimiter;
use Modules\Landing\Models\LandingPage;
use Modules\Landing\Models\LandingPageSection;
use Modules\Landing\Models\LandingSectionItem;
use Modules\Leads\Models\Lead;
use Modules\Settings\Models\Setting;
use PDO;
use Throwable;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Fix for older MySQL versions (< 5.7.7) with utf8mb4
        Schema::defaultStringLength(191);
        $this->configureStorageFallbacks();
        $this->configureRateLimits();

        LandingPage::observe(AdminMutationAuditObserver::class);
        LandingPageSection::observe(AdminMutationAuditObserver::class);
        LandingSectionItem::observe(AdminMutationAuditObserver::class);
        Setting::observe(AdminMutationAuditObserver::class);
        FormDefinition::observe(AdminMutationAuditObserver::class);
        Lead::observe(AdminMutationAuditObserver::class);
        SitemapEntry::observe(AdminMutationAuditObserver::class);
        LegalDocument::observe(AdminMutationAuditObserver::class);
        PricingRule::observe(AdminMutationAuditObserver::class);
        PricingRule::observe(PricingRuleCacheObserver::class);
        Order::observe(AdminMutationAuditObserver::class);
        Courier::observe(AdminMutationAuditObserver::class);
        CourierDocument::observe(AdminMutationAuditObserver::class);
        CourierAvailability::observe(AdminMutationAuditObserver::class);
        OrderAssignment::observe(AdminMutationAuditObserver::class);
        DispatchDecision::observe(AdminMutationAuditObserver::class);
        OrderTrackingEvent::observe(AdminMutationAuditObserver::class);
        OrderProof::observe(AdminMutationAuditObserver::class);
        SettlementBatch::observe(AdminMutationAuditObserver::class);
        CourierWalletEntry::observe(AdminMutationAuditObserver::class);
        PaymentReconciliation::observe(AdminMutationAuditObserver::class);
        PaymentRefund::observe(AdminMutationAuditObserver::class);
        NotificationTemplate::observe(AdminMutationAuditObserver::class);
        NotificationDispatch::observe(AdminMutationAuditObserver::class);
        SupportTicket::observe(AdminMutationAuditObserver::class);
        SupportTicketMessage::observe(AdminMutationAuditObserver::class);
        CorporateAccount::observe(AdminMutationAuditObserver::class);
        CorporateAccountAddress::observe(AdminMutationAuditObserver::class);
        LegalDocument::observe(LegalDocumentObserver::class);
    }

    private function configureStorageFallbacks(): void
    {
        $sessionDriver = (string) config('session.driver');
        $cacheStore = (string) config('cache.default');

        if ($sessionDriver !== 'database' && $cacheStore !== 'database') {
            return;
        }

        if (! $this->isPrimaryDatabaseReachable()) {
            if ($sessionDriver === 'database') {
                config(['session.driver' => 'file']);
            }

            if ($cacheStore === 'database') {
                config(['cache.default' => 'file']);
            }

            return;
        }

        if ($sessionDriver !== 'database') {
            return;
        }

        try {
            $sessionConnection = (string) (config('session.connection') ?: config('database.default'));
            $sessionTable = (string) config('session.table', 'sessions');

            if (! Schema::connection($sessionConnection)->hasTable($sessionTable)) {
                config(['session.driver' => 'file']);
            }
        } catch (Throwable) {
            config(['session.driver' => 'file']);

            if ($cacheStore === 'database') {
                config(['cache.default' => 'file']);
            }
        }
    }

    private function isPrimaryDatabaseReachable(): bool
    {
        $defaultConnection = (string) config('database.default');
        $connection = (array) config("database.connections.$defaultConnection", []);
        $driver = (string) ($connection['driver'] ?? '');

        if ($driver === 'sqlite') {
            return true;
        }

        $timeoutSeconds = max((float) env('DB_RUNTIME_PROBE_TIMEOUT', 0.3), 0.1);
        $host = $connection['host'] ?? null;
        if (is_array($host)) {
            $host = $host[0] ?? null;
        }

        if (is_string($host) && $host !== '') {
            $port = (int) ($connection['port'] ?? 3306);
            $socket = @fsockopen($host, $port, $errno, $errstr, $timeoutSeconds);

            if ($socket === false) {
                return false;
            }

            fclose($socket);
        }

        if (in_array($driver, ['mysql', 'pgsql', 'sqlsrv'], true)) {
            return $this->probeDatabaseConnection($driver, $connection, $timeoutSeconds);
        }

        return true;
    }

    private function probeDatabaseConnection(string $driver, array $connection, float $timeoutSeconds): bool
    {
        $host = (string) ($connection['host'] ?? '127.0.0.1');
        $port = (int) ($connection['port'] ?? ($driver === 'pgsql' ? 5432 : ($driver === 'sqlsrv' ? 1433 : 3306)));
        $database = (string) ($connection['database'] ?? '');
        $username = (string) ($connection['username'] ?? '');
        $password = (string) ($connection['password'] ?? '');

        if ($driver === 'mysql') {
            $database = $database !== '' ? $database : 'information_schema';
            $charset = (string) ($connection['charset'] ?? 'utf8mb4');
            $dsn = "mysql:host={$host};port={$port};dbname={$database};charset={$charset}";
        } elseif ($driver === 'pgsql') {
            $database = $database !== '' ? $database : 'postgres';
            $dsn = "pgsql:host={$host};port={$port};dbname={$database}";
        } else {
            $database = $database !== '' ? $database : 'master';
            $dsn = "sqlsrv:Server={$host},{$port};Database={$database}";
        }

        try {
            $pdo = new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_TIMEOUT => max((int) ceil($timeoutSeconds), 1),
            ]);
            $pdo = null;
        } catch (Throwable) {
            return false;
        }

        return true;
    }

    private function configureRateLimits(): void
    {
        RateLimiter::for('quote-api', function (Request $request) {
            return Limit::perMinute(30)->by($request->ip());
        });

        RateLimiter::for('orders-api', function (Request $request) {
            return Limit::perMinute(60)->by($request->ip());
        });

        RateLimiter::for('payments-api', function (Request $request) {
            return Limit::perMinute(40)->by($request->ip());
        });

        RateLimiter::for('dispatch-api', function (Request $request) {
            return Limit::perMinute(50)->by((string) ($request->user()?->id ?: $request->ip()));
        });

        RateLimiter::for('auth-api', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });
    }
}
