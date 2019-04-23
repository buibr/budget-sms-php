<?php

namespace buibr\Budget;

use buibr\Budget\BudgetDlr;
use buibr\Budget\BudgetRequest;
use buibr\Budget\BudgetResponse;
use buibr\Budget\Exceptions\InvalidConfigurationException;
use buibr\Budget\Exceptions\InvalidRequestException;


/** 
 * 
 * Class:       MizuSms
 * Author:      Burhan Ibraimi <burhan@wflux.pro>
 * Company      wFlux
 * 
 * Created:     Tue Apr 02 2019 12:27:02 PM
 *
 * @docs:       https://www.mizu-voip.com/Portals/0/Files/VoIP_Server_API.pdf
 * 
**/
class BudgetSMS {


    /**
     * Server endpoint url for the 
     */
    private $server = 'api.budgetsms.net';

    /**
     * Description: BudgetSMS username
     * Format:      Alphanumeric
     * Required:    Yes	
     * Example:     user1
     */
    private $username;

    /**
     * Description: BudgetSMS userid
     * Format:      Numeric
     * Required:    Yes	
     * Example:     21547
     */
    private $userid;

    /**
     * Description: BudgetSMS handle
     * Format:      Alphanumeric
     * Required:    Yes	
     * Example:     1e753da74
     */
    private $handle;

    /**
     * Description: SMS senderid
     * Format:      Alphanumeric or Numeric
     * Required:    Yes	
     * Example:     BudgetSMS
     */
    private $from;

    /**
     * Description: SMS message receiver msisdn
     * Format:      Numeric
     * Required:    Yes	
     * Example:     31612345678
     */
    private $to;

     /**
     * Description: SMS message content
     * Format:      Alphanumeric
     * Required:    Yes	
     * Example:     "The quick brown fox jumps over the lazy dog"
     */
    private $message;
    
    /**
     * Description: SMS message receiver msisdn
     * Format:      Alphanumeric
     * Required:    No	
     * Example:     1324513254
     */
    private $customid; 
    
    /**
     * Description: Get price information of the submitted message in the response
     * Format:      1 or 0	
     * Required:    No
     * Example:     1
     */
    private $price; 
    
    /**
     * Description: Get country and operator information of the submitted message in the response
     * Format:      1 or 0
     * Required:    No	
     * Example:     1
     */
    private $mccmnc; 
    
    /**
     * Description: Get account credit in the response
     * Format:      1 or 0
     * Required:    No	
     * Example:     1
     */
    private $credit; 


    public function __construct( array $attr = [] )
    {
        if( empty($attr) ) {
            return $attr;
        }

        foreach($attr as $key=>$val) {
            if(\property_exists($this, $key)) {
                $this->$key = $val;
            }
        }

    }

    public function __set($attr, $val)
    {
        if(\property_exists($this, $attr)) {
            
            if($attr === 'from')
                $this->setSender($val);
            
            elseif($attr === 'message')
                $this->setMessage($val);
            
            elseif($attr === 'to')
                $this->setRecipient($val);
            
            else
                $this->$attr = $val;
        }
    }

    public function __get( $key )
    {
        return $this->$key;
    }

    /**
     *  Validate the SMS Sending by Mizu
     */
    public function validate()
    {

        if(empty($this->server)) {
            throw new InvalidConfigurationException("Server not defined", 1001);
        }

        if(empty($this->username)) {
            throw new InvalidConfigurationException("Username missing.", 1002);
        }

        if(empty($this->userid)) {
            throw new InvalidConfigurationException("User ID missing.", 1003);
        }

        if(empty($this->handle)) {
            throw new InvalidConfigurationException("Handler not set.", 1004);
        }

    }

    /**
     *  Sender of the message.
     * @param string $sender - maximum 11 characters.
     */
    public function setSender( $sender )
    {
        return $this->from = substr($sender,0,11);
    }

    /**
     * Set the body of the sms message.
     * @param string $message - The message to send.
     * @param boolean $raw - true = \rawurlencode, false \urlencode
     */
    public function setMessage( $message, $raw = null )
    {
        if(is_null($raw)) {
            return $this->message = $message;
        }

        if ($raw) {
            return $this->message = \rawurlencode($message);
        }

        return $this->message = \urlencode($message);
    }

    /**
     *  Filters for number should be put here.
     * @param string $number - the receiver number.
     */
    public function setRecipient( $number )
    {
        if(empty($this->to) && empty($number)) {
            throw new InvalidConfigurationException("Receiver number not set.", 1002);
        }

        //   remove any space
        $number = trim($number);

        //  remove 00 on the begining.
        $number = ltrim($number, '00');

        //  remove +
        $number = ltrim($number, '+');

        //  
        $this->to = $number;
    }

