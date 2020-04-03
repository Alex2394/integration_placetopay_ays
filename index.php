<?php

require_once "vendor/dnetix/redirection/examples/bootstrap.php";

class placeToPayTest{

	private $login;
	private $secret_key;
	private $nonce;
	private $seed;
	private $tran_key;
	private $object = [];
	private $fields;
	private $placetopay;
	private $response;
	private $requestId;

	public function __construct(){
		
		$this->login = 'de56580d4fa21063c88e47df43775f6e';
		$this->secret_key = 'AlJ0lN94VmGZkeiR';
		$this->tran_key = 'AlJ0lN94VmGZkeiR';

		$this->placetopay = new Dnetix\Redirection\PlacetoPay([
		    'login'					=> $this->login,
		    'tranKey' 				=> $this->tran_key,
		    'url' 					=> 'https://test.placetopay.com/redirection/',
		    'rest' 					=> [
		        'timeout' 			=> 45, // (optional) 15 by default
		        'connect_timeout' 	=> 30, // (optional) 5 by default
		    ]
		]);

		if(isset($_GET['reference'])){
			$this->verifyPay();
		}
		else if(isset($_GET['cancelacion'])){
			$this->cancelPay();
		}
		else{
			$this->processPay();
		}
	}

	public function processPay(){
		// Creating a random reference for the test
		$reference = 'TEST_' . time();

		// Request Information
		$request = [
			"locale" => "es_CO",
		    "payer" => [
		        'name' => 'Alexander',
		        'surname' => 'Molina Cardozo',
		        'email' => 'alexander.molina@naturasoftware.com',
		        'documentType' => 'CC',
		        'document' => '1070615182',
		        'mobile' => '3133879594',
		        'address' => [
		            'street' => 'Cra. 13a # 34-57',
		            'city' => 'Bogota',
		            'state' => 'D.C',
		            'postalCode' => '110111',
		            'country' => 'CO',
		            'phone' => '4882435'
		        ]
		    ],
		    'buyer' => [
		        'name' => 'Alexander',
		        'surname' => 'Molina Cardozo',
		        'email' => 'alexander.molina@naturasoftware.com',
		        'documentType' => 'CC',
		        'document' => '1070615182',
		        'mobile' => '3133879594',
		        'address' => [
		            'street' => 'Cra. 13a # 34-57',
		            'city' => 'Bogota',
		            'state' => 'D.C',
		            'postalCode' => '110111',
		            'country' => 'CO',
		            'phone' => '4882435'
		        ]
		    ],
		    'payment' => [
        		'reference' => $reference,
        		'description' => 'Testing payment',
        		'amount' => [
		            'currency' => 'COP',
		            'total' => 100000,
        		],
    		],
		    'expiration' => date('c', strtotime('+1 hour')),
		    'ipAddress' => '127.0.0.1',
		    'userAgent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/59.0.3071.86 Safari/537.36',
		    'returnUrl' => 'http://localhost/place-to-pay-test/index.php?reference=' . $reference,
		    'cancelUrl' => 'http://localhost/place-to-pay-test/index.php?cancelacion'
		];

		try {
		    //$placetopay = placetopay();
		    $this->response = $this->placetopay->request($request);

		    if ($this->response->isSuccessful()) {
		    	$this->requestId = $this->response->requestId();
		        //Redirect the client to the processUrl or display it on the JS extension
		        header('Location: ' . $this->response->processUrl());
		    } else {
		        // There was some error so check the message
		        var_dump($this->response->status()->message());
		    }
		    //var_dump($this->response);
		} catch (Exception $e) {
		    var_dump($e->getMessage());
		}

	}

	public function verifyPay(){
		
		$this->response = $this->placetopay->query($this->requestId);

		if ($this->response->isSuccessful()) {
		    // In order to use the functions please refer to the Dnetix\Redirection\Message\RedirectInformation class

		    if ($this->response->status()->isApproved()) {
		        echo "Fue aprobada la transaccion";
		    }
		} else {
		    // There was some error with the connection so check the message
		    print_r($this->response->status()->message() . "\n");
		}
	
	}

	public function cancelPay(){

		echo "Se cancelo el pago";
	
	}
}

$obj = new placeToPayTest();

?>