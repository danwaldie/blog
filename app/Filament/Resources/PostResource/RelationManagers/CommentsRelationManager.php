<?php

namespace App\Filament\Resources\PostResource\RelationManagers;

use App\Enums\CommentStatus;
use App\Models\Comment;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CommentsRelationManager extends RelationManager
{
    protected static string $relationship = 'comments';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('commenter_name')
                    ->disabled(),

                Textarea::make('body')
                    ->disabled(),

                Textarea::make('moderation_explanation')
                    ->disabled()
                    ->hidden(fn (?Comment $record) => blank($record?->moderation_explanation))
                    ->rows(5),
                
                TextInput::make('moderation_confidence')
                    ->disabled()
                    ->hidden(fn (?Comment $record) => blank($record?->moderation_confidence)),
                
                Textarea::make('moderation_error')
                    ->disabled()
                    ->hidden(fn (?Comment $record) => blank($record?->moderation_error))
                    ->rows(5),
                
                Select::make('status')
                    ->required()
                    ->options([
                        CommentStatus::Submitted->value => 'Submitted',
                        CommentStatus::Published->value => 'Published',
                        CommentStatus::Rejected->value => 'Rejected',
                    ])
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('body')
            ->columns([
                TextColumn::make('body')
                    ->wrap()
                    ->description(fn(Comment $record): string => $record->commenter_name, 'above'),
                
                SelectColumn::make('status')
                    ->options([
                        CommentStatus::Submitted->value => 'Submitted',
                        CommentStatus::Published->value => 'Published',
                        CommentStatus::Rejected->value => 'Rejected',
                    ])
                    ->sortable(),

            ])
            ->filters([
                //
            ])
            ->actions([
                ViewAction::make()
                    ->label('View moderation')
                    ->infolist(fn (Infolist $infolist) => $infolist->schema([
                        Section::make('Comment')
                            ->schema([
                                TextEntry::make('commenter_name')->label('From'),
                                TextEntry::make('body')->label('Comment')->markdown(), // or ->formatStateUsing(...)
                                TextEntry::make('status')->badge(),
                            ])->columns(1),

                        Section::make('Moderation')
                            ->schema([
                                TextEntry::make('moderation_confidence')->label('Confidence'),
                                TextEntry::make('moderation_explanation')
                                    ->label('Explanation')
                                    ->visible(fn ($record) => filled($record->moderation_explanation)),
                                TextEntry::make('moderation_error')
                                    ->label('Error')
                                    ->visible(fn ($record) => filled($record->moderation_error)),
                                TextEntry::make('moderated_at')->dateTime(),
                            ])->columns(1),
                    ])),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
