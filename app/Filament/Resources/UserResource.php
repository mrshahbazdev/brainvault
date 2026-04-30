<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->required()
                ->maxLength(255),
            Forms\Components\TextInput::make('email')
                ->email()
                ->required()
                ->maxLength(255),
            Forms\Components\Select::make('plan')
                ->options([
                    'free' => 'Free',
                    'pro' => 'Pro',
                    'team' => 'Team',
                    'enterprise' => 'Enterprise',
                ])
                ->default('free'),
            Forms\Components\Toggle::make('is_admin')
                ->label('Admin Access'),
            Forms\Components\Toggle::make('onboarding_completed'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('plan')
                    ->colors([
                        'gray' => 'free',
                        'success' => 'pro',
                        'primary' => 'team',
                        'warning' => 'enterprise',
                    ]),
                Tables\Columns\TextColumn::make('bookmarks_count')
                    ->counts('bookmarks')
                    ->label('Bookmarks')
                    ->sortable(),
                Tables\Columns\TextColumn::make('notes_count')
                    ->counts('notes')
                    ->label('Notes')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('M d, Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('plan')
                    ->options([
                        'free' => 'Free',
                        'pro' => 'Pro',
                        'team' => 'Team',
                        'enterprise' => 'Enterprise',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
