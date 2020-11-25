<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetAuthUserTest extends TestCase
{
    use RefreshDatabase;
    
    /** @test */
    public function authenticated_user_can_be_fetched()
    {
        $this->actingAs($user = factory(User::class)->create(), 'api');

        $response = $this->get('/api/auth-user');

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'user_id' => $user->id,
                    'attributes' => [
                        'uname' => $user->uname,
                        'fname' => $user->fname,
                        'sname' => $user->sname,
                    ]
                ],
                'links' => [
                    'self' => url('/users/'.$user->id)
                ]
            ]);
    }


}
