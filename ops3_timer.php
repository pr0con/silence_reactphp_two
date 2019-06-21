<?php
	require_once 'vendor/autoload.php';
	
	use React\EventLoop\TimerInterface;
	
	$loop = React\EventLoop\Factory::create();
	$counter = 0;
	
	
	//use isTimerActive(TimerInterface $timer)   :: to check if timer is active.
	
	//Periodic Timer 
	$loop->addPeriodicTimer(2, function() use(&$counter) {
		$counter++;
		echo "$counter\n";
	});
	
	//Periodic Timer with Detached and cacel function
	$counter2 = 0;
	$loop->addPeriodicTimer(2, function(TimerInterface $timer) use(&$counter2, $loop) {
		$counter2++;
		echo "$counter2\n";
		
		if($counter2 == 5) {
			$loop->cancelTimer($timer);
		}
	});
	
	
	//Single Timer
	$loop->addTimer(2, function() {
		echo "Hello World from single timer.\n";
	});
	
	
	/**** Interactive Timers ******/
	$counter3 = 0;
	$periodicTimer2 = $loop->addPeriodicTimer(2, function() use (&$counter3, $loop) {
		$counter3++;
		echo "$counter3\n";
	});
	
	
	//detach above timer from event loop after five senconds...
	$loop->addTimer(5, function() use($periodicTimer2, $loop) {
		$loop->cancelTimer($periodicTimer2);
	});
	//------------------------------/
	
	
	//Blocking operations that stop all timers...
	//sleep(10) for instance... 
		
	$loop->run();
?>