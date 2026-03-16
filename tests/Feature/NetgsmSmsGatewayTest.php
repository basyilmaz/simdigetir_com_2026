<?php

namespace Tests\Feature;

use App\Domain\Notifications\Gateways\NetgsmSmsGateway;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class NetgsmSmsGatewayTest extends TestCase
{
    public function test_netgsm_gateway_returns_sent_in_sandbox_mode(): void
    {
        config()->set('services_integrations.sms.providers.netgsm.sandbox', true);

        $result = app(NetgsmSmsGateway::class)->send('0555 111 22 33', [
            'body' => 'Deneme mesaji',
        ]);

        $this->assertSame('sent', $result['status']);
        $this->assertNotNull($result['provider_message_id']);
        $this->assertStringStartsWith('NETGSM-SB-', (string) $result['provider_message_id']);
        $this->assertNull($result['error_message']);
    }

    public function test_netgsm_gateway_fails_when_live_config_is_incomplete(): void
    {
        config()->set('services_integrations.sms.providers.netgsm.sandbox', false);
        config()->set('services_integrations.sms.providers.netgsm.base_url', '');
        config()->set('services_integrations.sms.providers.netgsm.username', '');
        config()->set('services_integrations.sms.providers.netgsm.password', '');
        config()->set('services_integrations.sms.providers.netgsm.header', '');

        $result = app(NetgsmSmsGateway::class)->send('0555 111 22 33', [
            'body' => 'Deneme mesaji',
        ]);

        $this->assertSame('failed', $result['status']);
        $this->assertNull($result['provider_message_id']);
        $this->assertSame('Netgsm config is incomplete.', $result['error_message']);
    }

    public function test_netgsm_gateway_posts_to_provider_and_parses_success_response(): void
    {
        config()->set('services_integrations.sms.providers.netgsm.sandbox', false);
        config()->set('services_integrations.sms.providers.netgsm.base_url', 'https://api.netgsm.test');
        config()->set('services_integrations.sms.providers.netgsm.username', '8508402809');
        config()->set('services_integrations.sms.providers.netgsm.password', 'secret-pass');
        config()->set('services_integrations.sms.providers.netgsm.header', 'CASTAMONLTD');

        Http::fake([
            'https://api.netgsm.test/sms/send' => Http::response('00 123456', 200),
        ]);

        $result = app(NetgsmSmsGateway::class)->send('+90 555 111 22 33', [
            'body' => 'Siparisiniz alindi.',
        ]);

        $this->assertSame('sent', $result['status']);
        $this->assertSame('123456', $result['provider_message_id']);
        $this->assertNull($result['error_message']);

        Http::assertSent(function ($request): bool {
            $payload = $request->data();

            return $request->url() === 'https://api.netgsm.test/sms/send'
                && ($payload['gsmno'] ?? null) === '905551112233'
                && ($payload['message'] ?? null) === 'Siparisiniz alindi.'
                && ($payload['msgheader'] ?? null) === 'CASTAMONLTD';
        });
    }

    public function test_netgsm_gateway_marks_non_success_body_as_failed(): void
    {
        config()->set('services_integrations.sms.providers.netgsm.sandbox', false);
        config()->set('services_integrations.sms.providers.netgsm.base_url', 'https://api.netgsm.test');
        config()->set('services_integrations.sms.providers.netgsm.username', '8508402809');
        config()->set('services_integrations.sms.providers.netgsm.password', 'secret-pass');
        config()->set('services_integrations.sms.providers.netgsm.header', 'CASTAMONLTD');

        Http::fake([
            'https://api.netgsm.test/sms/send' => Http::response('30 INVALID_USER', 200),
        ]);

        $result = app(NetgsmSmsGateway::class)->send('0555 111 22 33', [
            'body' => 'Test',
        ]);

        $this->assertSame('failed', $result['status']);
        $this->assertSame('INVALID_USER', $result['provider_message_id']);
        $this->assertStringContainsString('Netgsm rejected message:', (string) $result['error_message']);
    }
}
