<?php


namespace User890104;


use SSilence\ImapClient\ImapClient;
use SSilence\ImapClient\ImapConnect;
use SSilence\ImapClient\IncomingMessage;
use SSilence\ImapClient\Section;

class MailAttachmentParser
{
    protected $imap;

    public function __construct(string $hostname, string $username, string $password, string $mailbox)
    {
        $this->imap = new ImapClient([
            'flags' => [
                'encrypt' => ImapConnect::ENCRYPT_SSL,
            ],
            'mailbox' => [
                'remote_system_name' => $hostname,
                'mailbox_name' => $mailbox,
            ],
            'connect' => [
                'username' => $username,
                'password' => $password,
            ]
        ]);
    }

    public function parseMessageAttachments(string $filenamePattern, string $destinationMailbox, callable $callback)
    {
        $messages = $this->imap->getMessages();

        foreach ($messages as $message) {
            /**
             * @var IncomingMessage $message
             */
            foreach ($message->attachments as $attachment) {
                if (!preg_match($filenamePattern, $attachment->name)) {
                    continue;
                }

                if (
                    is_object($attachment) &&
                    property_exists($attachment, 'info') &&
                    $attachment->info instanceof Section
                ) {
                    switch ($attachment->info->structure->encoding) {
                        case 3:
                            $attachment->body = imap_base64($attachment->body);
                            break;
                        case 4:
                            $attachment->body = quoted_printable_decode($attachment->body);
                            break;
                    }
                }

                // Store into temporary file
                $fp = tmpfile();
                $metadata = stream_get_meta_data($fp);
                $filename = $metadata['uri'];
                fwrite($fp, $attachment->body);
                $callback($filename, $attachment->name);
                fclose($fp);
            }

            $this->imap->moveMessage($message->header->uid, $destinationMailbox);
        }
    }
}
