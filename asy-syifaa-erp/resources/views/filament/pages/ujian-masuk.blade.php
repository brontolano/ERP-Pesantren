<x-filament-panels::page>
    <div class="max-w-lg mx-auto">
        <div class="rounded-2xl border-2 border-dashed border-gray-300 dark:border-gray-600 p-8 sm:p-12">
            <div class="text-center">
                <div class="mx-auto w-20 h-20 rounded-2xl bg-gradient-to-br from-emerald-100 to-emerald-50 dark:from-emerald-900/40 dark:to-emerald-900/20 flex items-center justify-center mb-6">
                    <x-heroicon-o-pencil-square class="w-10 h-10 text-emerald-500" />
                </div>
                <h2 class="text-2xl font-bold text-gray-700 dark:text-gray-300 mb-2">Ujian Masuk Online</h2>
                <p class="text-gray-500 dark:text-gray-400 mb-6 max-w-md mx-auto leading-relaxed">
                    Fitur ujian/tes masuk secara online akan segera tersedia. Anda akan mendapat notifikasi ketika jadwal ujian sudah ditentukan.
                </p>
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300 text-sm font-semibold">
                    <x-heroicon-o-clock class="w-4 h-4" />
                    Segera Hadir — Wave 2
                </div>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div class="flex items-center gap-3 p-4 rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                <div class="w-10 h-10 rounded-lg bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center flex-shrink-0">
                    <x-heroicon-o-document-text class="w-5 h-5 text-blue-500" />
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-700 dark:text-gray-300">Soal CBT</p>
                    <p class="text-[10px] text-gray-400">Computer Based Test</p>
                </div>
            </div>
            <div class="flex items-center gap-3 p-4 rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                <div class="w-10 h-10 rounded-lg bg-violet-50 dark:bg-violet-900/20 flex items-center justify-center flex-shrink-0">
                    <x-heroicon-o-clock class="w-5 h-5 text-violet-500" />
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-700 dark:text-gray-300">Timer Otomatis</p>
                    <p class="text-[10px] text-gray-400">Waktu terbatas</p>
                </div>
            </div>
            <div class="flex items-center gap-3 p-4 rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                <div class="w-10 h-10 rounded-lg bg-emerald-50 dark:bg-emerald-900/20 flex items-center justify-center flex-shrink-0">
                    <x-heroicon-o-chart-bar class="w-5 h-5 text-emerald-500" />
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-700 dark:text-gray-300">Hasil Instan</p>
                    <p class="text-[10px] text-gray-400">Nilai langsung tampil</p>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
