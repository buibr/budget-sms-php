<?php

namespace buibr\Budget;


use buibr\Budget\BudgetSMS;
use buibr\Budget\BudgetErrors;
use buibr\Budget\Exceptions\InvalidResponseException;

/** 
 * 
 * Author:      Burhan Ibraimi <burhan@wflux.pro>
 * Company      wFlux
 * 
 * Class:       BudgetResponse
 * Created:     Thu Apr 04 2019 7:38:03 PM
 * 
**/
class BudgetDlr {

    public $code;
    public $type;
    public $time;
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
     * 
     * @param string $data - response from budget sms
     */
    public function __construct(&$res = null, $obj = null)
    {

        if(empty($res)){
            return $this;
        }

        $data           = $res['content'];
        $curl           = $res['curl'];
        $header_size    = curl_getinfo( $curl, CURLINFO_HEADER_SIZE );
        $header         = substr( $data, 0, $header_size );
        $body           = trim( substr($data, $header_size) );
        
        $this->code = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
        $this->type = curl_getinfo($curl, CURLINFO_CONTENT_TYPE);
        $this->time = curl_getinfo($curl, CURLINFO_TOTAL_TIME);
        $this->data = $body;
        $this->extract($obj);
        
        return $this->status;
    }


    /**
     * Parse response to status and data.
     * 
     * sms reponse format: {STATUS} {TRANSACTION_ID} {CREDIT} {NUMBER CREDIT} {}
     */
    public function extract( BudgetSMS $obj = null )
    {

        if(strpos(trim(strtoupper($this->data)), "OK") > -1){
            //  
            $str    = explode(' ', $this->data);

            //  status of message.
            $this->sms_code     = $str[1];
            
            //  get the message from list
            $this->sms_message = BudgetErrors::dlr($this->sms_code);

            //  status to true.
            $this->status = true;

            return $this;
        }

        throw new InvalidResponseException("Unknow response.", 3001);

    }

    
    /**
     * Convert all parameters to array
     */
    public function toArray()
    {
        $a = [];

        foreach($this as $key=>$val)
        {
            $a[$key] = $val;
        }

        return $a;
    }


}   