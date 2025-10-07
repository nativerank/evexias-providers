<?php

namespace App\Listeners;

use App\EmailDeliveryStatus;
use App\Events\LeadCreated;
use App\Mail\LeadNotification;
use App\Models\LeadEmailLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendLeadNotificationEmail implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(LeadCreated $event): void
    {
        $lead = $event->lead->load('practice.marketingEmails');

        if ($lead->practice->marketingEmails->isEmpty()) {
            return;
        }

        $isProduction = app()->environment('production');

        foreach ($lead->practice->marketingEmails as $marketingEmail) {
            $recipientEmail = $isProduction
                ? $marketingEmail->email
                : config('mail.from.address');

            $log = LeadEmailLog::create([
                'lead_id' => $lead->id,
                'recipient_email' => $marketingEmail->email,
                'status' => EmailDeliveryStatus::PENDING,
            ]);

            try {
                Mail::to($recipientEmail)
                    ->send(new LeadNotification($lead));

                $log->update([
                    'status' => EmailDeliveryStatus::SENT,
                    'sent_at' => now(),
                ]);
            } catch (\Exception $e) {
                $log->update([
                    'status' => EmailDeliveryStatus::FAILED,
                    'error_message' => $e->getMessage(),
                ]);
            }
        }
    }
}
