<?php


namespace User890104;

use DateTime;
use Exception;
use Smalot\PdfParser\Document;
use Smalot\PdfParser\Page;
use Smalot\PdfParser\Parser as PdfParser;

/**
 * Class UnicreditPdfParser
 * @package User890104
 */
class UnicreditPdfParser
{
    /**
     *
     */
    const FORMAT_DATE = '/^\d{2}\.\d{2}\.\d{4}$/';
    /**
     *
     */
    const FORMAT_TRANSACTION_ID = '/^\d{3}[A-Z\d]{13}$/';
    /**
     *
     */
    const FORMAT_AMOUNT = '/^\d+\.\d{2}$/';
    /**
     *
     */
    const FORMAT_LOCAL_ACC = '/^\d{14}$/';
    /**
     *
     */
    const FORMAT_IBAN = '/^[A-Z]{2}\d{2}[A-Z]{4}\d{6}[A-Z0-9]{8}$/';
    /**
     *
     */
    const FORMAT_BIC = '/^[A-Z]{4}\d{4}$/';

    /**
     * @var PdfParser
     */
    protected $parser;
    /**
     * @var
     */
    protected $filename;
    /**
     * @var
     */
    protected $description;

    /**
     * UnicreditPdfParser constructor.
     */
    public function __construct()
    {
        $this->parser = new PdfParser;
    }

    /**
     * @param string $str
     * @param string $suffix
     * @return bool
     */
    protected static function removeSuffix(string &$str, string $suffix) {
        $pos = strrpos($str, $suffix);

        if ($pos === strlen($str) - strlen($suffix)) {
            $str = substr($str, 0, $pos);
			return true;
        }
		
		return false;
    }

    /**
     * @param string $dateStr
     * @return DateTime
     */
    protected static function parseDate(string $dateStr): DateTime
    {
        return DateTime::createFromFormat('d.m.Y', $dateStr);
    }

    /**
     * @param array $transaction
     * @param string $desc
     * @param int $version
     * @throws Exception
     */
    protected static function parseDescription(array &$transaction, string $desc, int $version) {
        switch ($version) {
            case 1:
                static::parseDescriptionV1($transaction, $desc);
                break;
            case 2:
                static::parseDescriptionV2($transaction, $desc);
                break;
            default:
                throw new Exception('Unsupported version: ' . $version);
        }
    }

    /**
     * @param array $transaction
     * @param string $desc
     */
    protected static function parseDescriptionV1(array &$transaction, string $desc) {
        $sep = ' / ';

        if (strpos($desc, '-') === 0) {
            $desc = substr($desc, 1);
        }

        static::removeSuffix($desc, 'По курс:');
        static::removeSuffix($desc, ' ' . $sep . 'U');
        $desc = rtrim($desc);
        $parts = explode(' Контрагент: ', $desc, 2);

        if (count($parts) === 2) {
            $type = $parts[0];
            $pos = strrpos($type, ' ');

            if ($pos !== false) {
                $otherId = substr($type, $pos + 1);

                if (preg_match(static::FORMAT_TRANSACTION_ID, $otherId)) {
                    $transaction['id_other'] = $otherId;
                    $type = substr($type, 0, $pos);
                }
            }

            $transaction['type'] = $type;
            $desc = $parts[1];
        }

        $parts = explode($sep, $desc, 2);

        if (count($parts) === 2) {
            $sender = $parts[0];
            $desc = $parts[1];

            $pos = strrpos($sender, ' ');
            if ($pos !== false) {
                $lastPart = substr($sender, $pos + 1);

                $isBic = preg_match(static::FORMAT_BIC, $lastPart);
                $isLocalAcc = preg_match(static::FORMAT_LOCAL_ACC, $lastPart);

                if (!$isBic && $isLocalAcc) {
                    $iban = 'BG00UNCR' . $lastPart;
                    $transaction['iban'] = iban_set_checksum($iban);
                }

                if ($isBic || $isLocalAcc) {
                    $sender = substr($sender, 0, $pos);
                }
            }

            $transaction['sender'] = $sender;

            if (strpos($desc, $sep) === 0) {
                $desc = str_replace($sep, '', $desc);
            }

            $parts = explode($sep, $desc);
            if (count($parts) === 2 && preg_match(static::FORMAT_IBAN, $parts[0])) {
                $transaction['iban'] = $parts[0];
                $transaction['reason'] = $parts[1];
            }
            else {
                $reason = $parts[0];
                $pos = strpos($reason, ' ');

                if ($pos !== false) {
                    $iban = substr($reason, 0, $pos);

                    if (preg_match(static::FORMAT_IBAN, $iban)) {
                        $transaction['iban'] = $iban;
                        $reason = substr($reason, $pos + 1);
                    }
                }

                $transaction['reason'] = $reason;
            }
        }
    }

