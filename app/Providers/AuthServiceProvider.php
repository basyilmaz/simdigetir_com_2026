<?php

namespace App\Providers;

use App\Models\Courier;
use App\Models\AdminAuditLog;
use App\Models\FormDefinition;
use App\Models\FormSubmission;
use App\Models\LegalDocument;
use App\Models\Order;
use App\Models\PaymentTransaction;
use App\Models\PricingRule;
use App\Models\SettlementBatch;
use App\Models\SitemapEntry;
use App\Models\SupportTicket;
use App\Models\User;
use App\Policies\CourierPolicy;
use App\Policies\AdminAuditLogPolicy;
use App\Policies\AdPolicy;
use App\Policies\FinancePolicy;
use App\Policies\LandingContentPolicy;
use App\Policies\LeadPolicy;
use App\Policies\OrderPolicy;
use App\Policies\SupportTicketPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Modules\Landing\Models\LandingPage;
use Modules\Landing\Models\LandingPageSection;
use Modules\Landing\Models\LandingSectionItem;
use Modules\Landing\Models\LandingSectionRevision;
use Modules\AdsCore\Models\AdAd;
use Modules\AdsCore\Models\AdAdset;
use Modules\AdsCore\Models\AdCampaign;
use Modules\AdsCore\Models\AdConnection;
use Modules\AdsCore\Models\AdConversion;
use Modules\AdsCore\Models\AdCreative;
use Modules\AdsCore\Models\AdDailyMetric;
use Modules\AdsCore\Models\AdEvent;
use Modules\AdsCore\Models\AdSyncLog;
use Modules\Leads\Models\Lead;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * @var array<class-string, class-string>
     */
    protected $policies = [
        LandingPage::class => LandingContentPolicy::class,
        LandingPageSection::class => LandingContentPolicy::class,
        LandingSectionItem::class => LandingContentPolicy::class,
        LandingSectionRevision::class => LandingContentPolicy::class,
        FormDefinition::class => LandingContentPolicy::class,
        FormSubmission::class => LandingContentPolicy::class,
        LegalDocument::class => LandingContentPolicy::class,
        SitemapEntry::class => LandingContentPolicy::class,
        Lead::class => LeadPolicy::class,
        AdminAuditLog::class => AdminAuditLogPolicy::class,
        Order::class => OrderPolicy::class,
        Courier::class => CourierPolicy::class,
        SupportTicket::class => SupportTicketPolicy::class,
        PricingRule::class => FinancePolicy::class,
        PaymentTransaction::class => FinancePolicy::class,
        SettlementBatch::class => FinancePolicy::class,
        User::class => UserPolicy::class,
        AdConnection::class => AdPolicy::class,
        AdCampaign::class => AdPolicy::class,
        AdAdset::class => AdPolicy::class,
        AdAd::class => AdPolicy::class,
        AdCreative::class => AdPolicy::class,
        AdEvent::class => AdPolicy::class,
        AdConversion::class => AdPolicy::class,
        AdSyncLog::class => AdPolicy::class,
        AdDailyMetric::class => AdPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
