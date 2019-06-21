<?php
	#futureTicks are like async await :: https://github.com/seregazhuk/reactphp-book/tree/master/ticks
	
	#promise state:: unfulfilled, fulfilled, failed
	#deferred 2methods:: resolve, reject
	
	
	#Rule:: Either return a promise or call done()
	#then is resumable 
	#done returns null and handles final errors
	
	require_once 'vendor/autoload.php';
	#composer require react/promise:^2.7.1
	
	use React\Promise\Deferred;
	
	$deferred = new Deferred();
	$deferred2 = new Deferred();
	$deferred3 = new Deferred();
	$deferred4 = new Deferred();
	$deferred5 = new Deferred();
	$deferred6 = new Deferred();
	$deferred7 = new Deferred();
	
	
	#1
	$promise = $deferred->promise();
	$promise->done(function($data) {
		echo 'Done: '.$data. PHP_EOL;
	});
	$deferred->resolve('hello world');
	
	
	#2
	$promise2 = $deferred2->promise();
	$promise2->otherwise(function($data){
		echo 'Fail: '.$data. PHP_EOL;
	});
	
	$deferred2->reject('no results');
	
	
	#3
	$promise3 = $deferred3->promise();
	$promise3->done(function($data){
		echo 'Done: '.$data.PHP_EOL;
	},function($data) {
		echo 'Reject: '.$data.PHP_EOL;
	});
	
	$deferred3->reject('hello world');
	
	#4 chained -- most useful
	$deferred4->promise()->then(function($data) {
		$data = $data.' @phase one'.PHP_EOL;
		echo $data;
		return $data;
	})->then(function($data) {
		$data = $data.'->@phase2'.PHP_EOL;
		echo $data;
		return $data;
	})->then(function($data) {
		$data = $data.'->@phase3'.PHP_EOL;
		echo $data;
	});
	
	$deferred4->resolve('hello world');
	
	#5 rejection forwarding :: 
	#NOTE: otherwise(callable, $onRejected) under hood => $promise->then(null, $onRejected);  
	$deferred5->promise()->otherwise(function($data) {
		echo "Error with> ".$data.PHP_EOL;
		throw new Exception('error_phase1> '.$data);	
	})->otherwise(function(\Exception $e) {
		echo $e->getMessage().PHP_EOL; 
		throw new Exception('error phase2> '.$e->getMessage());
	})->otherwise(function(\Exception $e) {
		echo $e->getMessage().PHP_EOL;
	});
	
	$deferred5->reject('badFuckingErrorOne');
	
	
	#6 Type Hint Error Handling
	$deferred6->promise()->otherwise(function($data) {
		echo "Error with> ".$data.PHP_EOL;
		throw new InvalidArgumentException('invalidArgEx With: '.$data);	
	})->otherwise(function(InvalidArgumentException $e) {
		echo $e->getMessage().PHP_EOL; 
		throw new BadFunctionCallException('badFuncCallEx> '.$e->getMessage());
	})->otherwise(function(InvalidArgumentException  $e) {
		//this will be skipped because a logic exception has been thrown above...
		echo $e->getMessage().PHP_EOL;
	});
	
	$deferred6->reject('badFuckingErrorTwo');
	
	#7 Mixed Forwarding
	$deferred7->promise()->then(function($data) {
		echo 'Input: '.$data.PHP_EOL;
		return $data;
	})->then(function($data) {
		throw new Exception('error: '.$data);
	})->otherwise(function(Exception $e) {
		return $e->getMessage();
	})->then(function($data) {
		echo $data.PHP_EOL;	
	});

	$deferred7->resolve('Example 7');

?>