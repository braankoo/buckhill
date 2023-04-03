<?php

namespace Tests\Feature;

use App\Models\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileTest extends Base
{
    /**
     * A basic feature test example.
     */
    public function test_upload_regular_user(): void
    {
        $response = $this->httpRequestWithToken(
            $this->getRegularUser()
        )->post(
            route('file.upload'),
            [
                'file' => UploadedFile::fake()->image('test.png'),
            ]
        )
            ->assertStatus(200);
        $response = json_decode($response->getContent(), true);
        \Storage::assertExists($response['data']['path']);
    }

    public function test_upload_admin_user(): void
    {
        $response = $this->httpRequestWithToken(
            $this->getAdminUser()
        )->post(
            route('file.upload'),
            [
                'file' => UploadedFile::fake()->image('test.png'),
            ]
        );
        $response->assertStatus(401);
    }

    public function test_get_image()
    {
        $image = UploadedFile::fake()->image('test.jpg');
        $a = Storage::putFileAs('/pet-shop', $image, 'test.jpg');
        $uuid = Str::uuid();
        $file = File::factory()->create(
            [
                'uuid' => $uuid,
                'name' => $image->getFilename(),
                'path' => $a,
                'type' => $image->getMimeType(),
            ]
        );
        $this->assertTrue(Storage::disk('local')->exists('pet-shop/test.jpg'));
        $response = $this->get(route('file.show', ['file' => $file->uuid]));
        $response->assertHeader('Content-Type', 'image/jpeg');
    }
}
