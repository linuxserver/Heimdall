<?php

namespace Tests\Feature;

use App\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\DataProvider;
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

    /** @var string[] */
    private array $tempFiles = [];

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
        $this->seed();
    }

    protected function tearDown(): void
    {
        array_map('unlink', array_filter($this->tempFiles, 'file_exists'));

        parent::tearDown();
    }

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
        $path = $this->tempFiles[] = tempnam(sys_get_temp_dir(), 'icon');
        file_put_contents($path, $contents);

        return new UploadedFile($path, $clientName, null, null, true);
    }

    public static function svgClientNames(): array
    {
        return [
            'svg extension' => ['icon.svg'],
            // Fails if the sanitizer goes by the client file name.
            'png extension' => ['icon.png'],
        ];
    }

    #[DataProvider('svgClientNames')]
    public function test_svg_icon_upload_is_accepted_and_sanitized(string $clientName): void
    {
        $response = $this->post('/items', $this->itemPayload($this->upload(self::SVG, $clientName)));

        $response->assertSessionHasNoErrors();
        $item = Item::where('title', 'Item A')->firstOrFail();
        Storage::disk('public')->assertExists($item->icon);
        $stored = Storage::disk('public')->get($item->icon);
        $this->assertStringContainsString('<circle', $stored);
        $this->assertStringNotContainsString('<script', $stored);
        $this->assertStringNotContainsString('onload', $stored);
    }

    public function test_malformed_svg_upload_is_rejected(): void
    {
        $malformed = '<svg xmlns="http://www.w3.org/2000/svg"><circle r="1">&nbsp;</svg>';

        $response = $this->post('/items', $this->itemPayload($this->upload($malformed, 'icon.svg')));

        $response->assertSessionHasErrors('file');
        $this->assertNull(Item::where('title', 'Item A')->first());
    }

    public function test_non_image_upload_is_rejected(): void
    {
        $response = $this->post('/items', $this->itemPayload($this->upload('just some text', 'icon.txt')));

        $response->assertSessionHasErrors('file');
        $this->assertNull(Item::where('title', 'Item A')->first());
    }
}
