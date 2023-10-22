<?php

declare(strict_types=1);

namespace App\Console;

use App\Factory\EmailFactory;
use Cake\Database\Connection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class SendEmailsCommand extends Command
{
    private Connection $connection;

    private EmailFactory $emailFactory;

    public function __construct(Connection $connection, EmailFactory $emailFactory) {
        parent::__construct();
        $this->connection = $connection;
        $this->emailFactory = $emailFactory;
    }

    protected function configure(): void
    {
        parent::configure();

        $this->setName('send-emails');
        $this->setDescription('Send a batch of emails');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $emails = $this
            ->connection
            ->selectQuery(
                [
                    'id' => 'e.id',
                    'email' => 'e.email',
                    'topic' => 'e.topic',
                    'body' => 'e.body',
                    'name' => 'o.name',
                ],
                ['e' => 'emails_queue'],
            )
            ->leftJoin(['o' => 'owners'], 'e.email = o.email')
            ->limit(20)
            ->execute()
            ->fetchAll('assoc') ?: [];

        foreach ($emails as $data) {
            $email = $this->emailFactory->createEmail();

            $email->addAddress($data['email'], $data['name']);
            $email->Subject = $data['topic'];
            $email->Body = $data['body'];
            $email->send();

            $this
                ->connection
                ->deleteQuery('emails_queue', ['id =' => $data['id']])
                ->execute();
        }

        return Command::SUCCESS;
    }
}
