<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Resources\Pages\ListRecords;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    public function getSubheading(): ?string
    {
        return 'Aktif operasyon, ödeme bekleyen siparişler ve müşteri teslimatları bu ekrandan izlenir.';
    }
}
