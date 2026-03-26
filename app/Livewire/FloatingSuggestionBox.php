<?php

namespace App\Livewire;

use App\Models\CdeProject;
use App\Models\ProjectSuggestion;
use Livewire\Component;

class FloatingSuggestionBox extends Component
{
    public bool $isOpen = false;
    public string $content = '';
    public string $category = 'general';
    public string $priority = 'normal';
    public ?int $projectId = null;
    public bool $submitted = false;

    public function toggle(): void
    {
        $this->isOpen = !$this->isOpen;
        $this->submitted = false;
    }

    public function close(): void
    {
        $this->isOpen = false;
    }

    public function submit(): void
    {
        // Guard: user must be logged in
        if (!auth()->check()) {
            \Filament\Notifications\Notification::make()
                ->danger()
                ->title('Please log in first')
                ->body('You must be logged in to submit a suggestion.')
                ->send();
            return;
        }

        $this->validate([
            'content'  => ['required', 'string', 'min:10', 'max:2000'],
            'category' => ['required', 'in:' . implode(',', array_keys(ProjectSuggestion::$categories))],
            'priority' => ['required', 'in:' . implode(',', array_keys(ProjectSuggestion::$priorities))],
        ]);

        $user = auth()->user();

        // Determine company & project context
        $companyId = $user?->company_id;

        // If no project selected, use the first active project or null
        $projectId = $this->projectId;
        if (!$projectId) {
            $projectId = CdeProject::where('company_id', $companyId)
                ->where('status', 'active')
                ->value('id');
        }

        // Create the suggestion — ALWAYS anonymous, author_id is never stored
        ProjectSuggestion::create([
            'company_id' => $companyId,
            'cde_project_id' => $projectId,
            'author_id' => null,  // Anonymous — never store who submitted
            'is_anonymous' => true,
            'category' => $this->category,
            'priority' => $this->priority,
            'content' => $this->content,
            'status' => 'new',
            'upvotes' => 0,
        ]);

        // Reset the form
        $this->content = '';
        $this->category = 'general';
        $this->priority = 'normal';
        $this->projectId = null;
        $this->submitted = true;
    }

    public function getProjectsProperty(): array
    {
        $user = auth()->user();
        if (!$user)
            return [];

        return CdeProject::where('company_id', $user->company_id)
            ->where('status', 'active')
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();
    }

    public function render()
    {
        return view('livewire.floating-suggestion-box');
    }
}
