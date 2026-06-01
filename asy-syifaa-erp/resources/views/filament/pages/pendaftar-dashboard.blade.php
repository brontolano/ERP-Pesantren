<x-filament-panels::page>
    @if($registrations->isEmpty())
        <x-filament::section>
            <div class="text-center py-12">
                <div class="mx-auto w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center mb-4">
                    <x-heroicon-o-document-plus class="w-8 h-8 text-gray-400" />
                </div>
                <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-1">Belum Ada Pendaftaran</h3>
                <p class="text-sm text-gray-500">Hubungi panitia SPMB untuk mendaftar.</p>
            </div>
        </x-filament::section>
    @else
        {{-- Info Multi Anak --}}
        @if($registrations->count() > 1)
            <div class="rounded-xl bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 p-4 mb-4">
                <div class="flex items-center gap-2">
                    <x-heroicon-o-user-group class="w-5 h-5 text-blue-500" />
                    <p class="text-sm text-blue-700 dark:text-blue-300">
                        Anda memiliki <strong>{{ $registrations->count() }} calon santri</strong> yang terdaftar.
                    </p>
                </div>
            </div>
        @endif

        @foreach($registrations as $reg)
            {{-- ====== KARTU PESERTA PPDB ====== --}}
            <div class="rounded-2xl overflow-hidden shadow-xl mb-6 border border-gray-200 dark:border-gray-700">
                {{-- Header --}}
                <div class="bg-gradient-to-r from-emerald-700 via-emerald-600 to-emerald-500 px-4 sm:px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-white/20 flex items-center justify-center">
                                <x-heroicon-o-academic-cap class="w-6 h-6 text-white" />
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-emerald-100 uppercase tracking-wider">Kartu Peserta PPDB {{ substr($reg->academic_year, 0, 4) }}</p>
                                <p class="text-base font-bold text-white">Pondok Pesantren Asy-Syifaa</p>
                            </div>
                        </div>
                        @php
                            $verified = in_array($reg->status, ['lulus', 'enrolled']);
                            $badgeColor = $verified ? 'bg-emerald-400 text-emerald-900' : 'bg-yellow-400 text-yellow-900';
                            $badgeText = $verified ? 'TERVERIFIKASI' : strtoupper($reg->status_label);
                        @endphp
                        <span class="hidden sm:inline-flex px-3 py-1 rounded-full text-xs font-bold {{ $badgeColor }}">
                            {{ $badgeText }}
                        </span>
                    </div>
                </div>

                {{-- Body --}}
                <div class="bg-white dark:bg-gray-800 px-4 sm:px-6 py-6">
                    <div class="flex flex-col sm:flex-row gap-6">
                        {{-- Foto / Avatar --}}
                        <div class="flex-shrink-0 flex justify-center sm:justify-start">
                            @php
                                $user = auth('erp')->user();
                                $canViewPhoto = $reg->gender !== 'P' || ($user && $user->hasAnyRole(['Superadmin', 'Mudir']));
                            @endphp
                            <x-avatar-santri
                                :gender="$reg->gender"
                                :foto-url="$reg->foto_url"
                                :can-view-photo="$canViewPhoto"
                                :id="'reg-' . $reg->id"
                            />
                        </div>

                        {{-- Data Peserta --}}
                        <div class="flex-1 text-center sm:text-left">
                            <div class="mb-4">
                                <p class="text-xs text-emerald-600 dark:text-emerald-400 uppercase font-semibold tracking-wide">Nama Lengkap</p>
                                <h2 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">{{ $reg->student_name }}</h2>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <p class="text-xs text-gray-400 uppercase font-semibold">No. Registrasi</p>
                                    <p class="text-sm sm:text-lg font-mono font-bold text-gray-800 dark:text-gray-200">{{ $reg->registration_number }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-400 uppercase font-semibold">Jenis Kelamin</p>
                                    <p class="text-sm sm:text-lg font-semibold text-gray-800 dark:text-gray-200">{{ $reg->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}</p>
                                </div>
                            </div>
                            <p class="mt-3 text-xs text-gray-500 dark:text-gray-400">VALIDITY: 31 DEC {{ substr($reg->academic_year, 0, 4) }}</p>

                            {{-- Mobile badge --}}
                            <span class="sm:hidden mt-3 inline-flex px-3 py-1 rounded-full text-xs font-bold {{ $badgeColor }}">
                                {{ $badgeText }}
                            </span>
                        </div>

                        {{-- QR Code --}}
                        @php
                            $verifyUrl = url('/verifikasi/' . $reg->registration_number);
                            $qrOptions = new \chillerlan\QRCode\QROptions([
                                'outputType' => \chillerlan\QRCode\Output\QROutputInterface::MARKUP_SVG,
                                'outputBase64' => false,
                                'svgUseCssProperties' => false,
                                'drawLightModules' => false,
                                'addQuietzone' => true,
                                'scale' => 5,
                                'cssClass' => '',
                            ]);
                            $qrSvg = (new \chillerlan\QRCode\QRCode($qrOptions))->render($verifyUrl);
                        @endphp
                        <div class="hidden sm:flex flex-col items-center gap-1 flex-shrink-0">
                            <div class="w-28 h-28 bg-white rounded-xl border-2 border-gray-200 dark:border-gray-600 flex items-center justify-center p-1.5">
                                {!! $qrSvg !!}
                            </div>
                            <p class="text-[10px] text-gray-500 dark:text-gray-400 font-medium">Scan untuk verifikasi</p>
                        </div>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="bg-gradient-to-r from-emerald-700 to-emerald-600 px-4 sm:px-6 py-2.5">
                    <p class="text-xs text-emerald-100 font-semibold tracking-wider uppercase text-center sm:text-left">
                        Digital Identity System &bull; Asy-Syifaa Edu
                    </p>
                </div>
            </div>

            {{-- ====== STATUS CARDS ====== --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                {{-- Status Pendaftaran --}}
                <div class="rounded-xl bg-white dark:bg-gray-800 p-5 border border-gray-200 dark:border-gray-700 shadow-sm">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 rounded-lg bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center">
                            <x-heroicon-o-clipboard-document-check class="w-5 h-5 text-emerald-600" />
                        </div>
                        <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">Status Pendaftaran</p>
                    </div>
                    <span @class([
                        'inline-flex items-center px-3 py-1.5 rounded-full text-sm font-semibold',
                        'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300' => in_array($reg->status, ['lulus', 'enrolled']),
                        'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/50 dark:text-yellow-300' => in_array($reg->status, ['pending', 'cadangan']),
                        'bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300' => in_array($reg->status, ['document_review', 'selection']),
                        'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300' => $reg->status === 'rejected',
                    ])>
                        {{ $reg->status_label }}
                    </span>
                </div>

                {{-- Dokumen Progress --}}
                <div class="rounded-xl bg-white dark:bg-gray-800 p-5 border border-gray-200 dark:border-gray-700 shadow-sm">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 rounded-lg bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center">
                            <x-heroicon-o-document-text class="w-5 h-5 text-blue-600" />
                        </div>
                        <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">Kelengkapan Dokumen</p>
                    </div>
                    <div class="flex items-end gap-2">
                        <span class="text-3xl font-bold text-emerald-600">{{ $reg->approved_docs }}</span>
                        <span class="text-gray-400 text-lg pb-0.5">/{{ $totalDocs }}</span>
                    </div>
                    <div class="mt-2 w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5 overflow-hidden">
                        <div class="bg-emerald-500 h-2.5 rounded-full transition-all duration-700 ease-out" style="width: {{ $reg->doc_pct }}%"></div>
                    </div>
                    @if(!empty($reg->missing_docs))
                        <p class="text-xs text-amber-600 dark:text-amber-400 mt-2">
                            {{ count($reg->missing_docs) }} dokumen belum di-upload
                        </p>
                    @else
                        <p class="text-xs text-emerald-600 dark:text-emerald-400 mt-2">Semua dokumen sudah ter-upload</p>
                    @endif
                </div>

                {{-- Ujian --}}
                <div class="rounded-xl bg-white dark:bg-gray-800 p-5 border border-gray-200 dark:border-gray-700 shadow-sm opacity-50">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 rounded-lg bg-amber-50 dark:bg-amber-900/30 flex items-center justify-center">
                            <x-heroicon-o-pencil-square class="w-5 h-5 text-amber-600" />
                        </div>
                        <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">Tes/Ujian Masuk</p>
                    </div>
                    <p class="text-sm font-medium text-gray-500">Belum Tersedia</p>
                    <p class="text-xs text-gray-400 mt-1">Segera hadir di Wave 2</p>
                </div>
            </div>
        @endforeach

        {{-- ====== CHECKLIST DOKUMEN ====== --}}
        @php $firstReg = $registrations->first(); @endphp
        @if($firstReg && !empty($firstReg->missing_docs))
            <x-filament::section icon="heroicon-o-exclamation-triangle" icon-color="warning">
                <x-slot name="heading">Dokumen Wajib Belum Ter-upload</x-slot>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    @foreach($firstReg->missing_docs as $docLabel)
                        <div class="flex items-center gap-2 p-2 rounded-lg bg-amber-50 dark:bg-amber-900/10">
                            <x-heroicon-o-document-arrow-up class="w-4 h-4 text-amber-500 flex-shrink-0" />
                            <span class="text-sm text-amber-700 dark:text-amber-300">{{ $docLabel }}</span>
                        </div>
                    @endforeach
                </div>
            </x-filament::section>
        @endif

        {{-- ====== INFORMASI BIAYA ====== --}}
        <x-filament::section icon="heroicon-o-currency-dollar" collapsible collapsed>
            <x-slot name="heading">Informasi Biaya Pendaftaran</x-slot>
            <div class="space-y-3">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800">
                        <p class="text-xs text-emerald-600 dark:text-emerald-400 font-semibold uppercase">Biaya Daftar Ulang</p>
                        <p class="text-2xl font-bold text-emerald-700 dark:text-emerald-300">Rp {{ number_format(config('spmb.total_registration_fee', 7000000), 0, ',', '.') }}</p>
                        <p class="text-xs text-emerald-500 mt-1">Termasuk perlengkapan santri</p>
                    </div>
                    <div class="p-4 rounded-xl bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800">
                        <p class="text-xs text-blue-600 dark:text-blue-400 font-semibold uppercase">SPP Bulanan</p>
                        <p class="text-2xl font-bold text-blue-700 dark:text-blue-300">Rp {{ number_format(config('spmb.first_month_spp', 750000), 0, ',', '.') }}</p>
                        <p class="text-xs text-blue-500 mt-1">Makan & laundry</p>
                    </div>
                </div>
                @if(config('spmb.registration_costs'))
                    <div class="p-3 rounded-lg bg-gray-50 dark:bg-gray-800 text-sm">
                        <p class="font-semibold text-gray-700 dark:text-gray-300 mb-2">Rincian Biaya Daftar Ulang:</p>
                        <div class="space-y-1">
                            @foreach(config('spmb.registration_costs', []) as $cost)
                                <div class="flex justify-between text-gray-600 dark:text-gray-400">
                                    <span>{{ $cost['name'] }}</span>
                                    <span class="font-mono">{{ $cost['amount'] > 0 ? 'Rp ' . number_format($cost['amount'], 0, ',', '.') : '-' }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </x-filament::section>

        {{-- ====== TIMELINE PPDB ====== --}}
        <x-filament::section icon="heroicon-o-calendar-days">
            <x-slot name="heading">Jadwal / Timeline SPMB</x-slot>
            <div class="relative">
                <div class="absolute left-4 top-0 bottom-0 w-0.5 bg-emerald-200 dark:bg-emerald-800"></div>
                <div class="space-y-6">
                    @foreach($timeline as $i => $step)
                        <div class="relative flex items-start gap-4 pl-10">
                            <div class="absolute left-2 w-5 h-5 rounded-full border-2 border-emerald-500 bg-white dark:bg-gray-800 flex items-center justify-center" style="top:2px;">
                                <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $step['kegiatan'] }}</p>
                                <p class="text-xs text-gray-500">{{ $step['tanggal'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </x-filament::section>

        {{-- ====== KEBIJAKAN ====== --}}
        <x-filament::section icon="heroicon-o-shield-check" collapsible collapsed>
            <x-slot name="heading">Pernyataan & Kebijakan Pesantren</x-slot>
            <ol class="list-decimal list-inside space-y-2 text-sm text-gray-700 dark:text-gray-300">
                @foreach(config('spmb.pernyataan_kebijakan', []) as $item)
                    <li>{{ $item }}</li>
                @endforeach
            </ol>
        </x-filament::section>
    @endif
</x-filament-panels::page>
