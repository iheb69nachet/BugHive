<?php

namespace BugHive\Concerns;

interface BugHivable
{
    /**
     * @return array
     */
    public function toLarabug();
}