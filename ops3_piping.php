<?php
	#https://github.com/seregazhuk/reactphp-book/tree/master/streams
	
	require_once 'vendor/autoload.php';

	use React\Stream\ReadableResourceStream;
	use React\Stream\WritableResourceStream;
	$loop =  React\EventLoop\Factory::create();
	
	//rs = readableStream , ws = writableStream
	$rs = new ReadableResourceStream(fopen('testfile.txt','r'), $loop, 1);
	$ws = new WritableResourceStream(STDOUT, $loop);

	//pipe example 1
	$rs->pipe($ws);
	
	#NOTE: pipe returns a writable stream so we can chain duplex read/writes
	//$source->pipe($dosomething)->pipe($dest);
	#pipe calls end by default
	//$source->pipe($dest, ['end' => false]) to turn off
	
	
	//handle most errors
	$ws->on('error', function(Exception $e) {
		echo 'Error: '.$e->getMessage(). PHP_EOL;
	});
	$rs->on('end', function() use($ws) {
		$ws->end(); //will remain if we dont call end on writable stream...
	});
	
	//DUPLEX STREAMS :: NETWORK SOCKET AND FS in RW mode
	use React\Stream\DuplexResourceStream;
	
	$conn = stream_socket_client("tcp://google.com:80");
	$stream = new DuplexResourceStream($conn, $loop);
	
	$stream->write("hello!");
	$stream->end();
	
	
	
	//THROUGH STREAM :: Kinda like middle-ware
	use React\Stream\ThroughStream;
	
	$through = new ThroughStream('strtoupper');
	$rs->pipe($through)->pipe($ws);
	
	
	//COMPOSITE STREAM :: combine a read stream and write stream into one.
	use React\Stream\CompositeStream;
	
	$stdin = new \React\Stream\ReadableResourceStream(STDIN, $loop);
	$stdout = new \React\Stream\WritableResourceStream(STDOUT, $loop);
	$composite = new \React\Stream\CompositeStream($stdin, $stdout);
	
	$composite->on('data', function ($chunk) use ($composite) {
	    $composite->write('You said: ' . $chunk);
	});
		

	
	$loop->run();
?>