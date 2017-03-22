<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Mail;
use Tests\EmailTracking;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class EmailSendingTest extends TestCase
{
 //   use EmailTracking;
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
//        Mail::raw('Hello World', function ($message) {
//            $message->from('foo@bar.com');
//            $message->to('bar@foo.com');
//        });
        $response = $this->post('api/sendMessage', [
            'user' => [
                'name' => 'Joe Doe',
                'email' => 'foo@bar.com'
            ],
            'message' => 'Lorem ipsum'
        ]);
        $response->assertJsonStructure(['message']);
//        $this->seeEmailWasSent();
        
    }
}
