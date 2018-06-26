<?php

require "../vendor/autoload.php";

$smsText = $argv[1] ?? null;
if (!$smsText) {
    echo 'Не передан текст смс!';
    exit(1);
}

$parser = new \FunPay\SmsParser\Parser();
try {
    $result = $parser->parse($smsText);
    echo "Код подтверждения - {$result->getCode()}\nСумма - {$result->getAmount()}\nКошелек - {$result->getWalletNumber()}\n";
} catch (\FunPay\SmsParser\NotParseException $e) {
    echo "Не удалось получить точный результат!\nВозможные варианты:\n Код подтверждения - {$e->getPossibleCode()}\n Сумма - {$e->getPossibleAmount()}\n Кошелек - {$e->getPossibleWalletNumber()}\nИсходный текст СМС - {$e->getSmsText()}";
    exit(1);
}