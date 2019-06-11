<?php


namespace Builders;


use Entities\Transaction;

class TransactionBuilder
{
    public static function buildEntity(array $data): Transaction
    {
        $transaction = new Transaction;
        $transaction->setId($data['id']);

        if (array_key_exists('id_other', $data)) {
            $transaction->setIdOther($data['id_other']);
        }

        $transaction->setDateTransaction($data['date_transaction']);
        $transaction->setDateValue($data['date_value']);
        $transaction->setType($data['type']);
        $transaction->setSender($data['sender']);

        if (array_key_exists('bic', $data)) {
            $transaction->setBic($data['bic']);
        }

        $transaction->setIban($data['iban']);
        $transaction->setAmount($data['amount']);
        $transaction->setReason($data['reason']);

        return $transaction;
    }
}
