<?php

namespace App\Jobs;

use App\Models\Practice;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeneratePracticeContentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Practice $practiceData;
    protected $callbackUrl;

    /**
     * Create a new job instance.
     */
    public function __construct(Practice $practiceData, string|null $callbackUrl)
    {
        $practiceData->loadMissing('location');
        $this->practiceData = $practiceData;
        $this->callbackUrl = $callbackUrl;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Log::debug('calling job');
            $prompt = $this->buildPrompt();
            $generatedContent = $this->callGeminiApi($prompt);

            // Store the generated content or trigger callback
            $this->handleResult($generatedContent);

        } catch (Exception $e) {
            Log::error('Gemini API Content Generation Failed', [
                'practice_data' => $this->practiceData,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->handleFailure($e);
            throw $e;
        }
    }

    /**
     * Build the prompt for Gemini API
     */
    private function buildPrompt(): string
    {
        $practiceName = $this->practiceData->name;
        $city = $this->practiceData->location->locality;
        $state = $this->practiceData->location->administrative_area_level_1;
        $isEvexipelProvider = true;

        return "Create SEO-friendly HTML content for a medical practice website.

**Practice Details:**
- Practice Name: {$practiceName}
- Location: {$city}, {$state}
- Evexipel Provider: " . ($isEvexipelProvider ? 'Yes' : 'No') . "

**Requirements:**
1. Generate clean HTML content using only semantic tags (h1, h2, h3, p, ul, li, strong)
2. No divs, classes, or styling - just content structure
3. Make it SEO-friendly with proper keyword placement for hormone therapy in {$city}, {$state}
4. Use the exact same format and structure as the example provided
5. DO NOT change the symptoms and causes lists - keep them exactly as shown below
6. You can rephrase the surrounding text and descriptions but maintain the professional medical tone
7. You can make the content location specific for the practice. Maybe talk about things people struggle with in that location.

**REQUIRED Symptoms of Hormonal Imbalance in Women (keep exactly as listed):**
- Low Energy & Fatigue and a persistent Decreased Drive or Motivation
- Cognitive issues like Brain Fog, Lack of Focus, or Memory Loss
- Physical changes, including Increased Body Fat, Unexplained Weight Gain, or Trouble Losing Weight, and Loss of Muscle Mass or Strength
- Emotional distress like Anxiety, Depression, Mood Swings, or Irritability
- Loss of intimacy, marked by Low Libido or Sex Drive and Vaginal Dryness or Discomfort during Intercourse
- Disruptions like Irregular, Painful, or Absent Periods, Trouble Sleeping or Insomnia, and uncomfortable Hot Flashes or Night Sweats
- Aesthetic concerns such as Hair Loss or Thinning Hair and Acne or Skin Texture Changes

**REQUIRED Causes of Hormonal Imbalance in Women (keep exactly as listed):**
- Natural transitions like Aging, Perimenopause, and Menopause
- Surgical factors, including Hysterectomy
- Medical conditions such as PCOS and Thyroid Dysfunction
- Modern life stressors, including Metabolic Syndrome and chronic Stress & Adrenal Dysfunction (indicated by High or Low Cortisol Levels)
- Lifestyle factors like Poor Diet & Nutrient Deficiencies

**REQUIRED Symptoms of Hormonal Imbalance in Men (keep exactly as listed):**
- Physical energy loss, including Low Energy and Fatigue and a Decreased Drive or Motivation
- Cognitive issues such as Brain Fog, Lack of Focus or Memory Loss
- Unwanted body changes: Increased Body Fat, Unexplained Weight Gain or Trouble Losing Weight, and Loss of Muscle Mass or Strength
- Emotional instability, including Anxiety, Depression, Mood Swings or Irritability
- Sexual health concerns: Low Libido or Sex Drive and Erectile Dysfunction
- Other disruptive symptoms: Trouble Sleeping or Insomnia, Hot Flashes or Night Sweats, Hair Loss or Thinning Hair, and Acne or Skin Texture Changes

**REQUIRED Causes of Hormonal Imbalance in Men (keep exactly as listed):**
- The natural aging process, often referred to as Andropause
- Clinically Low Testosterone levels
- Related endocrine issues like Thyroid Dysfunction
- Metabolic conditions such as Metabolic Syndrome
- The effects of prolonged stress, including Stress and Adrenal Dysfunction and imbalances in High or Low Cortisol Levels
- Lifestyle deficiencies, specifically Poor Diet and Nutrient Deficiencies

**Structure the content with:**
1. H1 with practice name
2. Introduction paragraph about the practice
3. Section on Hormonal Imbalance and BHRT for Women
4. Subsection on Women's Symptoms (use the exact list above)
5. Subsection on Women's Causes (use the exact list above)
6. Subsection on Testing & Optimization Therapy for Women
7. Subsection on Peptide Therapy and Nutraceuticals for Women
8. Section on Men's Health and Hormone Optimization
9. Subsection on Men's Symptoms and Causes (use the exact lists above)
10. Subsection on Testing & Optimization Therapy for Men
11. Subsection on Peptide Therapy and Nutraceuticals for Men
12. Call-to-action section for scheduling consultation

**SEO Keywords to incorporate naturally:**
- Hormone Replacement Therapy {$city}
- BHRT {$city}, {$state}
- Bioidentical Hormone Therapy {$city}
- Testosterone Therapy {$city}
- Hormone Optimization {$city}
- Low T Treatment {$city}
- Menopause Treatment {$city}
- Perimenopause Treatment {$city}

Return only the HTML content, no explanations or additional text.";
    }


    private function callGeminiApi(string $prompt): string
    {
        $apiKey = config('services.gemini.api_key');

        if (!$apiKey) {
            throw new Exception('Gemini API key not configured');
        }

        $model = 'gemini-2.5-flash';
        $apiUrl = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent";

        // Calculate input tokens (rough estimation: ~4 characters per token)
        $inputTokens = ceil(strlen($prompt) / 4);

        $requestData = [
            'contents' => [
                ['parts' => [['text' => $prompt]]]
            ],
            'generationConfig' => [
                'temperature' => 0.7,
                'maxOutputTokens' => 8000,
                'topK' => 40,
                'topP' => 0.95,
            ],
            'safetySettings' => [
                [
                    'category' => 'HARM_CATEGORY_HARASSMENT',
                    'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                ],
                [
                    'category' => 'HARM_CATEGORY_HATE_SPEECH',
                    'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                ],
                [
                    'category' => 'HARM_CATEGORY_SEXUALLY_EXPLICIT',
                    'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                ],
                [
                    'category' => 'HARM_CATEGORY_DANGEROUS_CONTENT',
                    'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                ]
            ]
        ];

        $startTime = microtime(true);

        $response = Http::timeout(120)
            ->withHeaders(['Content-Type' => 'application/json'])
            ->post($apiUrl . '?key=' . $apiKey, $requestData);

        $responseTime = microtime(true) - $startTime;

        if (!$response->successful()) {
            Log::error('Gemini API request failed', [
                'status' => $response->status(),
                'response' => $response->body(),
                'model' => $model,
                'url' => $apiUrl,
                'input_tokens_estimated' => $inputTokens,
                'response_time_seconds' => round($responseTime, 3)
            ]);
            throw new Exception('Gemini API request failed: ' . $response->body());
        }

        $data = $response->json();

        if (!isset($data['candidates'][0]['content']['parts'][0]['text'])) {
            Log::error('Invalid Gemini API response format', ['response' => $data]);
            throw new Exception('Invalid response format from Gemini API');
        }

        $generatedContent = $data['candidates'][0]['content']['parts'][0]['text'];


        return $generatedContent;
    }


    /**
     * Handle successful result
     */
    private function handleResult(string $generatedContent): void
    {
        // Store in database or file system
        $this->storeGeneratedContent($generatedContent);

        // Trigger callback if provided
        if ($this->callbackUrl) {
            $this->triggerCallback($generatedContent, 'success');
        }

    }

    /**
     * Handle failure
     */
    private function handleFailure(Exception $e): void
    {
        if ($this->callbackUrl) {
            $this->triggerCallback(null, 'error', $e->getMessage());
        }
    }

    /**
     * Store generated content (customize based on your needs)
     */
    private function storeGeneratedContent(string $content): void
    {


        // Option 2: Store in file system
        $filename = 'content_' . $this->practiceData->getKey() . '.html';

        \Storage::disk('local')->put('generated_content/' . $filename, $content);


    }

    /**
     * Trigger callback webhook
     */
    private function triggerCallback(?string $content, string $status, string $errorMessage = null): void
    {
        try {
            Http::timeout(30)->post($this->callbackUrl, [
                'practice_data' => $this->practiceData,
                'status' => $status,
                'content' => $content,
                'error_message' => $errorMessage,
                'generated_at' => now()->toISOString()
            ]);
        } catch (Exception $e) {
            Log::error('Callback failed', [
                'callback_url' => $this->callbackUrl,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * The number of times the job may be attempted.
     */
    public $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     */
    public $backoff = [60, 300, 900]; // 1 min, 5 min, 15 min

    /**
     * Determine the time at which the job should timeout.
     */
    public function retryUntil()
    {
        return now()->addMinutes(30);
    }
}
