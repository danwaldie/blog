<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(Tests\TestCase::class)->in('Feature', 'Unit');

// Automatically migrate DB for Feature and Unit tests
uses(RefreshDatabase::class)->in('Feature', 'Unit');
