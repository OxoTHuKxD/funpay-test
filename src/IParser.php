<?php

namespace FunPay\SmsParser;

interface IParser
{
    /**
     * @param string $smsText
     * @return ResultDTO
     * @throws NotParseException
     */
    public function parse(string $smsText): ResultDTO;
}