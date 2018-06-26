<?php

namespace FunPay\SmsParser;

class Parser implements IParser
{
    /**
     * @param string $smsText
     * @return ResultDTO
     * @throws NotParseException
     */
    public function parse(string $smsText): ResultDTO
    {
        preg_match_all("(((\d+)([,\.]\d+)?)([а-яА-ЯёЁa-zA-Z\.]+)?)", $smsText, $matches);
        $mainMatches = $matches[0];
        $secondMatches = $matches[1];
        $thirdMatches = $matches[2];

        if (count($mainMatches) !== 3) {
            throw new NotParseException($smsText);
        }

        $code = null;
        $walletNumber = null;
        $amount = null;

        $currentResult = $this->tryGetAmount($mainMatches, $secondMatches, $thirdMatches);
        if ($currentResult) {
            $amount = array_values($currentResult)[0];
            unset($mainMatches[array_keys($currentResult)[0]]);
        }

        $currentResult = $this->tryGetCode($mainMatches);
        if ($currentResult) {
            $code = array_values($currentResult)[0];
            unset($mainMatches[array_keys($currentResult)[0]]);
        }

        $currentResult = $this->tryGetWalletNumber($mainMatches);
        if ($currentResult) {
            $walletNumber = array_values($currentResult)[0];
            unset($mainMatches[array_keys($currentResult)[0]]);
        }

        if (!$this->checkPreFinallyResult($code, $walletNumber, $amount)) {
            throw new NotParseException($smsText, $code, $walletNumber, $amount);
        }

        if (!empty($mainMatches)) {
            if (is_null($code)) {
                $code = array_pop($mainMatches);
            } elseif (is_null($amount)) {
                $amount = array_pop($mainMatches);
            } else {
                $walletNumber = array_pop($mainMatches);
            }
        }

        if (!$this->checkFinallyResult($code, $walletNumber, $amount)) {
            throw new NotParseException($smsText, $code, $walletNumber, $amount);
        }

        return new ResultDTO($code, $walletNumber, $amount);
    }

    /**
     * @param string $amount
     * @return float
     */
    private function prepareAmount(string $amount): float
    {
        return (float)str_replace(',', '.', $amount);
    }

    /**
     * @param string $possibleAmount
     * @param string $secondMatchesValue
     * @param string $thirdMatchesValue
     * @return bool
     */
    private function checkIsAmount(string $possibleAmount, string $secondMatchesValue, string $thirdMatchesValue): bool
    {
        return $possibleAmount !== $secondMatchesValue || $possibleAmount !== $thirdMatchesValue;
    }

    /**
     * @param string $possibleCode
     * @return bool
     */
    private function checkIsCode(string $possibleCode): bool
    {
        return mb_strlen($possibleCode) === 4;
    }

    /**
     * @param string $possibleWalletNumber
     * @return bool
     */
    private function checkIsWalletNumber(string $possibleWalletNumber): bool
    {
        return mb_strlen($possibleWalletNumber) >= 11;
    }

    /**
     * @param string|null $code
     * @param string|null $walletNumber
     * @param float|null $amount
     * @return bool
     */
    private function checkPreFinallyResult(?string $code, ?string $walletNumber, ?float $amount): bool
    {
        $countNotFoundValue = 0;
        if (is_null($code)) {
            $countNotFoundValue++;
        }
        if (is_null($walletNumber)) {
            $countNotFoundValue++;
        }
        if (is_null($amount)) {
            $countNotFoundValue++;
        }

        return $countNotFoundValue < 2;
    }

    /**
     * @param string|null $code
     * @param string|null $walletNumber
     * @param float|null $amount
     * @return bool
     */
    private function checkFinallyResult(?string $code, ?string $walletNumber, ?float $amount): bool
    {
        return !is_null($code)
            && !is_null($walletNumber)
            && !is_null($amount)
            && mb_strlen($code) < 11
            && ($amount != (int)$amount || mb_strlen($amount) < 11 && mb_strlen($code) !== mb_strlen($amount));
    }

    /**
     * @param array $mainMatches
     * @param array $secondMatches
     * @param array $thirdMatches
     * @return array|null
     */
    private function tryGetAmount(array $mainMatches, array $secondMatches, array $thirdMatches): ?array
    {
        foreach ($mainMatches as $key => $val) {
            if ($this->checkIsAmount($val, $secondMatches[$key], $thirdMatches[$key])) {
                return [$key => $this->prepareAmount($secondMatches[$key])];
            }
        }

        return null;
    }

    /**
     * @param array $mainMatches
     * @return array|null
     */
    private function tryGetCode(array $mainMatches): ?array
    {
        foreach ($mainMatches as $key => $val) {
            if ($this->checkIsCode($val)) {
                return [$key => $val];
            }
        }

        return null;
    }

    /**
     * @param array $mainMatches
     * @return array|null
     */
    private function tryGetWalletNumber(array $mainMatches): ?array
    {
        foreach ($mainMatches as $key => $val) {
            if ($this->checkIsWalletNumber($val)) {
                return [$key => $val];
            }
        }

        return null;
    }
}