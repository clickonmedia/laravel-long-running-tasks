<?php

use Clickonmedia\Monitor\Tests\TestCase;

uses(TestCase::class)
    ->beforeEach(fn () => ray()->newScreen($this->name()))
    ->in(__DIR__);
