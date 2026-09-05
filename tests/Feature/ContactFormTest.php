<?php

namespace Tests\Feature;

use App\Mail\ContactMessageReceived;
use App\Models\ContactMessage;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ContactFormTest extends TestCase
{
    use RefreshDatabase;

    protected function payload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'company' => 'Acme Inc.',
            'budget' => '€15k – €40k',
            'subject' => 'Partner portal with Salesforce sync',
            'message' => 'We need a partner portal that keeps deal registration in sync with Salesforce.',
            'website' => '',
            'rendered_at' => time() - 30,
        ], $overrides);
    }

    public function test_it_stores_the_message_and_notifies_by_email(): void
    {
        Mail::fake();

        $this->post(route('contact.store'), $this->payload())
            ->assertRedirect(route('contact.show'))
            ->assertSessionHas('status');

        $this->assertDatabaseHas('contact_messages', [
            'email' => 'jane@example.com',
            'company' => 'Acme Inc.',
        ]);

        Mail::assertSent(ContactMessageReceived::class);
    }

    public function test_it_sends_the_notification_to_the_configured_recipient(): void
    {
        Mail::fake();
        Setting::put('contact_recipient', 'leads@example.com');

        $this->post(route('contact.store'), $this->payload());

        Mail::assertSent(ContactMessageReceived::class, fn ($mail) => $mail->hasTo('leads@example.com'));
    }

    public function test_it_validates_required_fields(): void
    {
        $this->post(route('contact.store'), $this->payload([
            'name' => '',
            'email' => 'not-an-email',
            'message' => 'too short',
        ]))->assertSessionHasErrors(['name', 'email', 'message']);

        $this->assertDatabaseCount('contact_messages', 0);
    }

    public function test_it_rejects_submissions_that_fill_the_honeypot(): void
    {
        $this->post(route('contact.store'), $this->payload(['website' => 'https://spam.example']))
            ->assertSessionHasErrors('website');

        $this->assertDatabaseCount('contact_messages', 0);
    }

    public function test_it_rejects_submissions_sent_faster_than_a_human_could_type(): void
    {
        $this->post(route('contact.store'), $this->payload(['rendered_at' => time()]))
            ->assertSessionHasErrors('message');

        $this->assertDatabaseCount('contact_messages', 0);
    }

    public function test_it_keeps_the_message_when_the_mailer_fails(): void
    {
        Mail::shouldReceive('to->send')->andThrow(new \RuntimeException('SMTP down'));

        $this->post(route('contact.store'), $this->payload())
            ->assertRedirect(route('contact.show'))
            ->assertSessionHas('status');

        $this->assertDatabaseCount('contact_messages', 1);
    }

    public function test_it_records_the_client_metadata(): void
    {
        Mail::fake();

        $this->post(route('contact.store'), $this->payload());

        $message = ContactMessage::sole();

        $this->assertNotNull($message->ip_address);
        $this->assertTrue($message->isUnread());
    }
}
