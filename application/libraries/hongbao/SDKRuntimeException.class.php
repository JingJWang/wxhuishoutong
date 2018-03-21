<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 异常
 */
class  SDKRuntimeException extends Exception {
    
    /**
     * @description 异常
     */
	public function errorMessage(){
		return $this->getMessage();
	}

}

?>