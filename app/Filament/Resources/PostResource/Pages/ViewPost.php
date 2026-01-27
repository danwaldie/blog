<?php

declare(strict_types=1);

namespace App\Filament\Resources\PostResource\Pages;

use App\Filament\Resources\PostResource;
use App\Models\Post;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

/** @property Post $record */
final class ViewPost extends ViewRecord
{
    protected static string $resource = PostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('view_webpage')
                ->label('View webpage')
                ->icon('heroicon-o-globe-alt')
                ->url(fn (Post $record): string => route('blog.show', $record->slug))
                ->openUrlInNewTab()
                ->visible(fn (Post $record): bool => $record->isPubliclyVisible()),
            EditAction::make(),
        ];
    }
}
