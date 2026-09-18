<?php
use App\Models\Tenant;

use Livewire\Component;

new class extends Component
{
    public $tenantName;
    public $tenants = [];

    public function mount()
    {
        $this->tenants = Tenant::all();
    }
};
?>

<div>
    {{-- People find pleasure in different ways. I find it in keeping my mind clear. - Marcus Aurelius --}}
</div>
