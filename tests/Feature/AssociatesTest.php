<?php

namespace Tests\Feature;

use App\Associate;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssociatesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function users_can_send_associate_requests()
    {
        $this->actingAs($user1 = factory(User::class)->create(), 'api');
        $user2 = factory(User::class)->create();

        $response = $this->post('/api/associate-request', [
            'associate_id' => $user2->id,
            'user_id' => $user1->id,
        ])->assertStatus(200);

        $associateRequest = Associate::first();

        $this->assertNotNull($associateRequest);
        $this->assertEquals($user2->id, $associateRequest->associate_id);
        $this->assertEquals($user1->id, $associateRequest->user_id);
        $response->assertJson([
            'data' => [
                'type' => 'associate-request',
                'associate_request_id' => $associateRequest->id,
                'attributes' => [
                    'confirmed_at' => null,
                ]
            ],
            'links' => [
                'self' => url('/users/'.$user2->id),
            ]
        ]);
    }

    /** @test */
    public function users_may_send_only_one_associate_request()
    {
        $this->actingAs($user1 = factory(User::class)->create(), 'api');
        $user2 = factory(User::class)->create();

        $this->post('/api/associate-request', [
            'associate_id' => $user2->id,
            'user_id' => $user1->id,
        ])->assertStatus(200);
        $this->post('/api/associate-request', [
            'associate_id' => $user2->id,
            'user_id' => $user1->id,
        ])->assertStatus(200);

        $associateRequests = Associate::all();

        $this->assertCount(1, $associateRequests);
    }

    /** @test */
    public function only_valid_users_may_be_associated()
    {
        // $this->withoutExceptionHandling();
        $this->actingAs($user = factory(User::class)->create(), 'api');

        $response = $this->post('/api/associate-request', [
            'associate_id' => 22,
        ])->assertStatus(422);

        $this->assertNull(Associate::first());
        $response->assertJson([
            'errors' => [
                'code' => 422,
                'title' => 'User not found',
                'detail' => 'The user you sent an associate request does not exist in our database.'
            ]
        ]);
    }

    /** @test */
    public function associate_requests_can_be_accepted()
    {
        $this->actingAs($user1 = factory(User::class)->create(), 'api');
        $user2 = factory(User::class)->create();

        $this->post('/api/associate-request', [
            'associate_id' => $user2->id,
            'user_id' => $user1->id,
        ])->assertStatus(200);

        $response = $this->actingAs($user2, 'api')
            ->post('/api/associate-request-response', [
                'user_id' => $user1->id,
                'status' => 1,
            ])->assertStatus(200);

        $associateRequest = Associate::first();
        $this->assertNotNull($associateRequest->confirmed_at);
        $this->assertInstanceOf(Carbon::class, $associateRequest->confirmed_at);
        $this->assertEquals(now()->startOfSecond(), $associateRequest->confirmed_at);
        $this->assertEquals(1, $associateRequest->status);
        $response->assertJson([
            'data' => [
                'type' => 'associate-request',
                'associate_request_id' => $associateRequest->id,
                'attributes' => [
                    'confirmed_at' => $associateRequest->confirmed_at->diffForHumans(),
                    'status' => $associateRequest->status,
                    'associate_id' => $associateRequest->associate_id,
                    'user_id' => $associateRequest->user_id,
                ]
            ],
            'links' => [
                'self' => url('/users/'.$user2->id),
            ]
        ]);

    }

     /** @test */
     public function associate_requests_can_be_ignored()
     {
        $this->withoutExceptionHandling(); 
        $this->actingAs($user1 = factory(User::class)->create(), 'api');
         $user2 = factory(User::class)->create();
 
         $this->post('/api/associate-request', [
             'associate_id' => $user2->id,
             'user_id' => $user1->id,
         ])->assertStatus(200);
 
         $response = $this->actingAs($user2, 'api')
             ->delete('/api/associate-request-response/delete', [
                 'user_id' => $user1->id,
             ])->assertStatus(204);
 
         $associateRequest = Associate::first();
         $this->assertNull($associateRequest);
         $response->assertNoContent();
 
     }

    /** @test */
    public function only_permit_valid_associate_requests()
    {
        // $this->withoutExceptionHandling();

        $user2 = factory(User::class)->create();

        $response = $this->actingAs($user2, 'api')
            ->post('/api/associate-request-response', [
                'user_id' => 2211,
                'status' => 1,
            ])->assertStatus(404);
        
        $this->assertNull(Associate::first());
        $response->assertJson([
            'errors' => [
                'code' => 404,
                'title' => 'Associate request not found/permitted',
                'detail' => 'Submitted associate request not permitted / not found in our database.'
            ]
        ]);
    }

    /** @test */
    public function only_intended_recipient_may_confirm_request()
    {
        $this->actingAs($user1 = factory(User::class)->create(), 'api');
        $user2 = factory(User::class)->create();

        $this->post('/api/associate-request', [
            'associate_id' => $user2->id,
            'user_id' => $user1->id,
        ])->assertStatus(200);

        $response = $this->actingAs(factory(User::class)->create(), 'api')
            ->post('/api/associate-request-response', [
                'user_id' => $user1->id,
                'status' => 1,
            ])->assertStatus(404);
        
        $associateRequest = Associate::first();
        $this->assertNull($associateRequest->confirmed_at);
        $this->assertNull($associateRequest->status);
        $response->assertJson([
            'errors' => [
                'code' => 404,
                'title' => 'Associate request not found/permitted',
                'detail' => 'Submitted associate request not permitted / not found in our database.'
            ]
        ]);
    }

    /** @test */
    public function only_intended_recipient_may_ignore_request()
    {
        $this->actingAs($user1 = factory(User::class)->create(), 'api');
        $user2 = factory(User::class)->create();

        $this->post('/api/associate-request', [
            'associate_id' => $user2->id,
            'user_id' => $user1->id,
        ])->assertStatus(200);

        $response = $this->actingAs(factory(User::class)->create(), 'api')
            ->delete('/api/associate-request-response/delete', [
                'user_id' => $user1->id,
            ])->assertStatus(404);
        
        $associateRequest = Associate::first();
        $this->assertNull($associateRequest->confirmed_at);
        $this->assertNull($associateRequest->status);
        $response->assertJson([
            'errors' => [
                'code' => 404,
                'title' => 'Associate request not found/permitted',
                'detail' => 'Submitted associate request not permitted / not found in our database.'
            ]
        ]);
    }

    /** @test */
    public function associate_request_requires_associate_id() {
        $response = $this->actingAs($user1 = factory(User::class)->create(), 'api')
            ->post('/api/associate-request', [
                'associate_id' => '',
                'user_id' => $user1->id,
            ])->assertStatus(422);
        
        $responseString = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('associate_id', $responseString['errors']['meta']);
    }

    /** @test */
    public function associate_request_response_requires_user_id_and_status() {
        $response = $this->actingAs($user1 = factory(User::class)->create(), 'api')
            ->post('/api/associate-request-response', [
                'user_id' => '',
                'status' => '',
            ])->assertStatus(422);
        
        $responseString = json_decode($response->getContent(), true);
        
        $this->assertArrayHasKey('user_id', $responseString['errors']['meta']);
        $this->assertArrayHasKey('status', $responseString['errors']['meta']);
    }

    /** @test */
    public function ignoring_associate_request_response_requires_user_id() {
        $response = $this->actingAs($user1 = factory(User::class)->create(), 'api')
            ->delete('/api/associate-request-response/delete', [
                'user_id' => '',
            ])->assertStatus(422);
        
        $responseString = json_decode($response->getContent(), true);
        
        $this->assertArrayHasKey('user_id', $responseString['errors']['meta']);
    }

    /** @test */
    public function assocation_is_retrieved_when_fetching_profile()
    {
        $this->actingAs($user = factory(User::class)->create(), 'api');
        $user2 = factory(User::class)->create();
        $associateRequest = Associate::create([
            'user_id' => $user->id,
            'associate_id' => $user2->id,
            'status' => 1,
            'confirmed_at' => now()->subDay(),
        ]);

        $this->get('/api/users/'.$user2->id)
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    'attributes' => [
                        'association' => [
                            'data' => [
                                'associate_request_id' => $associateRequest->id,
                                'attributes' => [
                                    'confirmed_at' => '1 day ago',
                                ]
                            ]
                        ],
                    ]
                ]
            ]);
    }

    /** @test */
    public function inverse_assocation_is_retrieved_when_fetching_profile()
    {
        $this->actingAs($user = factory(User::class)->create(), 'api');
        $user2 = factory(User::class)->create();
        $associateRequest = Associate::create([
            'associate_id' => $user->id,
            'user_id' => $user2->id,
            'status' => 1,
            'confirmed_at' => now()->subDay(),
        ]);

        $this->get('/api/users/'.$user2->id)
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    'attributes' => [
                        'association' => [
                            'data' => [
                                'associate_request_id' => $associateRequest->id,
                                'attributes' => [
                                    'confirmed_at' => '1 day ago',
                                ]
                            ]
                        ],
                    ]
                ]
            ]);
    }
}
