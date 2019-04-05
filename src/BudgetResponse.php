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
class BudgetResponse {

    public $code;
    public $type;
    public $time;
    public $status;
    public $response;

    /**
     * 
     * @param string $data - response from budget sms
     */
    public function __construct($data, $curl = null, $obj = null)
    {

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

        if(strpos($this->data, "OK:") > -1){
            //  
            $str    = explode(':', $this->data);

            //  first caracters devine the status.
            $status = array_shift($str);

            //  
            if(strtoupper(trim($status)) === 'OK' ) {
                $this->response = [
                    'transaction'   => null,
                    'mccmnc'        => $str[0],
                    'operator'      => $str[1],
                    'price'         => $str[2],
                ];

                return $this->status = true;
            }
            elseif(strtoupper(trim($status)) === 'ERR' ) {
                $this->response = [
                    'error_code' => $str[0],
                    'error_message' => BudgetErrors::get(trim($str[0])),
                ];
                return  $this->status = false;
            }
            else {
                return $this->status = false;
            }
        }
        else {
            //  
            $str    = explode(' ', $this->data);

            //  first caracters devine the status.
            $status = array_shift($str);
            
            //  
            if(strtoupper(trim($status)) === 'OK' ) {
                
                //  
                $this->response = [
                    'transaction'   => $str[0],
                    'price'         => $obj->price ? $str[1] : null,
                    'time'          => $obj->price ? $str[2] : null, // number after sms means time
                    'mccmnc'        => $obj->mccmnc ? $obj->price ?  $str[3] : $str[1] : null,
                    'credit'        => $obj->credit ? $obj->price ? $obj->mccmnc ? $str[4]: $str[3] : $str[1] : null,
                ];
                
                //  
                return $this->status = true;

            }
            elseif(strtoupper(trim($status)) === 'ERR' ) {
                $this->status = false;
                $this->response = [
                    'error_code' => $str[0],
                    'error_message' => BudgetErrors::get(trim($str[0])),
                ];
                return false;
            }
            else {
                $this->status = false;
                return false;
            }
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