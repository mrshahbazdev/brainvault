<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AuditLogResource\Pages;
use App\Models\AuditLog;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AuditLogResource extends Resource
{
    protected static ?string $model = AuditLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $navigationLabel = 'Audit Logs';

    protected static ?int $navigationSort = 4;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('action')
                    ->searchable()
                    ->colors([
                        'primary' => fn ($state) => str_contains($state, 'create'),
                        'warning' => fn ($state) => str_contains($state, 'update'),
                        'danger' => fn ($state) => str_contains($state, 'delete'),
                        'info' => fn ($state) => str_contains($state, 'ai'),
                    ]),
                Tables\Columns\TextColumn::make('auditable_type')
                    ->label('Resource')
                    ->formatStateUsing(fn ($state) => $state ? class_basename($state) : '-'),
                Tables\Columns\TextColumn::make('auditable_id')
                    ->label('ID'),
                Tables\Columns\TextColumn::make('ip_address'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('M d, Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('action')
                    ->options(fn () => AuditLog::distinct()->pluck('action', 'action')->toArray()),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAuditLogs::route('/'),
        ];
    }
}
