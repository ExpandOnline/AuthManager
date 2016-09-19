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
		$operationError = property_exists($e->detail, 'AdApiFaultDetail')
			? $e->detail->AdApiFaultDetail->Errors->AdApiError
			: $e->detail->ApiFaultDetail->OperationErrors->OperationError;
		return new SoapFaultException($operationError->ErrorCode . ' - ' . $operationError->Message);
	}

}