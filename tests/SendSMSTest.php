<?php

use buibr\Budget\BudgetSMS;

class SendSMSTest extends \PHPUnit\Framework\TestCase
{
    protected $budget;
    
    public function setUp()
    {
        $this->budget = new BudgetSMS([
            'username' => 'iwinback',
            'userid'   => '10651',
            'handle'   => '3131db62929367453d93079caaeef9a1',
            'from'     => 'Test New',
            'price'    => 1,
            'mccmnc'   => 1,
            'credit'   => 1,
        ]);
    }
    
    /** @test */
    public function test_set_receiver_missing()
    {
        $this->expectExceptionCode(1005);
        $this->expectExceptionMessage('Receiver number not set.');
        $this->budget->validate(['to']);
    }
    
    /** @test */
    public function test_set_receiver()
    {
        $this->budget->setRecipient('+38971789062');
        $this->assertNull($this->budget->validate());
    }
    
    
    
}