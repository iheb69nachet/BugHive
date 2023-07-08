<?php

namespace BugHive\Logger;

use Throwable;
use Monolog\Logger;
use BugHive\BugHive;
use Monolog\Handler\AbstractProcessingHandler;

class BugHiveHandler extends AbstractProcessingHandler
{
  
    public function __construct(BugHive $bughive, $level = Logger::ERROR, bool $bubble = true)
    {
        $this->bughive = $bughive;

        parent::__construct($level, $bubble);
    }

    /**
     * @param array $record
     */
    protected function write($record): void
    {
        if (isset($record['context']['exception']) && $record['context']['exception'] instanceof Throwable) {
            $this->bughive->handle(
                $record['context']['exception']
            );

            return;
        }
    }
}