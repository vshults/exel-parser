<?php

namespace Tests\Feature;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ExelUploadTest extends TestCase
{
    public function testFileUploadAndProcessing(): void
    {
        Storage::fake('local');

        $filePath = storage_path('app/temp/test.xlsx');
        $file = UploadedFile::fake()->createWithContent($filePath, file_get_contents($filePath));

        $response = $this->post('/file/upload', [
            'file' => $file,
        ]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'File uploaded and processed successfully']);
    }
}
