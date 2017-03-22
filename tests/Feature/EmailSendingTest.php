<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class EmailSendingTest extends TestCase
{
    use WithoutMiddleware;
    
    public function setUp()
    {
        parent::setUp();
    }
    
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testEmail()
    {
        $response = $this->post('api/sendMessage', [
            'user' => [
                'name' => 'Joe Doe',
                'email' => 'foo@bar.com'
            ],
            'message' => 'Lorem ipsum'
        ]);
        $response->assertJsonStructure(['message']);
    }
}
