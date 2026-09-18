<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts::tenant'), Title('Tenant tasks')] class extends Component
{
}; ?>

<div class="flex w-full flex-1 flex-col gap-6">
    <div class="flex flex-col gap-2">
        <flux:heading level="1">{{ __('Tasks') }}</flux:heading>
        <flux:subheading>{{ __('Only the tasks of this tenant are shown here.') }}</flux:subheading>
    </div>

    <livewire:task-list />
</div>
