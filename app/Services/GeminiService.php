<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * Thin client around Google's Gemini API used for two jobs:
 *   1. parseReceipt() - vision: turn a photo of a bill into line items.
 *   2. parseSms()     - text:   turn a bank/wallet SMS into a transaction.
 *
 * Both calls ask Gemini to return strict JSON (responseMimeType) and the
 * expected shape is described inline in the prompt.
 */
class GeminiService
{
    private string $apiKey;

    private string $model;

    private string $endpoint;

    public function __construct()
    {
        $this->apiKey = (string) config('services.gemini.key');
        $this->model = (string) config('services.gemini.model', 'gemini-2.5-flash');
        $this->endpoint = rtrim((string) config('services.gemini.endpoint', 'https://generativelanguage.googleapis.com/v1beta'), '/');
    }

    public function isConfigured(): bool
    {
        return $this->apiKey !== '';
    }

    /**
     * Parse a receipt/bill image into structured line items.
     *
     * @param  string  $imagePath  Absolute path to the image file.
     * @return array{receipt_type:string,merchant:?string,purchased_at:?string,total:?float,fuel_liters:?float,fuel_rate:?float,items:array<int,array<string,mixed>>,raw:array}
     */
    public function parseReceipt(string $imagePath, ?string $mimeType = null): array
    {
        if (! is_file($imagePath)) {
            throw new RuntimeException("Receipt image not found: {$imagePath}");
        }

        $mimeType ??= mime_content_type($imagePath) ?: 'image/jpeg';
        $base64 = base64_encode((string) file_get_contents($imagePath));

        $prompt = <<<'PROMPT'
You are a receipt and bill parser for a household expense tracker used in Pakistan.
The currency is always Pakistani Rupee (PKR). Read the attached photo of a receipt,
bill or shop slip (it may be printed or hand-written, in English or Urdu) and extract
its contents.

Return ONLY a JSON object with exactly this shape:
{
  "receipt_type": "grocery | fuel | pharmacy | other",
  "merchant": "shop or station name, or null",
  "purchased_at": "date/time on the receipt as YYYY-MM-DD HH:MM:SS, or null",
  "total": number or null,
  "fuel_liters": number or null,
  "fuel_rate": number or null,
  "items": [
    {
      "name": "item name",
      "quantity": number,
      "unit": "kg | ltr | pcs | dozen | etc, or null",
      "unit_price": number or null,
      "amount": number,
      "suggested_category": "Groceries | Vegetables & Fruit | Meat | Dairy | Snacks | Household | Personal Care | Medicine | Fuel | Other"
    }
  ]
}

Rules:
- If this is a petrol/diesel/CNG/fuel-station receipt, set "receipt_type" to "fuel",
  put liters in "fuel_liters", price-per-liter in "fuel_rate", and return a single
  item named "Fuel" (or the fuel grade) with the total amount.
- All numbers must be plain (no "Rs", no commas, no currency symbols).
- "amount" is the line total in rupees and is required for every item.
- If a field is unknown, use null. Never invent items that are not on the receipt.
- Respond with the JSON object only, no markdown, no commentary.
PROMPT;

        $data = $this->generate([
            ['text' => $prompt],
            ['inline_data' => ['mime_type' => $mimeType, 'data' => $base64]],
        ]);

        $items = [];
        foreach ((array) ($data['items'] ?? []) as $item) {
            if (! is_array($item) || ! isset($item['name'])) {
                continue;
            }
            $items[] = [
                'name' => (string) $item['name'],
                'quantity' => isset($item['quantity']) ? (float) $item['quantity'] : 1.0,
                'unit' => isset($item['unit']) ? (string) $item['unit'] : null,
                'unit_price' => isset($item['unit_price']) ? (float) $item['unit_price'] : null,
                'amount' => isset($item['amount']) ? (float) $item['amount'] : 0.0,
                'suggested_category' => isset($item['suggested_category']) ? (string) $item['suggested_category'] : null,
            ];
        }

        $type = strtolower((string) ($data['receipt_type'] ?? 'other'));
        if (! in_array($type, ['grocery', 'fuel', 'pharmacy', 'other'], true)) {
            $type = 'other';
        }

        return [
            'receipt_type' => $type,
            'merchant' => $this->stringOrNull($data['merchant'] ?? null),
            'purchased_at' => $this->stringOrNull($data['purchased_at'] ?? null),
            'total' => isset($data['total']) ? (float) $data['total'] : null,
            'fuel_liters' => isset($data['fuel_liters']) ? (float) $data['fuel_liters'] : null,
            'fuel_rate' => isset($data['fuel_rate']) ? (float) $data['fuel_rate'] : null,
            'items' => $items,
            'raw' => $data,
        ];
    }

