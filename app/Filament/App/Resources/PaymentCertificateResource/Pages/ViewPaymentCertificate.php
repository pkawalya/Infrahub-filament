<?php
namespace App\Filament\App\Resources\PaymentCertificateResource\Pages;
use App\Filament\App\Resources\PaymentCertificateResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
class ViewPaymentCertificate extends ViewRecord
{
    protected static string $resource = PaymentCertificateResource::class;
    protected function getHeaderActions(): array
    {
        return [Actions\EditAction::make()];
    }
}
