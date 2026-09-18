<?php

use App\Models\Task;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
    public string $title = '';

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
        ];
    }

    public function addTask(): void
    {
        $this->validate();

        Task::create([
            'title' => trim($this->title),
            'user_id' => auth()->id(),
        ]);

        $this->reset('title');
    }

    public function toggle(Task $task): void
    {
        $task->update([
            'is_completed' => ! $task->is_completed,
        ]);
    }

    public function deleteTask(Task $task): void
    {
        $task->delete();
    }

    #[Computed]
    public function tasks()
    {
        return Task::latest()->get();
    }
}; ?>

<flux:card>
    <div>
        <flux:heading>{{ __('Tasks') }}</flux:heading>
        <flux:subheading>{{ __('Only the tasks of this tenant are shown here.') }}</flux:subheading>
    </div>

    <form wire:submit="addTask" class="flex gap-2">
        <flux:input
            wire:model="title"
            :placeholder="__('Write a new task…')"
            class="flex-1"
            data-test="task-title-input"
        />

        <flux:button
            type="submit"
            variant="primary"
            icon="plus"
            wire:loading.attr="disabled"
            wire:target="addTask"
            data-test="add-task-button"
        >
            {{ __('Add') }}
        </flux:button>
    </form>

    <flux:error name="title" />

    <ul class="mt-4 divide-y divide-zinc-200 dark:divide-zinc-700">
        @forelse ($this->tasks as $task)
            <li wire:key="task-{{ $task->id }}" class="flex items-center gap-3 py-3">
                <flux:checkbox
                    :checked="$task->is_completed"
                    wire:click="toggle({{ $task->id }})"
                    :label="$task->title"
                    class="flex-1"
                    :class="$task->is_completed ? 'line-through text-zinc-400' : ''"
                />

                <flux:button
                    size="sm"
                    variant="ghost"
                    icon="trash"
                    :aria-label="__('Delete task')"
                    wire:click="deleteTask({{ $task->id }})"
                    wire:confirm="{{ __('Delete this task?') }}"
                    data-test="delete-task-button"
                />
            </li>
        @empty
            <li class="py-6 text-center text-sm text-zinc-500">
                {{ __('No tasks yet. Write your first one above.') }}
            </li>
        @endforelse
    </ul>
</flux:card>