<?php

namespace buibr\Budget;


use buibr\Budget\Exceptions\InvalidResponseException;

/**
 * Author:      Burhan Ibraimi <mail@buib.me>
 *
 * Class:       BudgetDlr
 * Created:     Thu Apr 04 2019 7:38:03 PM
 *
 **/
class BudgetDlr
{
    /** @var */
    public $code;
    
    /** @var */
    public $type;
    
    /** @var */
    public $time;
    
    /** @var */
    public $status;
    
    /**
     * Sms id of sended message.
     */
    public $smsid;
    
    /**
     * Phone number the sms sent to.
     */
    public $to;
    
    /**
     * Response code from budget.
     */
    public $sms_code;
    
    /**
     * Message based on the code in response
     */
    public $sms_message;
    
    /**
     * Timestamp date when the even has happend.
     */
    public $date;
    
    /**
     * BudgetDlr constructor.
     *
     * @param null $res - response from Budget SMS Request.
     * @param null $obj
     *
     * @throws \buibr\Budget\Exceptions\InvalidResponseException
     */
    public function __construct(&$res = NULL, $obj = NULL)
    {
        if ($res && $res['content'] && $res['curl']) {
            
            $header_size = curl_getinfo($res['curl'], CURLINFO_HEADER_SIZE);
            
            $this->code = curl_getinfo($res['curl'], CURLINFO_RESPONSE_CODE);
            $this->type = curl_getinfo($res['curl'], CURLINFO_CONTENT_TYPE);
            $this->time = curl_getinfo($res['curl'], CURLINFO_TOTAL_TIME);
            $this->extract($obj, trim(substr($res['content'], $header_size)));
        }
    }
    
    /**
     * Parse response to status and data.
     *
     * sms reponse format: {STATUS} {TRANSACTION_ID} {CREDIT} {NUMBER CREDIT} {}
     */
    public function extract(BudgetSMS $obj = NULL, $data = '')
    {
        if (strpos(trim(strtoupper($data)), "OK") > -1) {
            //  
            $str = explode(' ', $data);
            
            //  status of message.
            $this->sms_code = $str[1];
            
            //  get the message from list
            $this->sms_message = BudgetErrors::dlr($this->sms_code);
            
            //  status to true.
            $this->status = TRUE;
            
            return $this;
        }
        
        throw new InvalidResponseException("Unknow response.", 3001);
    }
    
    /**
     * Return full response as array
     * @return array
     */
    public function toArray()
    {
        $a = [];
        foreach ($this as $key => $val) {
            $a[$key] = $val;
        }
        return $a;
    }
    
    
}   