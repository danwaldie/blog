<?php

namespace App\Filament\Resources;

use App\Enums\CommentStatus;
use App\Filament\Resources\CommentResource\Pages;
use App\Models\Comment;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class CommentResource extends Resource
{
    protected static ?string $model = Comment::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Comment')
                    ->schema([
                        TextInput::make('commenter_name')
                            ->disabled(),

                        Textarea::make('body')
                            ->label('Comment')
                            ->rows(5)
                            ->disabled(),

                        Select::make('status')
                            ->required()
                            ->options([
                                CommentStatus::Submitted->value => 'Submitted',
                                CommentStatus::Published->value => 'Published',
                                CommentStatus::Rejected->value => 'Rejected',
                            ]),

                        DateTimePicker::make('published_at')
                            ->disabled(),

                        Textarea::make('moderation_explanation')
                            ->disabled()
                            ->rows(5)
                            ->hidden(fn (?Comment $record) => blank($record?->moderation_explanation)),

                        Textarea::make('moderation_error')
                            ->disabled()
                            ->rows(5)
                            ->hidden(fn (?Comment $record) => blank($record?->moderation_error))
                            ->visibleOn(['edit']),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('post.title')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('body')
                    ->wrap()
                    ->searchable(),
                
                TextColumn::make('moderation_confidence')
                    ->label('Moderation Confidence'),

                SelectColumn::make('status')
                    ->options([
                        CommentStatus::Submitted->value => 'Submitted',
                        CommentStatus::Published->value => 'Published',
                        CommentStatus::Rejected->value => 'Rejected',
                    ])
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        CommentStatus::Submitted->value => 'Submitted',
                        CommentStatus::Published->value => 'Published',
                        CommentStatus::Rejected->value => 'Rejected',
                    ]),
                SelectFilter::make('post')
                    ->relationship('post', 'title')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                //
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
                BulkAction::make('update_status')
                    ->label('Update status')
                    ->icon('heroicon-m-check-circle')
                    ->form([
                        Select::make('status')
                            ->label('New status')
                            ->options([
                                CommentStatus::Submitted->value => 'Submitted',
                                CommentStatus::Published->value => 'Published',
                                CommentStatus::Rejected->value => 'Rejected',
                            ])
                            ->required(),
                    ])
                    ->action(function (Collection $records, array $data): void {
                        /** @var array{status: string} $data */
                        /** @var Collection<int, Comment> $records */
                        $status = $data['status'];
                        foreach ($records as $record) {
                            $record->status = CommentStatus::from($status);
                            $record->save();
                        }
                    })
                    ->deselectRecordsAfterCompletion(),
            ]);
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
            'index' => Pages\ListComments::route('/'),
            'edit' => Pages\EditComment::route('/{record}/edit'),
        ];
    }
}
