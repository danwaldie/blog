<?php

namespace App\Filament\Resources;

use App\Enums\PostStatus;
use App\Filament\Resources\PostResource\Pages;
use App\Filament\Resources\PostResource\RelationManagers\CommentsRelationManager;
use App\Models\Post;
use Carbon\CarbonImmutable;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Post')
                    ->schema([
                        TextInput::make('title')
                            ->required()
                            ->maxLength(200)
                            ->live(onBlur: true),

                        TextInput::make('slug')
                            ->helperText('Leave blank to auto-generate from title.')
                            ->maxLength(200)
                            ->rule('alpha_dash')
                            ->unique(table: 'posts', column: 'slug', ignoreRecord: true)
                            ->dehydrateStateUsing(fn ($state) => filled($state) ? $state : null),

                        Select::make('status')
                            ->required()
                            ->options([
                                PostStatus::Draft->value => 'Draft',
                                PostStatus::Published->value => 'Published',
                                PostStatus::Scheduled->value => 'Scheduled',
                            ])
                            ->live(),

                        DateTimePicker::make('published_at')
                            ->helperText('Required for Scheduled posts. Auto-set when publishing.')
                            ->visible(fn (Forms\Get $get): bool => $get('status') !== PostStatus::Draft->value),

                        Textarea::make('excerpt')
                            ->disabled()
                            ->helperText('Auto-generated from content unless you set it manually in your flow.')
                            ->rows(3),

                        MarkdownEditor::make('body_markdown')
                            ->required()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Tags')
                    ->schema([
                        Select::make('tags')
                            ->relationship('tags', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(fn ($record) => static::getUrl('view', ['record' => $record->getKey()]))
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->limit(50),

                TextColumn::make('status')
                    ->badge()
                    ->sortable(),

                TextColumn::make('published_at')
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('author.name')
                    ->label('Author')
                    ->sortable(),

                TextColumn::make('tags.name')
                    ->label('Tags')
                    ->badge()
                    ->separator(', ')
                    ->limit(3),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        PostStatus::Draft->value => 'Draft',
                        PostStatus::Published->value => 'Published',
                        PostStatus::Scheduled->value => 'Scheduled',
                    ]),
            ])
            ->actions([
                Action::make('view')
                    ->label('View')
                    ->icon('heroicon-o-globe-alt')
                    ->url(fn (Post $record): string => route('blog.show', $record->slug))
                    ->openUrlInNewTab()
                    ->visible(fn (Post $record): bool => $record->isPubliclyVisible()),

                Action::make('publish')
                    ->label('Publish')
                    ->icon('heroicon-o-paper-airplane')
                    ->requiresConfirmation()
                    ->visible(fn (Post $record): bool => $record->status !== PostStatus::Published)
                    ->action(function (Post $record): void {
                        // IMPORTANT: instance save so model events fire
                        $record->status = PostStatus::Published;
                        $record->published_at ??= CarbonImmutable::now();
                        $record->save();

                        Notification::make()
                            ->title('Post published')
                            ->success()
                            ->send();
                    }),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            CommentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'view' => Pages\ViewPost::route('/{record}'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}
