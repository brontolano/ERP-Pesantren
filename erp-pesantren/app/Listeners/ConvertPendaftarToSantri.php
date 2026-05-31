<?php

namespace App\Listeners;

use App\Events\DaftarUlangPaid;
use App\Services\SpmbService;

class ConvertPendaftarToSantri
{
    public function __construct(
        protected SpmbService $spmbService,
    ) {}

    public function handle(DaftarUlangPaid $event): void
    {
        $this->spmbService->convertToSantri($event->registration);
    }
}
