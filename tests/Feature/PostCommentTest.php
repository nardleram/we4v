<?php

namespace Tests\Feature;

use App\Comment;
use App\Post;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostCommentTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function users_can_comment_on_posts()
    {
        
        $this->actingAs($user = factory(User::class)->create(), 'api');
        $post = factory(Post::class)->create(['id' => 1122]);

        $response = $this->post('/api/posts/'.$post->id.'/comment', [
            'body' => 'Behold my comment and be amazed! Its might outshines the post it illuminates with its presence like the sun outshines the moon! For I am Solomon, singer of songs, quaker of mountains, fenn walker extraordinaire!'
        ])
            ->assertStatus(200);

        $comment = Comment::first();
        $this->assertCount(1, Comment::all());
        $this->assertEquals($user->id, $comment->user_id);
        $this->assertEquals($comment->post_id, $post->id);
        $this->assertEquals('Behold my comment and be amazed! Its might outshines the post it illuminates with its presence like the sun outshines the moon! For I am Solomon, singer of songs, quaker of mountains, fenn walker extraordinaire!', $comment->body);
        $response->assertJson([
            'data' => [
                [
                    'data' => [
                        'type' => 'comments',
                        'comment_id' => 1,
                        'attributes' => [
                            'commented_by' => [
                                'data' => [
                                    'user_id' => $user->id,
                                    'attributes' => [
                                        'uname' => $user->uname,
                                    ]
                                ]
                            ],
                            'body' => 'Behold my comment and be amazed! Its might outshines the post it illuminates with its presence like the sun outshines the moon! For I am Solomon, singer of songs, quaker of mountains, fenn walker extraordinaire!',
                            'commented_at' => $comment->created_at->diffForHumans(),
                        ]
                    ],
                    'links' => [
                        'self' => url('/posts/1122'),
                    ]
                ]
            ],
            'links' => [
                'self' => url('/posts'),
            ]
        ]);

    }

    /** @test */
    public function comments_require_body_content()
    {
        $this->actingAs($user = factory(User::class)->create(), 'api');
        $post = factory(Post::class)->create(['id' => 1122]);

        $response = $this->post('/api/posts/'.$post->id.'/comment', [
            'body' => ''
        ])->assertStatus(422);
        
        $responseString = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('body', $responseString['errors']['meta']);
    }

    /** @test */
    public function posts_are_returned_with_comments()
    {
        $this->actingAs($user = factory(User::class)->create(), 'api');
        $post = factory(Post::class)->create(['id' => 1122, 'user_id' => $user->id]);
        $this->post('/api/posts/'.$post->id.'/comment', [
            'body' => 'My man is gone now, there is no longer any point listening for his tired footsteps climbing up the stairs...'
        ]);

        $response = $this->get('/api/posts');

        $comment = Comment::first();

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    [
                        'data' => [
                            'type' => 'posts',
                            'attributes' => [
                                'comments' => [
                                    'data' => [
                                        [
                                            'data' => [
                                                'type' => 'comments',
                                                'comment_id' => 1,
                                                'attributes' => [
                                                    'commented_by' => [
                                                        'data' => [
                                                            'user_id' => $user->id,
                                                            'attributes' => [
                                                                'uname' => $user->uname,
                                                            ]
                                                        ]
                                                    ],
                                                    'body' => 'My man is gone now, there is no longer any point listening for his tired footsteps climbing up the stairs...',
                                                    'commented_at' => $comment->created_at->diffForHumans(),
                                                ]
                                            ],
                                            'links' => [
                                                'self' => url('/posts/1122'),
                                            ]
                                        ]
                                    ],
                                    'comment_count' => 1,
                                ]
                            ]
                        ]
                    ]
                ]
            ]);
    }
}