    /**
     * Parse a bank/wallet SMS into a transaction.
     *
     * @return array{is_transaction:bool,amount:?float,direction:?string,merchant:?string,is_fuel:bool,occurred_at:?string,bank:?string,raw:array}
     */
    public function parseSms(string $body, ?string $sender = null): array
    {
        $sender = $sender ?: 'unknown';

        $prompt = <<<PROMPT
You are a bank and mobile-wallet SMS parser for a household expense tracker in Pakistan.
Pakistani banks and wallets (HBL, UBL, MCB, Meezan, Allied, Bank Alfalah, JazzCash,
Easypaisa, SadaPay, NayaPay, etc.) send SMS messages for account activity.

Decide whether the message below represents money SPENT by the account holder
(a debit: card purchase, POS, online payment, fuel, ATM withdrawal, bill payment,
fund transfer out). Ignore OTP codes, promotions/marketing, balance enquiries and
pure informational messages.

Return ONLY a JSON object with exactly this shape:
{
  "is_transaction": true or false,
  "amount": number or null,
  "direction": "debit | credit | null",
  "merchant": "payee / merchant / shop name, or null",
  "is_fuel": true or false,
  "occurred_at": "YYYY-MM-DD HH:MM:SS if a date/time is present, else null",
  "bank": "bank or wallet name if identifiable, else null"
}

Rules:
- "is_transaction" is true ONLY for an actual spending/debit event.
- "amount" is a plain number in PKR (no "Rs", no commas).
- "is_fuel" is true when the merchant is a petrol pump, fuel/filling station or CNG.
- Respond with the JSON object only, no markdown, no commentary.

SMS sender: {$sender}
SMS body:
{$body}
PROMPT;

        $data = $this->generate([['text' => $prompt]]);

        $direction = isset($data['direction']) ? strtolower((string) $data['direction']) : null;
        if (! in_array($direction, ['debit', 'credit'], true)) {
            $direction = null;
        }

        return [
            'is_transaction' => (bool) ($data['is_transaction'] ?? false),
            'amount' => isset($data['amount']) ? (float) $data['amount'] : null,
            'direction' => $direction,
            'merchant' => $this->stringOrNull($data['merchant'] ?? null),
            'is_fuel' => (bool) ($data['is_fuel'] ?? false),
            'occurred_at' => $this->stringOrNull($data['occurred_at'] ?? null),
            'bank' => $this->stringOrNull($data['bank'] ?? null),
            'raw' => $data,
        ];
    }

    /**
     * Send a generateContent request and return the decoded JSON payload.
     *
     * @param  array<int,array<string,mixed>>  $parts
     * @return array<string,mixed>
     */
    private function generate(array $parts): array
    {
        if (! $this->isConfigured()) {
            throw new RuntimeException('GEMINI_API_KEY is not set. Add it to the backend .env file.');
        }

        $url = "{$this->endpoint}/models/{$this->model}:generateContent";

        $response = Http::timeout(90)
            ->withHeaders(['x-goog-api-key' => $this->apiKey])
            ->retry(2, 1500, throw: false)
            ->post($url, [
                'contents' => [
                    ['parts' => $parts],
                ],
                'generationConfig' => [
                    'temperature' => 0,
                    'responseMimeType' => 'application/json',
                ],
            ]);

        if ($response->failed()) {
            Log::error('Gemini request failed', ['status' => $response->status(), 'body' => $response->body()]);
            throw new RuntimeException('Gemini API error ('.$response->status().'): '.$response->body());
        }

        $text = data_get($response->json(), 'candidates.0.content.parts.0.text');

        if (! is_string($text) || trim($text) === '') {
            $reason = data_get($response->json(), 'candidates.0.finishReason', 'unknown');
            throw new RuntimeException("Gemini returned no usable content (finishReason: {$reason}).");
        }

        $decoded = json_decode($this->stripFences($text), true);

        if (! is_array($decoded)) {
            throw new RuntimeException('Gemini did not return valid JSON: '.$text);
        }

        return $decoded;
    }

    /** Remove ```json ... ``` fences in case the model adds them anyway. */
    private function stripFences(string $text): string
    {
        $text = trim($text);
        $text = preg_replace('/^```(?:json)?/i', '', $text);
        $text = preg_replace('/```$/', '', (string) $text);

        return trim((string) $text);
    }

    private function stringOrNull(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }
        $value = trim((string) $value);

        return $value === '' || strtolower($value) === 'null' ? null : $value;
    }
}
