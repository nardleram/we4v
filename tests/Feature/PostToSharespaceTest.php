<?php

namespace Tests\Feature;

use App\Associate;
use App\Post;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PostToSharespaceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        Storage::fake('public');
    }

    /** @test */
    public function a_user_can_post_a_text_post()
    {
        $this->withoutExceptionHandling();
        $this->actingAs($user = factory(User::class)->create(), 'api');

        $response = $this->post('/api/posts', [
            'body' => 'Testing body'
        ]);

        $post = Post::first();
        
        $this->assertCount(1, Post::all());
        $this->assertEquals($user->id, $post->user_id);
        $this->assertEquals('Testing body', $post->body);
        $response->assertStatus(201)
            ->assertJson([
                'data' => [
                    'type' => 'posts',
                    'post_id' => $post->id,
                    'attributes' => [
                        'posted_by' => [
                            'data' => [
                                'attributes' => [
                                    'fname' => $user->fname,
                                    'sname' => $user->sname,
                                    'uname' => $user->uname,
                                ]
                            ]
                        ],
                        'body' => 'Testing body'
                    ]
                ],
                'links' => [
                    'self' => url('/posts/'.$post->id),
                ]
            ]);
    }

    /** @test */
    public function a_user_can_post_a_text_post_with_an_image()
    {
        $this->actingAs($user = factory(User::class)->create(), 'api');

        $file = UploadedFile::fake()->image('user_post.png');

        $response = $this->post('/api/posts', [
            'body' => 'Testing body with image',
            'image' => $file,
            'width' => 800,
            'height' => 800,
        ]);

        dd($response);

        Storage::disk('public')->assertExists('post-images/'.$file->hashName());
        
        $response->assertStatus(201)
            ->assertJson([
                'data' => [
                    'type' => 'posts',
                    'attributes' => [
                        'body' => 'Testing body with image',
                        'image' => url('post-images/'.$file->hashName()),
                    ]
                ]
            ]);
    }

    /** @test */
    public function a_user_can_retrieve_posts()
    {
        $this->actingAs($user = factory(User::class)->create(), 'api');
        $user2 = factory(User::class)->create();
        Associate::create([
            'user_id' => $user->id,
            'associate_id' => $user2->id,
            'status' => 1,
            'confirmed_at' => now(),
        ]);

        $posts = factory(Post::class, 2)->create(['user_id' => $user2->id]);

        $response = $this->get('/api/posts');

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    [
                        'data' => [
                            'type' => 'posts',
                            'post_id' => $posts->last()->id,
                            'attributes' => [
                                'body' => $posts->last()->body,
                                'image' => url($posts->last()->image),
                                'posted_at' => $posts->last()->created_at->diffForHumans(),
                            ]
                        ]
                    ],
                    [
                        'data' => [
                            'type' => 'posts',
                            'post_id' => $posts->first()->id,
                            'attributes' => [
                                'body' => $posts->first()->body,
                                'image' => url($posts->first()->image),
                                'posted_at' => $posts->first()->created_at->diffForHumans(),
                            ]
                        ]
                    ]
                ],
                'links' => [
                    'self' => url('/posts'),
                ]
            ]);
    }

    /** @test */
    public function users_can_only_retrieve_own_posts()
    {
        $this->withoutExceptionHandling();
        $this->actingAs($user = factory(User::class)->create(), 'api');

        $posts = factory(Post::class)->create();

        $response = $this->get('/api/posts');

        $response->assertStatus(200)
            ->assertExactJson([
                'data' => [],
                'links' => [
                    'self' => url('/posts'),
                ]
            ]);
    }
}
