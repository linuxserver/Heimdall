<?php

namespace Tests\Feature;

use App\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Icons uploaded on the add/edit item form. SVGs are accepted and sanitized
 * before they are stored.
 */
class ItemIconUploadTest extends TestCase
{
    use RefreshDatabase;

    private const SVG = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 10 10">'
        . '<script>alert(1)</script>'
        . '<circle cx="5" cy="5" r="4" onload="alert(2)"/></svg>';

    private function itemPayload(UploadedFile $file): array
    {
        return [
            'pinned' => 1,
            'appid' => 'null',
            'website' => null,
            'title' => 'Item A',
            'colour' => '#00f',
            'url' => 'http://10.0.1.1',
            'tags' => [0],
            'file' => $file,
        ];
    }

    /**
     * A real file on disk, so the MIME type is detected from its contents
     * rather than taken from the client file name.
     */
    private function upload(string $contents, string $clientName): UploadedFile
    {
        $path = tempnam(sys_get_temp_dir(), 'icon');
        file_put_contents($path, $contents);

        return new UploadedFile($path, $clientName, null, null, true);
    }

    private function storedIcon(): string
    {
        $item = Item::where('title', 'Item A')->firstOrFail();
        Storage::disk('public')->assertExists($item->icon);

        return Storage::disk('public')->get($item->icon);
    }

    public function test_svg_icon_upload_is_accepted_and_sanitized(): void
    {
        Storage::fake('public');
        $this->seed();

        $response = $this->post('/items', $this->itemPayload($this->upload(self::SVG, 'icon.svg')));

        $response->assertSessionHasNoErrors();
        $stored = $this->storedIcon();
        $this->assertStringContainsString('<circle', $stored);
        $this->assertStringNotContainsString('<script', $stored);
        $this->assertStringNotContainsString('onload', $stored);
    }

    public function test_svg_uploaded_under_another_extension_is_still_sanitized(): void
    {
        Storage::fake('public');
        $this->seed();

        $response = $this->post('/items', $this->itemPayload($this->upload(self::SVG, 'icon.png')));

        $response->assertSessionHasNoErrors();
        $stored = $this->storedIcon();
        $this->assertStringNotContainsString('<script', $stored);
        $this->assertStringNotContainsString('onload', $stored);
    }

    public function test_non_image_upload_is_rejected(): void
    {
        Storage::fake('public');
        $this->seed();

        $response = $this->post('/items', $this->itemPayload($this->upload('just some text', 'icon.txt')));

        $response->assertSessionHasErrors('file');
        $this->assertNull(Item::where('title', 'Item A')->first());
    }
}
