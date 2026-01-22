<?php

namespace App\Filament\Resources\PostResource\Pages;

use App\Enums\PostStatus;
use App\Filament\Resources\PostResource;
use Carbon\CarbonImmutable;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\HtmlString;

class EditPost extends EditRecord
{
    protected static string $resource = PostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('preview')
                ->label('Preview')
                ->icon('heroicon-o-eye')
                ->modalHeading(fn (): string => 'Preview: ' . $this->record->title)
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Close')
                ->modalContent(fn () => new HtmlString(
                    '<div class="prose max-w-none">' . ($this->record->body_html ?? '') . '</div>'
                )),

            Actions\Action::make('publish')
                ->label('Publish')
                ->icon('heroicon-o-paper-airplane')
                ->requiresConfirmation()
                ->visible(fn (): bool => $this->record->status !== PostStatus::Published)
                ->action(function (): void {
                    $this->record->status = PostStatus::Published;
                    $this->record->published_at ??= CarbonImmutable::now();
                    $this->record->save();

                    Notification::make()
                        ->title('Post published')
                        ->success()
                        ->send();
                }),

            Actions\DeleteAction::make(),
        ];
    }
}
