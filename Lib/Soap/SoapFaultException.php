<?php

/**
 * Class SoapFaultException
 */
class SoapFaultException extends Exception {

	/**
	 * @param SoapFault $e
	 *
	 * @return SoapFaultException
	 */
	public static function createBySoapFault(SoapFault $e) {
		$operationError = $e->detail->ApiFaultDetail->OperationErrors->OperationError;
		return new SoapFaultException($operationError->ErrorCode . ' - ' . $operationError->Message);
	}

}