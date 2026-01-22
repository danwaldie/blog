<?php

namespace App\Filament\Resources;

use App\Enums\PostStatus;
use App\Filament\Resources\PostResource\Pages;
use App\Filament\Resources\PostResource\RelationManagers;
use App\Models\Post;
use Carbon\CarbonImmutable;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\HtmlString;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Post')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(200)
                            ->live(onBlur: true),

                        Forms\Components\TextInput::make('slug')
                            ->helperText('Leave blank to auto-generate from title.')
                            ->maxLength(200)
                            ->rule('alpha_dash')
                            ->unique(table: 'posts', column: 'slug', ignoreRecord: true)
                            ->dehydrateStateUsing(fn ($state) => filled($state) ? $state : null),

                        Forms\Components\Select::make('status')
                            ->required()
                            ->options([
                                PostStatus::Draft->value => 'Draft',
                                PostStatus::Published->value => 'Published',
                                PostStatus::Scheduled->value => 'Scheduled',
                            ])
                            ->live(),

                        Forms\Components\DateTimePicker::make('published_at')
                            ->helperText('Required for Scheduled posts. Auto-set when publishing.')
                            ->visible(fn (Forms\Get $get): bool => $get('status') !== PostStatus::Draft->value),

                        Forms\Components\Textarea::make('excerpt')
                            ->disabled()
                            ->helperText('Auto-generated from content unless you set it manually in your flow.')
                            ->rows(3),

                        Forms\Components\MarkdownEditor::make('body_markdown')
                            ->required()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Tags')
                    ->schema([
                        Forms\Components\Select::make('tags')
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
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->limit(50),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('published_at')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('author.name')
                    ->label('Author')
                    ->sortable(),

                Tables\Columns\TextColumn::make('tags.name')
                    ->label('Tags')
                    ->badge()
                    ->separator(', ')
                    ->limit(3),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        PostStatus::Draft->value => 'Draft',
                        PostStatus::Published->value => 'Published',
                        PostStatus::Scheduled->value => 'Scheduled',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('preview')
                    ->label('Preview')
                    ->icon('heroicon-o-eye')
                    ->modalHeading(fn (Post $record) => 'Preview: ' . $record->title)
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close')
                    ->modalContent(fn (Post $record) => new HtmlString(
                        '<div class="prose max-w-none">' . ($record->body_html ?? '') . '</div>'
                    )),

                Tables\Actions\Action::make('view')
                    ->label('View')
                    ->url(fn (Post $record): string => route('blog.show', $record->slug))
                    ->openUrlInNewTab()
                    ->visible(fn (Post $record): bool => $record->isPubliclyVisible()),

                Tables\Actions\Action::make('publish')
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
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}
