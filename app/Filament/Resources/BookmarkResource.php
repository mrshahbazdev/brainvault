<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookmarkResource\Pages;
use App\Models\Bookmark;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class BookmarkResource extends Resource
{
    protected static ?string $model = Bookmark::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-bookmark';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Forms\Components\TextInput::make('url')
                ->url()
                ->required()
                ->maxLength(2000)
                ->columnSpanFull(),
            Forms\Components\TextInput::make('title')
                ->maxLength(500),
            Forms\Components\Textarea::make('description')
                ->rows(3),
            Forms\Components\Select::make('content_type')
                ->options([
                    'article' => 'Article',
                    'documentation' => 'Documentation',
                    'webpage' => 'Webpage',
                    'video' => 'Video',
                    'pdf' => 'PDF',
                ]),
            Forms\Components\TextInput::make('ai_category')
                ->disabled(),
            Forms\Components\Textarea::make('ai_summary')
                ->disabled()
                ->rows(3),
            Forms\Components\Toggle::make('is_favorite'),
            Forms\Components\Toggle::make('is_archived'),
            Forms\Components\Toggle::make('is_read'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->limit(50),
                Tables\Columns\TextColumn::make('user.name')
                    ->searchable()
                    ->sortable()
                    ->label('User'),
                Tables\Columns\TextColumn::make('site_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('content_type')
                    ->badge()
                    ->colors([
                        'primary' => 'article',
                        'success' => 'documentation',
                        'gray' => 'webpage',
                        'warning' => 'video',
                    ]),
                Tables\Columns\TextColumn::make('ai_category')
                    ->badge()
                    ->color('info'),
                Tables\Columns\IconColumn::make('is_favorite')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_read')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('M d, Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('content_type')
                    ->options([
                        'article' => 'Article',
                        'documentation' => 'Documentation',
                        'webpage' => 'Webpage',
                        'video' => 'Video',
                    ]),
                Tables\Filters\TernaryFilter::make('is_favorite'),
                Tables\Filters\TernaryFilter::make('is_archived'),
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
            'index' => Pages\ListBookmarks::route('/'),
            'edit' => Pages\EditBookmark::route('/{record}/edit'),
        ];
    }
}
