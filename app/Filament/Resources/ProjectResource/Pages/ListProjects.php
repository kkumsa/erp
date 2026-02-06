<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Filament\Resources\ProjectResource;
use App\Models\Project;
use Filament\Actions;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ListRecords;
class ListProjects extends ListRecords implements HasInfolists
{
    use InteractsWithInfolists;

    protected static string $resource = ProjectResource::class;

    protected static string $view = 'filament.resources.project-resource.pages.list-projects';

    public bool $slideOverMode = true;

    public ?int $selectedProjectId = null;

    public ?Project $selectedProject = null;

    public function selectProject(int $projectId): void
    {
        if ($this->slideOverMode) {
            $this->selectedProjectId = $projectId;
            $this->selectedProject = Project::find($projectId);
            $this->dispatch('project-selected');
        } else {
            $this->redirect(ProjectResource::getUrl('view', ['record' => $projectId]));
        }
    }

    public function closePanel(): void
    {
        $this->selectedProjectId = null;
        $this->selectedProject = null;
    }

    public function setSlideOverMode(bool $mode): void
    {
        $this->slideOverMode = $mode;
        
        if (!$mode) {
            $this->closePanel();
        }
    }

    public function projectInfolist(Infolist $infolist): Infolist
    {
        if (!$this->selectedProject) {
            return $infolist->schema([]);
        }

        return ProjectResource::infolist($infolist)
            ->record($this->selectedProject);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
