<?php

namespace App\Exceptions;

use Exception;

class InsufficientStockException extends Exception
{
    protected $product;
    protected $requestedQuantity;
    protected $availableQuantity;

    public function __construct($product, $requestedQuantity, $availableQuantity = null, $message = null)
    {
        $this->product = $product;
        $this->requestedQuantity = $requestedQuantity;
        $this->availableQuantity = $availableQuantity ?? $product->stock_quantity;

        if (!$message) {
            $message = "Insufficient stock for product '{$product->name}'. Requested: {$requestedQuantity}, Available: {$this->availableQuantity}";
        }

        parent::__construct($message);
    }

    public function getProduct()
    {
        return $this->product;
    }

    public function getRequestedQuantity()
    {
        return $this->requestedQuantity;
    }

    public function getAvailableQuantity()
    {
        return $this->availableQuantity;
    }

    public function getShortfall()
    {
        return $this->requestedQuantity - $this->availableQuantity;
    }
} 