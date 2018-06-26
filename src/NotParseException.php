<?php

namespace FunPay\SmsParser;

class NotParseException extends \RuntimeException
{
    /** @var string */
    private $smsText;

    /** @var string|null */
    private $possibleCode;

    /** @var string|null */
    private $possibleWalletNumber;

    /** @var float|null */
    private $possibleAmount;

    /**
     * NotParseException constructor.
     * @param string $smsText
     * @param string|null $possibleCode
     * @param string|null $possibleWalletNumber
     * @param float|null $possibleAmount
     */
    public function __construct(string $smsText, ?string $possibleCode = null, ?string $possibleWalletNumber = null, ?float $possibleAmount = null)
    {
        $this->smsText = $smsText;
        $this->possibleCode = $possibleCode;
        $this->possibleWalletNumber = $possibleWalletNumber;
        $this->possibleAmount = $possibleAmount;
        parent::__construct('Unable to parse sms. Data: ' . json_encode([
                'smsText' => $this->smsText,
                'possibleCode' => $this->possibleCode,
                '$possibleWalletNumber' => $this->possibleWalletNumber,
                'possibleAmount' => $this->possibleAmount
            ], JSON_UNESCAPED_UNICODE));
    }

    /**
     * @return string
     */
    public function getSmsText(): string
    {
        return $this->smsText;
    }

    /**
     * @return string|null
     */
    public function getPossibleCode(): ?string
    {
        return $this->possibleCode;
    }

    /**
     * @return string|null
     */
    public function getPossibleWalletNumber(): ?string
    {
        return $this->possibleWalletNumber;
    }

    /**
     * @return float|null
     */
    public function getPossibleAmount(): ?float
    {
        return $this->possibleAmount;
    }
}