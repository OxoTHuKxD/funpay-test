<?php

namespace FunPay\SmsParser;


class ResultDTO
{
    /** @var string */
    private $code;

    /** @var string */
    private $walletNumber;

    /** @var float */
    private $amount;

    /**
     * ResultDTO constructor.
     * @param string $code
     * @param string $walletNumber
     * @param float $amount
     */
    public function __construct(string $code, string $walletNumber, float $amount)
    {
        $this->code = $code;
        $this->walletNumber = $walletNumber;
        $this->amount = $amount;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getWalletNumber(): string
    {
        return $this->walletNumber;
    }

    /**
     * @return float
     */
    public function getAmount(): float
    {
        return $this->amount;
    }
}