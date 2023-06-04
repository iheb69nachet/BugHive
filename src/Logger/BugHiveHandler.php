<?php

namespace BugHive\Logger;

use Throwable;
use Monolog\Logger;
use BugHive\BugHive;
use Monolog\Handler\AbstractProcessingHandler;

class BugHiveHandler extends AbstractProcessingHandler
{
  
    public function __construct(BugHive $laraBug, $level = Logger::ERROR, bool $bubble = true)
    {
        $this->laraBug = $laraBug;

        parent::__construct($level, $bubble);
    }

    /**
     * @param array $record
     */
    protected function write($record): void
    {
        if (isset($record['context']['exception']) && $record['context']['exception'] instanceof Throwable) {
            $this->laraBug->handle(
                $record['context']['exception']
            );

            return;
        }
    }
}