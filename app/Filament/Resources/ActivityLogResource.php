<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActivityLogResource\Pages;
use App\Models\ActivityLog;
use Filament\Forms;
use Filament\Schemas\Schema; // ✅ Benar untuk v5
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Support\Icons\Heroicon;
use BackedEnum;
use Illuminate\Database\Eloquent\Model;
use Filament\Actions\ViewAction; // ✅ Namespace action terpadu di v5

class ActivityLogResource extends Resource
{
    protected static ?string $model = ActivityLog::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static ?string $modelLabel = 'Log Aktivitas';

    protected static ?string $pluralModelLabel = 'Log Aktivitas';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->label('Admin/Kasir')
                    ->disabled()
                    ->required(),
                Forms\Components\TextInput::make('activity_type')
                    ->label('Tipe Aktivitas')
                    ->disabled()
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->label('Deskripsi')
                    ->disabled()
                    ->required()
                    ->maxLength(65535),
                Forms\Components\DateTimePicker::make('created_at')
                    ->label('Waktu Aktivitas')
                    ->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Admin/Kasir')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('activity_type')
                    ->label('Tipe Aktivitas')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->label('Deskripsi')
                    ->limit(50)
                    ->tooltip(fn (Tables\Columns\TextColumn $column): ?string => $column->getState()),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu Aktivitas')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([ 
                ViewAction::make(),
            ])
            ->toolbarActions([ 
                // No bulk actions allowed for activity logs
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListActivityLogs::route('/'),
            'view' => Pages\ViewActivityLog::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }
}