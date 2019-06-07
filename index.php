<?php
require 'vendor/autoload.php';

use Dotenv\Dotenv;
use User890104\MailAttachmentParser;
use User890104\UnicreditPdfParser;

header('Content-Type: text/plain; charset=utf-8');

$dotenv = Dotenv::create(__DIR__);
$dotenv->load();

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
    function ($filename) use ($parser) {
        $transactions = $parser->parseFile($filename);
        echo json_encode($transactions, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
);
