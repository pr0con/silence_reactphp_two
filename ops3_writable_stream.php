<?php	
	
	require_once 'vendor/autoload.php';

	use React\Stream\ReadableResourceStream;
	use React\Stream\WritableResourceStream;
	$loop =  React\EventLoop\Factory::create();
	
	//rs = readableStream , ws = writableStream
	$rs = new ReadableResourceStream(fopen('testfile.txt','r'), $loop);
	$ws = new WritableResourceStream(STDOUT, $loop);
	
	
	//PHP Offers streams to access current php process
	//php://stdin, php://stdout, php://stderr
	
	//var_dump($ws->isWritable());
	
	$rs->on('data', function($data) use($ws) {
		$ws->write($data."\n");
	});
	
	$rs->on('end', function() use($ws) {
		$ws->end(); //will remain if we dont call end on writable stream...
	});
	#close() method can be used to flush/discard buffer content then does end() internally
	
	
	//SCENARIO:: writing two stream, one if bottlenecked writing, write will return false, and return a DRAIN event when ready to write more.
	//3rd Parameter of WritableResourceStream(,,maxbufsize) 
	//NOTE a WritableStreams end() has no event that is fired .... listen to the close() function...
	
	$writable2 = new WritableResourceStream(STDOUT, $loop, 1); //only 1 byte buffer
	var_dump($writable2->write("Hello World\n"));
	
	$writable2->on('drain', function() {
		echo "The Stream Drained. \n";
	});
	
	
	$writable2->on('end', function() {
		//this will never happen
		echo "i wont happen \n";
	});
	
	$writable2->on('close', function() {
		echo "closed \n";
	});
	
	$loop->addTimer(1, function() use($writable2) {
		$writable2->end();
	});
	
	
	
	
	
	$loop->run();
?>