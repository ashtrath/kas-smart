<x-filament-panels::page full-height="true">
    <div class="relative grid grid-cols-1 lg:grid-cols-3 gap-6 pb-8">
        <div class="lg:col-span-2">
            <x-filament::section class="h-full">
                @livewire('products-grid')
            </x-filament::section>
        </div>
        <div class="lg:col-span-1">
            <x-filament-panels::form
                :wire:key="$this->getId() . '.forms.' . $this->getFormStatePath()"
            >
                {{ $this->form }}
            </x-filament-panels::form>
        </div>
    </div>
</x-filament-panels::page>
