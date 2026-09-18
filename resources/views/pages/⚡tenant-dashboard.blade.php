<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts::tenant'), Title('Tenant dashboard')] class extends Component
{
    public function getTenantIdProperty(): string
    {
        return (string) tenant('id');
    }
}; ?>

<div class="flex w-full flex-1 flex-col gap-6">
    <div class="flex flex-col gap-2">
        <flux:heading level="1">{{ __('Tenant dashboard') }}</flux:heading>
        <flux:subheading>
            {{ __('Current tenant:') }} <code class="rounded bg-zinc-100 px-1.5 py-0.5 text-sm dark:bg-zinc-800">{{ $this->tenantId }}</code>
        </flux:subheading>
    </div>

    <flux:card class="flex items-center gap-4">
        <div class="grid flex-1 gap-1">
            <flux:heading>{{ __('Tasks') }}</flux:heading>
            <flux:subheading line-clamp-2>{{ __('Only the tasks of this tenant are shown here.') }}</flux:subheading>
        </div>

        <flux:button variant="primary" :href="route('tenant.tasks')" icon-trailing="arrow-right" wire:navigate>
            {{ __('Open tasks') }}
        </flux:button>
    </flux:card>
</div>
