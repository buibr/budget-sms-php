<?php

use buibr\Budget\BudgetSMS;

class SendSMSTest extends \PHPUnit\Framework\TestCase
{
    protected $budget;
    
    public function setUp()
    {
        $this->budget = new BudgetSMS([
            'username' => 'buibr',
            'userid'   => '21806',
            'handle'   => 'a55071c51f8b705cf20cc13ee2e80a97',
            'from'     => 'Test New',
            'price'    => 1,
            'mccmnc'   => 1,
            'credit'   => 1,
        ]);
    }
    
    /** @test */
    public function test_validate_receiver_missing()
    {
        $this->expectExceptionCode(1005);
        $this->expectExceptionMessage('Receiver number not set.');
        $this->budget->validate(['to']);
    }
    
    /** @test */
    public function test_validate_receiver_set()
    {
        $this->budget->setRecipient('+38971789062');
        $this->assertNull($this->budget->validate());
    }
}