    /**
     * @param array $transaction
     * @param string $desc
     */
    protected static function parseDescriptionV2(array &$transaction, string $desc) {
        $sep = ' / ';

        if (strpos($desc, '-') === 0) {
            $desc = substr($desc, 1);
        }

        static::removeSuffix($desc, ' /');

        /** @noinspection PhpStatementHasEmptyBodyInspection */
        while (static::removeSuffix($desc, $sep));

        static::removeSuffix($desc, ' 00');

        $parts = explode($sep, $desc, 3);

        if (count($parts) === 3) {
            // part 1
            $typeAndRef = $parts[0];
            $pos = strrpos($typeAndRef, ' ');

            if ($pos !== false) {
                $otherId = substr($typeAndRef, $pos + 1);

                if (preg_match(static::FORMAT_TRANSACTION_ID, $otherId)) {
                    $transaction['id_other'] = $otherId;
                    $transaction['type'] = substr($typeAndRef, 0, $pos);
                }
            }

            // part 2
            $transaction['iban'] = $parts[1];

            // part 3
            $senderAndReason = $parts[2];

            // Sorry about this
            $pos = strpos($senderAndReason, ' ');

            if ($pos !== false) {
                $pos = strpos($senderAndReason, ' ', $pos + 1);

                if ($pos !== false) {
                    $pos = strpos($senderAndReason, ' ', $pos + 1);

                    if ($pos !== false) {
                        // Still not given up?
                        $transaction['sender'] = substr($senderAndReason, 0, $pos);
                        $transaction['reason'] = substr($senderAndReason, $pos + 1);
                    }
                }
            }
        }
    }

    /**
     * @param string $filename
     * @return array
     * @throws Exception
     */
    public function parseFile(string $filename)
    {
        /**
         * @var Document $pdf
         */
        $pdf = $this->parser->parseFile($filename);

        $pages = $pdf->getObjectsByType('Page');

        $state = 0;

        $transactions = [];
        $transaction = [];
        $description = [];

        $lines = array_merge(...array_map(function($page) {
            /**
             * @var Page $page
             */
            return array_map(function($line) {
                $line = iconv('utf-8', 'cp1252', $line);
                return iconv('cp1251', 'utf-8', $line);
            }, $page->getTextArray());
        }, array_values($pages)));

        $version = 1;

        $v2Text = 'Детайлна информация за Вашите трансакции за периода';

        if (in_array($v2Text, $lines)) {
            $version = 2;
        }

        foreach ($lines as $line) {
            if ($state === 0 && preg_match(static::FORMAT_DATE, $line)) {
                $state = 1;
                $transaction['date_transaction'] = static::parseDate($line);
                continue;
            }

            if ($state === 1 && preg_match(static::FORMAT_TRANSACTION_ID, $line)) {
                $state = 2;
                $transaction['id'] = $line;
                continue;
            }

            if ($state === 2 && $line === '') {
                $state = 3;
                continue;
            }

            if ($state === 3 && preg_match(static::FORMAT_AMOUNT, $line)) {
                $state = 4;
                $transaction['amount'] = floatval($line);
                continue;
            }

            if ($state === 4) {
                if (preg_match(static::FORMAT_DATE, $line)) {
                    $state = 5;
                    $transaction['date_value'] = static::parseDate($line);
                }
                else {
                    $description[] = $line;
                }
                continue;
            }

            if ($state === 5 && $line === '/') {
                static::parseDescription($transaction, implode(' ', $description), $version);
                $transactions[] = $transaction;
            }

            $state = 0;
            $transaction = [];
            $description = [];
        }

        return $transactions;
    }
}
