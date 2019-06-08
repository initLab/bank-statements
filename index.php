<?php
require __DIR__ . '/config/bootstrap.php';

use Builders\TransactionBuilder;
use User890104\MailAttachmentParser;
use User890104\UnicreditPdfParser;

header('Content-Type: text/plain; charset=utf-8');

$mail = new MailAttachmentParser(
    getenv('MAIL_HOSTNAME'),
    getenv('MAIL_USERNAME'),
    getenv('MAIL_PASSWORD'),
    getenv('MAIL_FOLDER_SOURCE')
);
$filenamePattern = '/^000-52_\d{8}_C_' . getenv('IBAN') . '_\d+\.pdf$/';

$parser = new UnicreditPdfParser;

$mail->parseMessageAttachments(
    $filenamePattern,
    getenv('MAIL_FOLDER_DESTINATION'),
    function ($filename, $attachmentName) use ($parser, $entityManager) {
        // debug
        //copy($filename, $attachmentName);
        $transactions = $parser->parseFile($filename);
        // debug
        //echo json_encode($transactions, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        foreach ($transactions as $transaction) {
            if (!array_key_exists('iban', $transaction)) {
                continue;
            }

            $transactionEntity = TransactionBuilder::buildEntity($transaction);
            $entityManager->persist($transactionEntity);
        }
    }
);

$entityManager->flush();
