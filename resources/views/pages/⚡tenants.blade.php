<?php

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Tenants')] class extends Component
{
    public string $name = '';

    public string $email = '';

    public string $domain = '';

    public string $ownerName = '';

    public string $ownerEmail = '';

    public string $ownerPassword = '';

    public string $ownerPassword_confirmation = '';

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'domain' => [
                'required',
                'string',
                'max:255',
                Rule::notIn(config('tenancy.central_domains')),
                Rule::unique('domains', 'domain'),
            ],
            'ownerName' => ['required', 'string', 'max:255'],
            'ownerEmail' => ['required', 'email', 'max:255'],
            'ownerPassword' => array_filter([
                'required',
                'confirmed',
                Password::defaults(),
            ]),
        ];
    }

    public function register(): void
    {
        $this->validate();

        $tenant = Tenant::create([
            'name' => $this->name,
            'email' => $this->email,
        ]);

        $tenant->domains()->create([
            'domain' => strtolower($this->domain),
        ]);

        $tenant->run(fn () => User::create([
            'name' => $this->ownerName,
            'email' => $this->ownerEmail,
            'password' => Hash::make($this->ownerPassword),
        ]));

        $this->reset(['name', 'email', 'domain', 'ownerName', 'ownerEmail', 'ownerPassword', 'ownerPassword_confirmation']);

        $this->dispatch('toast', message: __('Tenant created successfully.'));
    }

    public function delete(Tenant $tenant): void
    {
        $tenant->delete();

        $this->dispatch('toast', message: __('Tenant deleted successfully.'));
    }

    #[Computed]
    public function tenants()
    {
        return Tenant::with('domains')->latest()->get();
    }
}; ?>

<section class="w-full space-y-6">
    <flux:heading level="1">{{ __('Tenants') }}</flux:heading>
    <flux:subheading>{{ __('Register and manage the tenants of your application.') }}</flux:subheading>

    <flux:card>
        <div>
            <flux:heading>{{ __('Register a new tenant') }}</flux:heading>
            <flux:subheading>{{ __('The tenant database and its migrations are created automatically.') }}</flux:subheading>
        </div>

        <form wire:submit="register" class="space-y-4">
            <div class="grid gap-4 sm:grid-cols-2">
                <flux:field>
                    <flux:label>{{ __('Tenant name') }}</flux:label>
                    <flux:input wire:model="name" placeholder="{{ __('Acme Inc.') }}" />
                    <flux:error name="name" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Tenant email') }}</flux:label>
                    <flux:input type="email" wire:model="email" placeholder="acme@example.com" />
                    <flux:error name="email" />
                </flux:field>
            </div>

            <flux:field>
                <flux:label>{{ __('Domain') }}</flux:label>
                <flux:input wire:model="domain" placeholder="acme.localhost" />
                <flux:error name="domain" />
            </flux:field>

            <flux:separator />

            <div class="grid gap-4 sm:grid-cols-2">
                <flux:field>
                    <flux:label>{{ __('Owner name') }}</flux:label>
                    <flux:input wire:model="ownerName" placeholder="{{ __('Jane Doe') }}" />
                    <flux:error name="ownerName" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Owner email') }}</flux:label>
                    <flux:input type="email" wire:model="ownerEmail" placeholder="jane@acme.com" />
                    <flux:error name="ownerEmail" />
                </flux:field>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <flux:field>
                    <flux:label>{{ __('Password') }}</flux:label>
                    <flux:input type="password" wire:model="ownerPassword" />
                    <flux:error name="ownerPassword" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Confirm password') }}</flux:label>
                    <flux:input type="password" wire:model="ownerPassword_confirmation" />
                    <flux:error name="ownerPassword" />
                </flux:field>
            </div>

            <flux:button type="submit" variant="primary" wire:loading.attr="disabled" wire:target="register" icon="plus">
                {{ __('Register tenant') }}
            </flux:button>
        </form>
    </flux:card>

    <flux:card>
        <div>
            <flux:heading>{{ __('Current tenants') }}</flux:heading>
            <flux:subheading>{{ $this->tenants->count() }} {{ __('registered') }}</flux:subheading>
        </div>

        <flux:table>
            <flux:table.columns>
                <flux:table.column>{{ __('Name') }}</flux:table.column>
                <flux:table.column>{{ __('Email') }}</flux:table.column>
                <flux:table.column>{{ __('Domain') }}</flux:table.column>
                <flux:table.column class="w-24"></flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($this->tenants as $tenant)
                    <flux:table.row :key="$tenant->id">
                        <flux:table.cell>{{ $tenant->name }}</flux:table.cell>
                        <flux:table.cell>{{ $tenant->email }}</flux:table.cell>
                        <flux:table.cell>{{ $tenant->domains()->value('domain') }}</flux:table.cell>
                        <flux:table.cell>
                            <flux:button
                                size="sm"
                                variant="danger"
                                icon="trash"
                                wire:click="delete('{{ $tenant->id }}')"
                                wire:confirm="{{ __('Delete this tenant and its database?') }}"
                            >
                                {{ __('Delete') }}
                            </flux:button>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="4" class="text-center text-zinc-500">
                            {{ __('No tenants registered yet.') }}
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </flux:card>
</section>