    /**
     * Convert all parameters to array
     */
    public function toArray()
    {
        $a = [];

        $a['username']  = $this->username;
        $a['userid']    = $this->userid;
        $a['handle']    = $this->handle;

        if( !empty($this->message) ) {
            $a['msg']       = $this->message;
        }
        if( !empty($this->from) ) {
            $a['from']      = $this->from;
        }
        if( !empty($this->to) ) {
            $a['to']        = $this->to;
        }
        if(!empty($this->customid)) {
            $a['customid']  = $this->customid;
        }
        if(!empty($this->price)) {
            $a['price']     = $this->price;
        }
        if(!empty($this->mccmnc)) {
            $a['mccmnc']    = $this->mccmnc;
        }
        if(!empty($this->credit)) {
            $a['credit']    = $this->credit;
        }

        return $a;
    }

    /**
     * Send sms to a number.
     * 
     * @param string|int $receiver - Number that will receive the sms.
     * @param string $message - The message to send.
     * @param boolean $raw - encode to raw or url or no encode (true = \rawurlencode, false = \urlencode, null = no changes)
     * 
     * @return BudgetResponse
     * @throws ErrorException
     */
    public function send( $receiver = null, $message = null, $raw = null )
    {
        if(!empty($receiver)) {
            $this->setRecipient( $receiver );
        }

        if(!empty($message)) {
            $this->setMessage( $message , $raw);
        }

        $this->validate();

        try
        {
            
            $req = new BudgetRequest;
            $req->setUrl( "https://{$this->server}/sendsms/" );
            $req->setParams( $this->toArray() );
            $res = $req->request();
            
            return new BudgetResponse($res['content'], $res['curl'], $this);

        }
        catch( \ErrorException $e)
        {
            throw $e;
        }
    }

    /**
     * Send sms to a number.
     * 
     * @return BudgetResponse
     * @throws ErrorException
     */
    public function balance( )
    {

        $this->validate();

        try
        {
            
            $req = new BudgetRequest;
            $req->setUrl( "https://{$this->server}/checkcredit/" );
            $req->setParams( $this->toArray() );

            $res = $req->request();
            
            return new BudgetResponse($res['content'], $res['curl']);

        }
        catch( \ErrorException $e)
        {
            return false;
        }
    }

    /**
     * Send sms to a number.
     * 
     * @return BudgetResponse
     * @throws ErrorException
     */
    public function operator( $receiver = null)
    {
        if(!empty($receiver)) {
            $this->setRecipient( $receiver );
        }

        $this->validate();

        try
        {
            
            $req = new BudgetRequest;
            $req->setUrl( "https://{$this->server}/checkoperator/" );
            $req->setParams( \array_merge(['check'=>$this->to], $this->toArray()) );
            $res = $req->request();
            
            return new BudgetResponse($res['content'], $res['curl'], $this);

        }
        catch( \ErrorException $e)
        {
            throw $e;
        }

    }


     /**
     * Pull DLR 
     * https://www.budgetsms.net/sms-http-api/pull-dlr/
     * 
     * It is possible to fetch the DLR status for a single SMS message, sent through the HTTP API. 
     * Although fetching DLR statuses through this call is possible, the preferred method is making use of Push DLR.
     * 
     * @return BudgetDlr
     * @throws ErrorException
     */
    public function pullDLR( $sms_id ){

        if(empty($sms_id)) {
            throw new InvalidConfigurationException("'smsid' must be set ");
        }

        $this->validate();

        try
        {
            
            $req = new BudgetRequest;
            $req->setUrl( "https://{$this->server}/checksms/" );
            $req->setParams( \array_merge(['smsid'=>$sms_id], $this->toArray()) );
            $data = $req->request();
            
            $res = new BudgetDlr($data, $this);
            $res->smsid = $sms_id;
            return $res;

        }
        catch( \ErrorException $e)
        {
            throw $e;
        }
    }


    /**
     * Push DLR
     * https://www.budgetsms.net/sms-http-api/push-dlr/
     * 
     * With Push DLR you will receive DLR updates for SMS messages sent through your account. 
     * There is no request you can perform, but it is actually a request made by BudgetSMS to your server.
     * Each time BudgetSMS receives an update for a sent message, we check if the user has setup Push DLR. 
     * If it is setup by the user, we will then forward this DLR notification to your server.
     * 
     * 
     * This will fetch automaticaly the status from push and use as you wish.
     */
    public function pushDLR( $param = null ){

        $res = new BudgetDlr;

        if( empty($param) ) {
            throw new InvalidConfigurationException('Invalid sumbit data.');
        }
        
        if( !isset($param['id']) ) {
            throw new InvalidRequestException('Missing parameter smsid.');
        }
        
        if( !isset($param['status']) ) {
            throw new InvalidRequestException('Unknown sms status.');
        }

        $res->smsid         = strip_tags(trim(stripcslashes($param['id'])));
        $res->sms_code      = strip_tags(trim(stripcslashes($param['status'])));
        $res->to            = strip_tags(trim(stripcslashes($param['to'])));
        $res->date          = strip_tags(trim(stripcslashes($param['date'])));
        $res->sms_message   = BudgetErrors::dlr( $res->sms_code );

        return $res;
    }

}