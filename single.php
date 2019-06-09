<?php
require __DIR__ . '/config/bootstrap.php';

use Builders\TransactionBuilder;
use Entities\Transaction;
use User890104\UnicreditPdfParser;

header('Content-Type: text/plain; charset=utf-8');

if ($_SERVER['argc'] < 2) {
	echo 'usage: ', $_SERVER['argv'][0], ' <pdf-file>', PHP_EOL;
	exit(1);
}

$parser = new UnicreditPdfParser;

$transactions = $parser->parseFile($_SERVER['argv'][1]);

echo json_encode($transactions, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

foreach ($transactions as $transaction) {
	if (!array_key_exists('iban', $transaction)) {
		continue;
	}

    $transactionEntity = $entityManager->find(Transaction::class, $transaction['id']);

    if ($transactionEntity instanceof Transaction) {
        continue;
    }

    $transactionEntity = TransactionBuilder::buildEntity($transaction);
	$entityManager->persist($transactionEntity);
}

$entityManager->flush();
