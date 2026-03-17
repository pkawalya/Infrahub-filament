<?php

namespace App\Filament\Client\Pages;

use Filament\Pages\Page;

class ClientHelp extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-question-mark-circle';
    protected static ?string $navigationLabel = 'Help & Docs';
    protected static ?string $title = 'Help & Documentation';
    protected static ?int $navigationSort = 99;
    protected string $view = 'filament.client.pages.help';
}
