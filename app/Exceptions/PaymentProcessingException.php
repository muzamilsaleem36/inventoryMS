<?php

namespace App\Exceptions;

use Exception;

class PaymentProcessingException extends Exception
{
    protected $paymentMethod;
    protected $amount;
    protected $errorCode;
    protected $errorDetails;

    public function __construct($message, $paymentMethod = null, $amount = null, $errorCode = null, $errorDetails = [])
    {
        $this->paymentMethod = $paymentMethod;
        $this->amount = $amount;
        $this->errorCode = $errorCode;
        $this->errorDetails = $errorDetails;

        parent::__construct($message);
    }

    public function getPaymentMethod()
    {
        return $this->paymentMethod;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function getErrorCode()
    {
        return $this->errorCode;
    }

    public function getErrorDetails()
    {
        return $this->errorDetails;
    }

    public function toArray()
    {
        return [
            'message' => $this->getMessage(),
            'payment_method' => $this->paymentMethod,
            'amount' => $this->amount,
            'error_code' => $this->errorCode,
            'error_details' => $this->errorDetails,
        ];
    }
} 