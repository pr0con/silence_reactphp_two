<?php
	#Callback and Promises are not fundamentally different, everything a callback does a promise does.
	#promises help remove callback hell
	
	#composer require react/cache:^0.5.0
	require_once 'vendor/autoload.php';
	
	#1
	use React\Cache\CacheInterface;
	
	abstract class Example implements CacheInterface {
		private $data = ['example1','example2'];	
			
		public function get($key, $default = NULL) {
			if(!isset($this->$data[$key])) {
				//optional reject reason::
				$exception = new Exception("Value with key $key not found");
				return Promise\reject();
			}
			
			return Promise\resolve($this->data[$key]);
		}
	}
	
	
	#2
	use React\Promise\Deferred;
	
	$deferred = new Deferred();
	$deferred->resolve('Some Deferred Error');
	
	$promise = React\Promise\reject($deferred->promise());
	$promise->then(null, function($reason) {
		echo 'Example promise was rejected with '.PHP_EOL.$reason.PHP_EOL;
	});
	
	
	#3 Array of Promises	
	$resolverOne = new Deferred();
	$resolverTwo = new Deferred();
	
	$pending = [
		$resolverOne->promise(),
		$resolverTwo->promise()
	];
	
	$promiseArray = React\Promise\all($pending)->then(function($resolved) {
		print_r($resolved);
	});
	
	$resolverOne->resolve(10);
	$resolverTwo->resolve(20);
	
	
	
	
	$loop = React\EventLoop\Factory::create();
	#4 Array of Promises	
	$resolverThree = new Deferred();
	$resolverFour = new Deferred();
	
	$pending2 = [
		$resolverThree->promise(),
		$resolverFour->promise()
	];
	
	$promiseArray = React\Promise\race($pending2)->then(function($resolved) {
		echo 'Resolved Winner: '.$resolved.PHP_EOL;
	}, function($reason) {
		echo 'Failed With: '.$reason.PHP_EOL;
	});
	
	
	$loop->addTimer(2, function() use($resolverThree) {
		$resolverThree->resolve("I Loose");
	});
	$loop->addTimer(1, function() use($resolverFour) {
		$resolverFour->resolve("I Win");
		/* $resolverFour->reject("I Won race but failed occured"); */
	});
	
	
	#5 Any seems almost just like race... ?
	$resolverFive = new Deferred();
	$resolverSix = new Deferred();
	
	$pending3 = [
		$resolverFive->promise(),
		$resolverSix->promise()
	];
	
	$promiseAny = React\Promise\any($pending3)->then(function($resolved) {
		echo 'Resolved Winner: '.$resolved.PHP_EOL;
	});
	

	$loop->addTimer(2, function() use($resolverFive) {
		$resolverFive->resolve("I Loose");
	});
	$loop->addTimer(1, function() use($resolverSix) {
		$resolverSix->resolve("I Win");
		/* $resolverFour->reject("I Won race but failed occured"); */
	});	
	
	
	
	#6 Continue after a certain number completed
	$resolverOne_ = new Deferred();
	$resolverTwo_ = new Deferred();
	$resolverThree_ = new Deferred();
	
	$pending4 = [
		$resolverOne_->promise(),
		$resolverTwo_->promise(),
		$resolverThree_->promise()
	];
	
	$promiseSome = React\Promise\some($pending4, 2)->then(function($resolved) {
		echo 'Resolved'.PHP_EOL;
		print_r($resolved);
	}, function($errors) {
		echo 'Failed'.PHP_EOL;
		print_r($errors);
	});
	
	$loop->addTimer(2, function() use($resolverOne_) {
		$resolverOne_->resolve("Hello");
	});
	$loop->addTimer(1, function() use($resolverTwo_) {
		$resolverTwo_->resolve("World");
		/* $resolverFour->reject("I Won race but failed occured"); */
	});	
	
	$resolverThree_->resolve(37);
	
	$loop->run();
	
	
?>