<?php

namespace Tests\Feature;

use App\User;
use App\UserImage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UserImagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        Storage::fake('public');
    }

    /** @test */
    public function users_can_upload_images()
    {
        $this->withoutExceptionHandling();
        $this->actingAs($user = factory(User::class)->create(), 'api');

        $file = UploadedFile::fake()->image('user-img.jpg');

        $response = $this->post('/api/user-images', [
            'image' => $file,
            'width' => 850,
            'height' => 400,
            'pagePosition' => 'cover',
        ])->assertStatus(201);

        Storage::disk('public')->assertExists('user-images/'.$file->hashName());
        $userImage = UserImage::first();
        $this->assertEquals('user-images/'.$file->hashName(), $userImage->path);
        $this->assertEquals('850', $userImage->width);
        $this->assertEquals('400', $userImage->height);
        $this->assertEquals('cover', $userImage->pagePosition);
        $this->assertEquals($user->id, $userImage->user_id);
        $response->assertJson([
            'data' => [
                'type' => 'user-image',
                'user_image_id' => $userImage->id,
                'attributes' => [
                    'path' => url($userImage->path),
                    'width' => $userImage->width,
                    'height' => $userImage->height,
                    'pagePosition' => $userImage->pagePosition,
                ]
            ],
            'links' => [
                'self' => url('/users/'.$user->id),
            ]
        ]);
    }

    /** @test */
    public function users_are_returned_with_their_images()
    {
        $this->withoutExceptionHandling();
        $this->actingAs($user = factory(User::class)->create(), 'api');
        $file = UploadedFile::fake()->image('user-img.jpg');
        $this->post('/api/user-images', [
            'image' => $file,
            'width' => 850,
            'height' => 400,
            'pagePosition' => 'cover',
        ])->assertStatus(201);
        $this->post('/api/user-images', [
            'image' => $file,
            'width' => 850,
            'height' => 400,
            'pagePosition' => 'profile',
        ])->assertStatus(201);

        $response = $this->get('/api/users/'.$user->id);

        $response->assertJson([
            'data' => [
                'type' => 'users',
                'user_id' => $user->id,
                'attributes' => [
                    'cover_image' => [
                        'data' => [
                            'type' => 'user-image',
                            'user_image_id' => 1,
                            'attributes' => []
                        ],
                    ],
                    'profile_image' => [
                        'data' => [
                            'type' => 'user-image',
                            'user_image_id' => 2,
                            'attributes' => []
                        ],
                    ],
                ]
            ]
        ]);
    }
}
