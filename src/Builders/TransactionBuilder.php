<?php


namespace Builders;


use Entities\Transaction;

class TransactionBuilder
{
    /**
     * @param array $data
     * @return Transaction
     */
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
        $transaction->setIban($data['iban']);
        $transaction->setAmount($data['amount']);
        $transaction->setReason($data['reason']);

        return $transaction;
    }
}
