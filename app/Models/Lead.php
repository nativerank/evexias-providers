<?php

namespace App\Models;

use App\LeadSource;
use App\LeadStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Propaganistas\LaravelPhone\PhoneNumber;

class Lead extends Model
{
    use HasFactory;

    protected $fillable = [
        'practice_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'data',
        'source',
        'lead_type',
        'status',
        'lead_created_at',
        'contacted_at',
    ];

    protected $casts = [
        'data' => 'array',
        'lead_created_at' => 'datetime',
        'contacted_at' => 'datetime',
        'status' => LeadStatus::class,
        'source' => LeadSource::class,
    ];

    public function practice(): BelongsTo
    {
        return $this->belongsTo(Practice::class);
    }

    public function emailLogs(): HasMany
    {
        return $this->hasMany(LeadEmailLog::class);
    }

    public function getField(string $key, mixed $default = null): mixed
    {
        if (in_array($key, ['first_name', 'last_name', 'email', 'phone'])) {
            return $this->{$key} ?? $default;
        }

        return data_get($this->data, $key, $default);
    }

    public static function createFromData(int $practiceId, array $data, ?LeadSource $source = null, ?string $leadType = null): self
    {
        $validator = Validator::make($data, [
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $lead = new self();
        $lead->practice_id = $practiceId;
        $lead->source = $source ?? LeadSource::API;
        $lead->status = LeadStatus::NEW;

        $lead->first_name = $data['first_name'] ?? $data['firstName'] ?? $data['fname'] ?? null;
        $lead->last_name = $data['last_name'] ?? $data['lastName'] ?? $data['lname'] ?? null;
        $lead->email = $data['email'] ?? $data['email_address'] ?? $data['emailAddress'] ?? null;

        $rawPhone = $data['phone'] ?? $data['phone_number'] ?? $data['phoneNumber'] ?? $data['mobile'] ?? null;
        if ($rawPhone) {
            try {
                $phoneNumber = new PhoneNumber($rawPhone, 'US');
                $lead->phone = $phoneNumber->formatE164();
            } catch (\Exception $e) {
                $lead->phone = $rawPhone;
            }
        } else {
            $lead->phone = null;
        }

        $lead->lead_created_at = $data['created_at'] ?? $data['createdAt'] ?? $data['lead_created_at'] ?? now();
        $lead->lead_type = $leadType ?? $data['lead_type'] ?? $data['leadType'] ?? $data['type'] ?? null;

        $lead->data = $data;
        $lead->save();

        try {
            event(new \App\Events\LeadCreated($lead));
        } catch (\Exception $e) {
            \Log::error('Failed to dispatch LeadCreated event', [
                'lead_id' => $lead->id,
                'error' => $e->getMessage(),
            ]);
        }

        return $lead;
    }
}
