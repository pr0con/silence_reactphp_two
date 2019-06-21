<?php
	#Streams @low-level are an event-emitter
	#3 Interfaces
	#-ReadableStreamInterface
	#-WritableStreamInterface
	#-DuplexStreamInterface
	#Every Stream implements EventEmitterInterface
	#Read Only streams implemented by ReadableStreamInterface
	
	//composer require react/stream:^1.1.0
	
	require_once 'vendor/autoload.php';
	
	use React\Stream\ReadableResourceStream;

	$loop =  React\EventLoop\Factory::create();
	
	
	//use to monitor a log file for example...
	$stream = new ReadableResourceStream(fopen('testfile.txt','r'), $loop);
	$stream->on('data', function($data) {
		//process data line by line
	});

	$stream->on('close', function() {
		echo 'Stream Closed!';
	});
	
	$stream->on('end', function() {
		echo 'Stream Terminated Successfully!';
	});
	
	
	//Stream w/ spooling
	$spool = "";
	$stream2 = new ReadableResourceStream(fopen('testfile.txt','r'), $loop);
	
	var_dump($stream2->isReadable());
	
	$stream2->on('data', function($data) use(&$spool) {
		//readable true
		$spool .= $data;
	});
	
	$stream2->on('end', function() use (&$spool) {
		//readable true
		echo $spool;
	});
	
	$stream2->on('close', function() use ($stream2) {
		var_dump($stream2->isReadable());
	});
	
	//Stream w/ pause
	$readChunkSize = 1;
	$stream3 = new ReadableResourceStream(fopen('testfile.txt','r'), $loop, $readChunkSize ); //NOTICE 1 for 1 byte per second
	$stream3->on('data', function($data) use ($stream3, $loop) {
		echo $data, "\n";
		$stream3->pause();
		$loop->addTimer(1, function() use ($stream3) {
			$stream3->resume();
		});
	});
	
	$loop->run();
?>