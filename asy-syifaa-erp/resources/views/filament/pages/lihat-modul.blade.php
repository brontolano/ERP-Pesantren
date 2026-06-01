<x-filament-panels::page>
    <div class="space-y-8">
        {{-- Summary Bar --}}
        @php
            $totalModules = collect($this->modules)->pluck('modules')->flatten(1)->count();
            $activeModules = collect($this->modules)->pluck('modules')->flatten(1)->where('active', true)->count();
            $activePct = $totalModules > 0 ? round(($activeModules / $totalModules) * 100) : 0;
        @endphp
        <div class="flex items-center gap-4 p-4 rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="flex-1">
                <div class="flex items-center gap-2 mb-2">
                    <span class="text-2xl font-bold text-gray-900 dark:text-white">{{ $activeModules }}</span>
                    <span class="text-sm text-gray-400">/ {{ $totalModules }} modul</span>
                    <span class="ml-2 px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700 dark:bg-emerald-900/50 dark:text-emerald-300">{{ $activePct }}% aktif</span>
                </div>
                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                    <div class="bg-emerald-500 h-2 rounded-full transition-all duration-500" style="width: {{ $activePct }}%"></div>
                </div>
            </div>
        </div>

        {{-- Module Groups --}}
        @foreach($this->modules as $group)
        <div>
            <div class="flex items-center gap-3 mb-4">
                <h2 class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ $group['group'] }}</h2>
                <div class="flex-1 h-px bg-gray-200 dark:bg-gray-700"></div>
                <span class="text-xs text-gray-400">{{ collect($group['modules'])->where('active', true)->count() }} aktif</span>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
                @foreach($group['modules'] as $module)
                @php
                    $isActive = $module['active'];
                    $color = $module['color'];
                    $colorClasses = match($color) {
                        'emerald' => ['bg' => 'bg-emerald-50 dark:bg-emerald-900/20', 'icon' => 'text-emerald-600 dark:text-emerald-400', 'ring' => 'hover:ring-emerald-300 dark:hover:ring-emerald-700'],
                        'sky' => ['bg' => 'bg-sky-50 dark:bg-sky-900/20', 'icon' => 'text-sky-600 dark:text-sky-400', 'ring' => 'hover:ring-sky-300 dark:hover:ring-sky-700'],
                        'violet' => ['bg' => 'bg-violet-50 dark:bg-violet-900/20', 'icon' => 'text-violet-600 dark:text-violet-400', 'ring' => 'hover:ring-violet-300 dark:hover:ring-violet-700'],
                        'amber' => ['bg' => 'bg-amber-50 dark:bg-amber-900/20', 'icon' => 'text-amber-600 dark:text-amber-400', 'ring' => 'hover:ring-amber-300 dark:hover:ring-amber-700'],
                        'rose' => ['bg' => 'bg-rose-50 dark:bg-rose-900/20', 'icon' => 'text-rose-600 dark:text-rose-400', 'ring' => 'hover:ring-rose-300 dark:hover:ring-rose-700'],
                        default => ['bg' => 'bg-gray-50 dark:bg-gray-800', 'icon' => 'text-gray-400 dark:text-gray-500', 'ring' => ''],
                    };
                @endphp

                @if($isActive)
                <a href="{{ $module['url'] }}" wire:navigate
                   class="group flex flex-col items-center text-center p-4 rounded-xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md hover:ring-2 {{ $colorClasses['ring'] }} transition-all duration-200 hover:-translate-y-0.5">
                    <div class="w-12 h-12 rounded-xl {{ $colorClasses['bg'] }} flex items-center justify-center mb-3 transition-transform duration-200 group-hover:scale-110">
                        <x-filament::icon :icon="$module['icon']" class="w-6 h-6 {{ $colorClasses['icon'] }}" />
                    </div>
                    <span class="text-xs font-semibold text-gray-700 dark:text-gray-300 leading-tight">{{ $module['name'] }}</span>
                    <span class="text-[10px] text-gray-400 dark:text-gray-500 mt-1 leading-tight">{{ $module['description'] }}</span>
                </a>
                @else
                <div class="relative flex flex-col items-center text-center p-4 rounded-xl bg-gray-50 dark:bg-gray-800/50 border border-dashed border-gray-300 dark:border-gray-700 opacity-60 cursor-not-allowed select-none">
                    <span class="absolute top-2 right-2 px-1.5 py-0.5 rounded text-[9px] font-bold bg-amber-100 text-amber-700 dark:bg-amber-900/50 dark:text-amber-400">SOON</span>
                    <div class="w-12 h-12 rounded-xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center mb-3">
                        <x-filament::icon :icon="$module['icon']" class="w-6 h-6 text-gray-400 dark:text-gray-500" />
                    </div>
                    <span class="text-xs font-semibold text-gray-400 dark:text-gray-500 leading-tight">{{ $module['name'] }}</span>
                    <span class="text-[10px] text-gray-300 dark:text-gray-600 mt-1 leading-tight">{{ $module['description'] }}</span>
                </div>
                @endif
                @endforeach
            </div>
        </div>
        @endforeach
    </div>
</x-filament-panels::page>
