<?php

class ErrorHandleClass
{
	// error handle
	public function customErrorHandle($errorNo, $errorMessage, $errorFile, $errorLine){
		echo "<p>Error Message: [$errorNo] $errorMessage</p>";
		echo "<p>Error on line: $errorLine in $errorFile</p>";
	}

}