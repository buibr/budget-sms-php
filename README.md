# budget-sms-php

<h4>Install with composer</h4>

```terminal
composer requre buibr/budget-sms-php
```

<h4>Usage:</h4> 

Example 1:
```php
$budget = new \buibr\Budget\BudgetSMS( [
    'username'=>'xxx',
    'userid'=> 'xxx',
    'handle'=>'xxx',
]);

//  sender name
$budget->setSender("Test");

//  add recepient
$budget->setRecipient('+38971xxxxxx');

//  add message
$budget->setMessage('Testing the provider');

//  Send the message 
$send = $budget->send();

```

Example 2:
```php
use buibr\Budget\BudgetSMS;

$budget = new BudgetSMS( [
    'username'=>'xxx',
    'userid'=> 'xxx',
    'handle'=>'xxx',
    'from'=>'Test',
    'price'=> 1, // optional
    'mccmnc'=> 1, // optional
    'credit'=> 1, // optional
]);

$send = $budget->send( '+38971xxxxxx', 'message content' );

```


<h4>Response:</h4> 

Success:

```php
buibr\Budget\BudgetResponse Object
(
    [code] => 200
    [type] => text/plain; charset=UTF-8
    [time] => 0.494388
    [status] => 1
    [response] => Array
        (
            [transaction] => 76208843
            [price] => 0.02
            [time] => 1
            [mccmnc] => 29401
            [credit] => 590.5892
        )

    [data] => OK 76208843 0.02 1 29401 590.5892
)
```

Error:


```php
buibr\Budget\BudgetResponse Object
(
    [code] => 200
    [type] => text/plain; charset=UTF-8
    [time] => 0.32309
    [status] => 
    [response] => SMS message text is empty
    [data] => ERR 2001
)
```