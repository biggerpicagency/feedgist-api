<?php
/**
 * Created by PhpStorm.
 * User: BP1
 * Date: 2017-03-22
 * Time: 14:29
 */

namespace Tests;

use Illuminate\Support\Facades\Mail;

trait EmailTracking
{
    protected $emails = [];
    
    /** @before */
    public function setUpMailTracking()
    {
        Mail::getSwiftMailer()
            ->registerPlugin(new TestingEmailEventListener($this));
    }
    
    protected function seeEmailWasSent()
    {
        $this->assertNotEmpty(
            $this->emails
        );
    }
    
    public function addEmail(\Swift_Message $email)
    {
        $this->emails[] = $email;
    }
}

class TestingEmailEventListener implements \Swift_Events_EventListener
{
    protected $test;
    
    public function __construct($test)
    {
        $this->test = $test;
    }
    
    public function beforeSendPerformed($event)
    {
        $this->test->addEmail($event->getMessage());
    }
}