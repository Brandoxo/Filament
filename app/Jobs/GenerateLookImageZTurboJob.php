<?php

namespace App\Jobs;

use App\Models\Look;
use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GenerateLookImageZTurboJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 180;
    public int $tries   = 3;

    private const HF_MODEL      = 'fal-ai/z-image/turbo';
    private const GEMINI_MODEL  = 'gemini-2.5-flash';
    private const STORAGE_BASE  = 'https://pub-d6a275def1944cbf9dafb1239d52700b.r2.dev';

    public function __construct(public Look $look) {}

    public function handle(): void
    {
        $this->look->load('products');

        // Step 1 — analyze product images with Gemini Vision
        $visualDescription = $this->describeProductsWithGemini();

        // Step 2 — generate outfit image with Z-Image Turbo
        $pngData = $this->generateWithZTurbo($visualDescription);

        if (! $pngData) {
            return;
        }

        // Step 3 — upload to R2 and save URL
        $publicUrl = $this->uploadToR2($pngData);

        if ($publicUrl) {
            $this->look->update(['image_url' => $publicUrl]);
            Log::info("GenerateLookImageZTurboJob: Image ready for Look #{$this->look->id}", ['url' => $publicUrl]);
        }
    }

    // -------------------------------------------------------------------------
    // Step 1: Gemini Vision — analyze product images → visual description
    // -------------------------------------------------------------------------

    private function describeProductsWithGemini(): string
    {
        $parts = [[
            'text' => 'You are a product cataloguer for a fashion brand. '
                . 'I will show you images of garments that belong to a single styled look. '
                . 'For EACH item, describe it with forensic precision: '
                . 'exact garment type, every visible color and color-blocking detail, print or pattern (name it exactly), fabric texture and weight, '
                . 'collar/neckline style, sleeve length and shape, closure type and hardware (buttons, zippers, buckles — material and finish), '
                . 'pocket placement, hem style, waistband, lining, any visible stitching details, embroidery, patches, logos, or text. '
                . 'Do NOT generalize. Do NOT omit any visible element. '
                . 'Return a single structured paragraph that inventories every item so a designer could recreate each piece exactly from your description alone.',
        ]];

        $hasImages = false;

        foreach ($this->look->products->take(3) as $product) {
            $imageUrl = $this->resolveImageUrl($product);

            if (! $imageUrl) {
                $parts[] = ['text' => "Product: {$product->title}"];
                continue;
            }

            $download = $this->downloadImage($imageUrl);

            if (! $download) {
                $parts[] = ['text' => "Product: {$product->title}"];
                continue;
            }

            $parts[] = ['text' => "Product: {$product->title}"];
            $parts[] = [
                'inlineData' => [
                    'mimeType' => $download['mime'],
                    'data'     => $download['b64'],
                ],
            ];

            $hasImages = true;
        }

        if (! $hasImages) {
            Log::warning("GenerateLookImageZTurboJob: No product images downloaded for Look #{$this->look->id}, using text-only prompt.");
            return $this->fallbackDescription();
        }

        $response = Http::timeout(60)
            ->post(
                'https://generativelanguage.googleapis.com/v1beta/models/' . self::GEMINI_MODEL . ':generateContent?key=' . config('services.gemini.api_key'),
                [
                    'contents'         => [['parts' => $parts]],
                    'generationConfig' => ['maxOutputTokens' => 600],
                ]
            );

        if (! $response->successful()) {
            Log::warning("GenerateLookImageZTurboJob: Gemini Vision failed for Look #{$this->look->id}", [
                'status' => $response->status(),
                'body'   => substr($response->body(), 0, 300),
            ]);
            return $this->fallbackDescription();
        }

        $description = data_get($response->json(), 'candidates.0.content.parts.0.text', '');

        if (empty($description)) {
            return $this->fallbackDescription();
        }

        Log::info("GenerateLookImageZTurboJob: Gemini described Look #{$this->look->id}", ['description' => $description]);

        return $description;
    }

    private function fallbackDescription(): string
    {
        $names = $this->look->products->pluck('title')->take(5)->join(', ');
        return "an outfit consisting of: {$names}";
    }

    // -------------------------------------------------------------------------
    // Step 2: Z-Image Turbo — generate outfit image from visual description
    // -------------------------------------------------------------------------

    private function generateWithZTurbo(string $visualDescription): ?string
    {
        $prompt = "Ghost mannequin product photography, invisible mannequin technique. "
            . "Full-body floating outfit \"{$this->look->title}\" worn by an invisible ghost mannequin — no person, no face, no hands, no visible body. "
            . "Reproduce EXACTLY these garments with every detail intact: {$visualDescription}. same colors, same textures, same patterns, same garment types, same details. "
            . "Every seam, hardware piece, print, logo, stitch, and closure must match the description exactly — no substitutions, no color changes, no creative liberties. "
            . "Pure white seamless studio background, neutral flat lighting that reveals fabric texture, sharp focus across all clothing details, "
            . "high-resolution product photography, photorealistic.";

        // fal-ai provider: uses its own URL path and native request format
        $response = Http::withToken(config('services.huggingface.api_token'))
            ->timeout(120)
            ->post(
                'https://router.huggingface.co/fal-ai/' . self::HF_MODEL,
                [
                    'prompt'               => $prompt,
                    'image_size'           => ['width' => 1024, 'height' => 1024],
                    'num_inference_steps'  => 8,
                    'output_format'        => 'png',
                    'num_images'           => 1,
                ]
            );

        if (! $response->successful()) {
            Log::error("GenerateLookImageZTurboJob: Z-Image Turbo error for Look #{$this->look->id}", [
                'status' => $response->status(),
                'body'   => substr($response->body(), 0, 500),
            ]);
            $this->fail("Z-Image Turbo returned HTTP {$response->status()}");
            return null;
        }

        // fal-ai returns JSON: {"images": [{"url": "https://..."}]}
        $imageUrl = data_get($response->json(), 'images.0.url');

        if (! $imageUrl) {
            Log::error("GenerateLookImageZTurboJob: No image URL in fal-ai response for Look #{$this->look->id}", [
                'body' => substr($response->body(), 0, 300),
            ]);
            return null;
        }

        // Download the image bytes from the temporary fal-ai URL
        $download = Http::timeout(60)->get($imageUrl);

        if (! $download->successful()) {
            Log::error("GenerateLookImageZTurboJob: Could not download generated image for Look #{$this->look->id}", [
                'url' => $imageUrl,
            ]);
            return null;
        }

        return $download->body();
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function resolveImageUrl(Product $product): ?string
    {
        if (! $product->image_url) {
            return null;
        }

        $path = $product->image_url;

        return str_starts_with($path, 'http')
            ? $path
            : rtrim(self::STORAGE_BASE, '/') . '/' . ltrim($path, '/');
    }

    private function downloadImage(string $url): ?array
    {
        try {
            $response = Http::timeout(30)->get($url);

            if (! $response->successful()) {
                return null;
            }

            $mime = match (true) {
                str_contains($response->header('Content-Type'), 'png')  => 'image/png',
                str_contains($response->header('Content-Type'), 'webp') => 'image/webp',
                default                                                  => 'image/jpeg',
            };

            return [
                'b64'  => base64_encode($response->body()),
                'mime' => $mime,
            ];
        } catch (\Exception $e) {
            Log::warning("GenerateLookImageZTurboJob: Could not download image", [
                'url'   => $url,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    private function uploadToR2(string $imageData): ?string
    {
        try {
            $filename = 'looks/look-' . $this->look->id . '-' . Str::random(8) . '.png';

            Storage::disk('r2')->put($filename, $imageData, ['visibility' => 'public']);

            return rtrim(env('STORAGE_PUBLIC_URL'), '/') . '/' . $filename;
        } catch (\Exception $e) {
            Log::error("GenerateLookImageZTurboJob: R2 upload failed", ['error' => $e->getMessage()]);
            return null;
        }
    }
}
