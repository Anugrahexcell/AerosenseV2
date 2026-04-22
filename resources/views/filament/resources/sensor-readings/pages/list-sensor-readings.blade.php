<x-filament-panels::page>
    {{-- ⟳ Auto-refresh every 10 seconds to pick up new sensor readings from Fakultas Teknik --}}
    <div wire:poll.60s></div>

    {{ $this->content }}
</x-filament-panels::page>
