<?php

namespace buibr\Budget;

/** 
 * 
 * Author:      Burhan Ibraimi <burhan@wflux.pro>
 * Company      wFlux
 * 
 * Class:       {classname}
 * Created:     Thu Apr 04 2019 9:24:31 PM
 * 
**/
class BudgetErrors {

    private static $errors = [
        "1001" => "Not enough credits to send messages",
        "1002" => "Identification failed. Wrong credentials",
        "1003" => "Account not active, contact BudgetSMS",
        "1004" => "This IP address is not added to this account. No access to the API",
        "1005" => "No handle provided",
        "1006" => "No UserID provided",
        "1007" => "No Username provided",
        "2001" => "SMS message text is empty",
        "2002" => "SMS numeric senderid can be max. 16 numbers",
        "2003" => "SMS alphanumeric sender can be max. 11 characters",
        "2004" => "SMS senderid is empty or invalid",
        "2005" => "Destination number is too short",
        "2006" => "Destination is not numeric",
        "2007" => "Destination is empty",
        "2008" => "SMS text is not OK (check encoding?)",
        "2009" => "Parameter issue (check all mandatory parameters, encoding, etc.)",
        "2010" => "Destination number is invalidly formatted",
        "2011" => "Destination is invalid",
        "2012" => "SMS message text is too long",
        "2013" => "SMS message is invalid",
        "2014" => "SMS CustomID is used before",
        "2015" => "Charset problem",
        "2016" => "Invalid UTF-8 encoding",
        "2017" => "Invalid SMSid",
        "3001" => "No route to destination. Contact BudgetSMS for possible solutions",
        "3002" => "No routes are setup. Contact BudgetSMS for a route setup",
        "3003" => "Invalid destination. Check international mobile number formatting",
        "4001" => "System error, related to customID",
        "4002" => "System error, temporary issue. Try resubmitting in 2 to 3 minutes",
        "4003" => "System error, temporary issue.",
        "4004" => "System error, temporary issue. Contact BudgetSMS",
        "4005" => "System error, permanent",
        "4006" => "Gateway not reachable",
        "4007" => "System error, contact BudgetSMS",
        "5001" => "Send error, Contact BudgetSMS with the send details",
        "5002" => "Wrong SMS type",
        "5003" => "Wrong operator",
        "6001" => "Unknown error",
        "7001" => "No HLR provider present, Contact BudgetSMS.",
        "7002" => "Unexpected results from HLR provider",
        "7003" => "Bad number format",
        "7901" => "Unexpected error. Contact BudgetSMS",
        "7902" => "HLR provider error. Contact BudgetSMS",
        "7903" => "HLR provider error. Contact BudgetSMS"
    ];

    private static $dlr = [
        "0"	    => "Message is sent, no status yet (default)",
        "1"	    => "Message is delivered",
        "2"	    => "Message is not sent",
        "3"	    => "Message delivery failed",
        "4"	    => "Message is sent",
        "5"	    => "Message expired",
        "6"	    => "Message has an invalid destination address",
        "7"	    => "SMSC error, message could not be processed",
        "8"	    => "Message not allowed",
        "11"	=> "Message status unknown, usually after 24 hours without update SMSC",
        "12"	=> "Message status unknown, SMSC received unknown status code",
        "13"	=> "Message status unknown, no status update received from SMSC after 72 hours",
    ];

    /**
     *  Request errors
     * @return string
     */
    public static function get( $id ) {
        if(\array_key_exists($id, self::$errors)) {
            return self::$errors[$id];
        }

        return $id;
    }

    /**
     * sms status codes.
     * @return string.
     */
    public static function dlr( $id ){

        if(\array_key_exists($id, self::$dlr)) {
            return self::$dlr[$id];
        }

        return $id;
    }

}