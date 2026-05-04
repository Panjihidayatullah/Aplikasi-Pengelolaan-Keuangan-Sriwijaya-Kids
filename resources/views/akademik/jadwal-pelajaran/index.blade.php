@extends('layouts.app')

@section('title', 'Jadwal Pelajaran')

@section('content')
<div id="jadwalPage" class="container mx-auto px-4 py-6">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Jadwal Pelajaran</h1>
            <p class="text-gray-600 mt-1">
                @if($isSiswaScope)
                    Jadwal pelajaran untuk kelas Anda.
                @elseif($isGuruScope)
                    Jadwal mengajar Anda.
                @else
                    Kelola jadwal kelas, mapel, dan guru.
                @endif
            </p>
        </div>
        <div class="inline-flex items-center gap-2">
            <a
                href="{{ route('akademik.jadwal-pelajaran.export.pdf', ['mode' => 'preview']) }}"
                target="_blank"
                rel="noopener noreferrer"
                class="btn-hover-glow inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-semibold transition"
            >
                Preview PDF
            </a>
            <a
                href="{{ route('akademik.jadwal-pelajaran.export.pdf') }}"
                class="btn-hover-glow inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-semibold transition"
            >
                Unduh PDF
            </a>
            @if($isAdmin)
            <a href="{{ route('akademik.jadwal-pelajaran.create') }}" class="btn-hover-glow inline-flex items-center px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold transition">
                Tambah Jadwal
            </a>
            @endif
        </div>
    </div>

    @php
        $daysToRender = collect($hariOptions);
    @endphp

    <style>
        .jadwal-slider::-webkit-scrollbar {
            display: none;
        }

        .jadwal-slide {
            width: 100%;
            flex: 0 0 100%;
        }

        #jadwalPage .btn-hover-glow {
            transition: transform 0.2s ease, box-shadow 0.2s ease, filter 0.2s ease;
            will-change: transform, box-shadow;
        }

        #jadwalPage .btn-hover-glow:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 22px rgba(15, 23, 42, 0.2), 0 0 0 3px rgba(59, 130, 246, 0.18);
            filter: brightness(1.03);
        }

        #jadwalPage .btn-hover-glow:disabled,
        #jadwalPage .btn-hover-glow[aria-disabled='true'] {
            transform: none;
            box-shadow: none;
            filter: none;
        }
    </style>

    @if(!$isSiswaScope)
    <div class="mb-6">
        <div class="mb-3 flex items-center justify-between gap-3">
            <h2 class="text-lg font-bold text-gray-800">Pilih Kelas</h2>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            @forelse($kelasCards as $kelasCard)
            <div
                data-kelas-id="{{ $kelasCard->id }}"
                data-kelas-nama="{{ $kelasCard->nama }}"
                class="jadwal-kelas-box text-left rounded-xl border border-gray-200 bg-white hover:border-blue-300 hover:shadow-sm p-4 transition cursor-pointer"
                role="button"
                tabindex="0"
            >
                <p class="font-bold text-gray-800">{{ $kelasCard->nama }}</p>
                <p class="text-sm text-gray-600 mt-1">{{ $kelasCardCounts[$kelasCard->id] ?? 0 }} jadwal</p>
                <div class="mt-3 inline-flex items-center gap-2">
                    <button
                        type="button"
                        data-kelas-jadwal-link
                        class="btn-hover-glow inline-flex items-center px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-semibold"
                    >
                        Lihat Jadwal
                    </button>
                    <button
                        type="button"
                        data-kelas-siswa-link
                        class="btn-hover-glow inline-flex items-center px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-semibold"
                    >
                        Lihat Daftar Siswa
                    </button>
                </div>
            </div>
            @empty
            <div class="col-span-full rounded-lg border border-dashed border-gray-300 bg-white px-4 py-6 text-sm text-gray-500">
                Tidak ada data kelas saat ini.
            </div>
            @endforelse
        </div>
    </div>
    @endif

    @if($isSiswaScope)
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
        <p class="text-sm text-blue-800">
            Menampilkan jadwal untuk kelas Anda:
            <span class="font-semibold">{{ $kelasSiswa ?: '-' }}</span>
        </p>
    </div>
    @endif

    @if($isSiswaScope)
    <div class="mb-3 flex items-center justify-between">
        <button id="jadwalPrevBtn" type="button" class="btn-hover-glow px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg font-semibold text-sm">Sebelumnya</button>
        <p id="jadwalSliderCounter" class="text-sm font-semibold text-gray-700">1 / {{ $daysToRender->count() }}</p>
        <button id="jadwalNextBtn" type="button" class="btn-hover-glow px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold text-sm">Berikutnya</button>
    </div>

    <div id="jadwalDaySlider" class="jadwal-slider flex overflow-x-auto snap-x snap-mandatory scroll-smooth" style="scrollbar-width: none; -ms-overflow-style: none;">
        @foreach($daysToRender as $hari)
        @php
            $jadwalHari = $groupedJadwal->get($hari, collect());
        @endphp
        <section class="jadwal-slide snap-start bg-white rounded-lg shadow overflow-hidden" data-day-slide>
            <div class="px-6 py-4 border-b bg-gray-50">
                <h2 class="text-lg font-bold text-gray-800">{{ $hari }}</h2>
            </div>

            @if($jadwalHari->isEmpty())
            <div class="px-6 py-6 text-sm text-gray-500">Belum ada jadwal pada hari ini.</div>
            @else
            <div class="overflow-x-auto">
                <table class="w-full min-w-[640px]">
                    <thead class="bg-gray-100 border-b">
                        <tr>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-800">Jam</th>
                            @if(!$isSiswaScope)
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-800">Kelas</th>
                            @endif
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-800">Mata Pelajaran</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-800">Guru</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-800">Ruang</th>
                            @if(!$isSiswaScope)
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-800">Status</th>
                            @if($isAdmin)
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-800">Aksi</th>
                            @endif
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($jadwalHari as $item)
                        <tr class="border-b hover:bg-gray-50 transition">
                            <td class="px-6 py-4 text-gray-700">{{ substr((string) $item->jam_mulai, 0, 5) }} - {{ substr((string) $item->jam_selesai, 0, 5) }}</td>
                            @if(!$isSiswaScope)
                            <td class="px-6 py-4 text-gray-700">{{ $item->kelas->nama ?? '-' }}</td>
                            @endif
                            <td class="px-6 py-4 text-gray-700 font-semibold">
                                @if($item->is_istirahat)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">Ishoma / Istirahat</span>
                                @else
                                {{ $item->mataPelajaran->nama ?? '-' }}
                                @endif
                            </td>
                            <td class="px-6 py-4 text-gray-700">{{ $item->is_istirahat ? '-' : ($item->guru->nama ?? '-') }}</td>
                            <td class="px-6 py-4 text-gray-700">{{ $item->is_istirahat ? '-' : ($item->ruangan->nama ?? $item->ruang ?? '-') }}</td>
                            @if(!$isSiswaScope)
                            <td class="px-6 py-4">
                                @if($item->is_active)
                                <span class="inline-block px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold">Aktif</span>
                                @else
                                <span class="inline-block px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-xs font-semibold">Nonaktif</span>
                                @endif
                            </td>
                            @if($isAdmin)
                            <td class="px-6 py-4">
                                <div class="inline-flex items-center gap-3 whitespace-nowrap">
                                    <a href="{{ route('akademik.jadwal-pelajaran.edit', $item) }}" class="text-yellow-500 hover:text-yellow-700 font-semibold text-sm">Edit</a>
                                    <form method="POST" action="{{ route('akademik.jadwal-pelajaran.destroy', $item) }}" class="inline-flex" onsubmit="return confirm('Yakin ingin menghapus jadwal ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700 font-semibold text-sm">Hapus</button>
                                    </form>
                                </div>
                            </td>
                            @endif
                            @endif
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </section>
        @endforeach
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const slider = document.getElementById('jadwalDaySlider');
            if (!slider) return;

            const slides = slider.querySelectorAll('[data-day-slide]');
            const totalSlides = slides.length;
            const prevBtn = document.getElementById('jadwalPrevBtn');
            const nextBtn = document.getElementById('jadwalNextBtn');
            const counter = document.getElementById('jadwalSliderCounter');

            const getActiveIndex = () => {
                if (!slider.clientWidth) return 0;
                const index = Math.round(slider.scrollLeft / slider.clientWidth);
                return Math.max(0, Math.min(index, totalSlides - 1));
            };

            const updateControls = () => {
                const index = getActiveIndex();
                if (counter) {
                    counter.textContent = (index + 1) + ' / ' + totalSlides;
                }
                if (prevBtn) prevBtn.disabled = index === 0;
                if (nextBtn) nextBtn.disabled = index >= totalSlides - 1;
            };

            const slideTo = (index) => {
                slider.scrollTo({
                    left: index * slider.clientWidth,
                    behavior: 'smooth',
                });
            };

            prevBtn?.addEventListener('click', function () {
                slideTo(getActiveIndex() - 1);
            });

            nextBtn?.addEventListener('click', function () {
                slideTo(getActiveIndex() + 1);
            });

            let ticking = false;
            slider.addEventListener('scroll', function () {
                if (!ticking) {
                    window.requestAnimationFrame(function () {
                        updateControls();
                        ticking = false;
                    });
                    ticking = true;
                }
            });

            window.addEventListener('resize', updateControls);
            updateControls();
        });
    </script>
    @else
    <div class="rounded-lg border border-dashed border-gray-300 bg-white px-5 py-8 text-center text-gray-500">
        Klik box kelas untuk menampilkan popup jadwal dan slide per hari.
    </div>
    @endif

    @if(!$isSiswaScope)
    <div id="jadwalKelasModal" class="fixed inset-0 z-50 hidden">
        <div id="jadwalKelasModalOverlay" class="absolute inset-0 bg-slate-900/35 backdrop-blur-sm"></div>
        <div class="absolute inset-0 p-2 md:p-6 lg:p-10 flex items-center justify-center">
            <div class="mx-auto max-h-[88vh] bg-white rounded-xl shadow-2xl overflow-hidden" style="width:min(96vw,1230px);">
                <div class="px-5 py-4 border-b bg-gray-50 flex items-center justify-between gap-3">
                    <h3 id="jadwalKelasModalTitle" class="text-lg md:text-xl font-bold text-gray-800">Jadwal Kelas</h3>
                    <div class="inline-flex items-center gap-2">
                        <button id="jadwalKelasShowJadwalBtn" type="button" class="btn-hover-glow px-3 py-1.5 bg-blue-600 hover:bg-blue-700 rounded-lg text-sm font-semibold text-white">Jadwal</button>
                        <button id="jadwalKelasShowStudentsBtn" type="button" class="btn-hover-glow px-3 py-1.5 bg-gray-200 hover:bg-gray-300 rounded-lg text-sm font-semibold text-gray-700">Daftar Siswa</button>
                        <button id="jadwalKelasModalClose" type="button" class="btn-hover-glow px-3 py-1.5 bg-gray-200 hover:bg-gray-300 rounded-lg text-sm font-semibold text-gray-700">Tutup</button>
                    </div>
                </div>

                <div class="p-5 md:p-6 overflow-y-auto max-h-[calc(88vh-72px)]">
                    <div id="jadwalKelasJadwalPanel">
                        <div class="mb-3 flex items-center justify-between">
                            <button id="jadwalKelasPrevBtn" type="button" class="btn-hover-glow px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg font-semibold text-sm">Sebelumnya</button>
                            <p id="jadwalKelasSliderCounter" class="text-sm font-semibold text-gray-700">1 / 1</p>
                            <button id="jadwalKelasNextBtn" type="button" class="btn-hover-glow px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold text-sm">Berikutnya</button>
                        </div>

                        <div id="jadwalKelasDaySlider" class="jadwal-slider flex overflow-x-auto snap-x snap-mandatory scroll-smooth" style="scrollbar-width: none; -ms-overflow-style: none;">
                        </div>
                    </div>

                    <div id="jadwalKelasSiswaPanel" class="hidden">
                        <div class="mb-3 flex items-center justify-between gap-3">
                            <p id="jadwalKelasStudentsSummary" class="text-sm text-gray-600"></p>
                            <div class="inline-flex items-center gap-2">
                                <button id="jadwalKelasStudentsPrevBtn" type="button" class="btn-hover-glow px-3 py-1.5 bg-gray-200 hover:bg-gray-300 rounded-lg text-sm font-semibold text-gray-700">Sebelumnya</button>
                                <p id="jadwalKelasStudentsCounter" class="text-sm font-semibold text-gray-700">1 / 1</p>
                                <button id="jadwalKelasStudentsNextBtn" type="button" class="btn-hover-glow px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 rounded-lg text-sm font-semibold text-white">Berikutnya</button>
                            </div>
                        </div>
                        <div class="overflow-x-auto border border-gray-200 rounded-lg">
                            <table class="w-full min-w-[720px]">
                                <thead class="bg-gray-100 border-b">
                                    <tr>
                                        <th class="px-5 py-3 text-left text-sm font-semibold text-gray-800">No</th>
                                        <th class="px-5 py-3 text-left text-sm font-semibold text-gray-800">NIS</th>
                                        <th class="px-5 py-3 text-left text-sm font-semibold text-gray-800">Nama Siswa</th>
                                        <th class="px-5 py-3 text-left text-sm font-semibold text-gray-800">Jenis Kelamin</th>
                                        <th class="px-5 py-3 text-left text-sm font-semibold text-gray-800">Status</th>
                                    </tr>
                                </thead>
                                <tbody id="jadwalKelasStudentsBody"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const classBoxes = document.querySelectorAll('.jadwal-kelas-box');
            if (!classBoxes.length) return;

            const modal = document.getElementById('jadwalKelasModal');
            const overlay = document.getElementById('jadwalKelasModalOverlay');
            const closeBtn = document.getElementById('jadwalKelasModalClose');
            const titleEl = document.getElementById('jadwalKelasModalTitle');
            const showJadwalBtn = document.getElementById('jadwalKelasShowJadwalBtn');
            const showStudentsBtn = document.getElementById('jadwalKelasShowStudentsBtn');
            const jadwalPanel = document.getElementById('jadwalKelasJadwalPanel');
            const studentsPanel = document.getElementById('jadwalKelasSiswaPanel');
            const studentsSummary = document.getElementById('jadwalKelasStudentsSummary');
            const studentsPrevBtn = document.getElementById('jadwalKelasStudentsPrevBtn');
            const studentsNextBtn = document.getElementById('jadwalKelasStudentsNextBtn');
            const studentsCounter = document.getElementById('jadwalKelasStudentsCounter');
            const studentsBody = document.getElementById('jadwalKelasStudentsBody');
            const slider = document.getElementById('jadwalKelasDaySlider');
            const prevBtn = document.getElementById('jadwalKelasPrevBtn');
            const nextBtn = document.getElementById('jadwalKelasNextBtn');
            const counter = document.getElementById('jadwalKelasSliderCounter');

            const scheduleData = @json($popupScheduleByClass);
            const studentsData = @json($popupStudentsByClass ?? []);
            const dayOptions = @json($hariOptions);

            let totalSlides = 0;
            const studentsPageSize = 10;
            let currentStudentsPage = 1;
            let activeStudentRows = [];

            const getActiveIndex = () => {
                if (!slider.clientWidth) return 0;
                const index = Math.round(slider.scrollLeft / slider.clientWidth);
                return Math.max(0, Math.min(index, Math.max(0, totalSlides - 1)));
            };

            const updateControls = () => {
                const index = getActiveIndex();
                if (counter) {
                    counter.textContent = totalSlides > 0 ? (index + 1) + ' / ' + totalSlides : '0 / 0';
                }
                if (prevBtn) prevBtn.disabled = index === 0;
                if (nextBtn) nextBtn.disabled = index >= totalSlides - 1;
            };

            const setActiveTab = (tab) => {
                const isJadwal = tab === 'jadwal';

                jadwalPanel?.classList.toggle('hidden', !isJadwal);
                studentsPanel?.classList.toggle('hidden', isJadwal);

                if (showJadwalBtn) {
                    showJadwalBtn.classList.toggle('bg-blue-600', isJadwal);
                    showJadwalBtn.classList.toggle('hover:bg-blue-700', isJadwal);
                    showJadwalBtn.classList.toggle('text-white', isJadwal);
                    showJadwalBtn.classList.toggle('bg-gray-200', !isJadwal);
                    showJadwalBtn.classList.toggle('hover:bg-gray-300', !isJadwal);
                    showJadwalBtn.classList.toggle('text-gray-700', !isJadwal);
                }

                if (showStudentsBtn) {
                    showStudentsBtn.classList.toggle('bg-indigo-600', !isJadwal);
                    showStudentsBtn.classList.toggle('hover:bg-indigo-700', !isJadwal);
                    showStudentsBtn.classList.toggle('text-white', !isJadwal);
                    showStudentsBtn.classList.toggle('bg-gray-200', isJadwal);
                    showStudentsBtn.classList.toggle('hover:bg-gray-300', isJadwal);
                    showStudentsBtn.classList.toggle('text-gray-700', isJadwal);
                }
            };

            const slideTo = (index) => {
                slider.scrollTo({
                    left: index * slider.clientWidth,
                    behavior: 'smooth',
                });
            };

            const escapeHtml = (value) => String(value ?? '')
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#039;');

            const renderSlides = (kelasId, kelasNama) => {
                const classSchedule = scheduleData[String(kelasId)] || scheduleData[kelasId] || {};
                const daysToRender = dayOptions;

                slider.innerHTML = daysToRender.map((day) => {
                    const rows = classSchedule[day] || [];

                    const bodyHtml = rows.length
                        ? rows.map((row) => `
                            <tr class="border-b hover:bg-gray-50 transition">
                                <td class="px-6 py-4 text-gray-700">${escapeHtml(row.jam_mulai)} - ${escapeHtml(row.jam_selesai)}</td>
                                <td class="px-6 py-4 text-gray-700 font-semibold">${escapeHtml(row.mata_pelajaran)}</td>
                                <td class="px-6 py-4 text-gray-700">${escapeHtml(row.guru)}</td>
                                <td class="px-6 py-4 text-gray-700">${escapeHtml(row.ruang)}</td>
                                <td class="px-6 py-4"><span class="inline-block px-3 py-1 ${row.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'} rounded-full text-xs font-semibold">${row.is_active ? 'Aktif' : 'Nonaktif'}</span></td>
                            </tr>
                        `).join('')
                        : `<tr><td colspan="5" class="px-6 py-6 text-sm text-gray-500">Belum ada jadwal pada hari ini.</td></tr>`;

                    return `
                        <section class="jadwal-slide snap-start bg-white rounded-lg border border-gray-200 overflow-hidden" data-popup-slide>
                            <div class="px-6 py-4 border-b bg-gray-50">
                                <h4 class="text-lg font-bold text-gray-800">${escapeHtml(day)}</h4>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full min-w-[845px]">
                                    <thead class="bg-gray-100 border-b">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-800">Jam</th>
                                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-800">Mata Pelajaran</th>
                                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-800">Guru</th>
                                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-800">Ruang</th>
                                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-800">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>${bodyHtml}</tbody>
                                </table>
                            </div>
                        </section>
                    `;
                }).join('');

                totalSlides = daysToRender.length;
                slider.scrollLeft = 0;
                titleEl.textContent = 'Jadwal Kelas: ' + kelasNama;
                updateControls();
            };

            const renderStudents = (kelasId) => {
                activeStudentRows = studentsData[String(kelasId)] || studentsData[kelasId] || [];
                currentStudentsPage = 1;

                const activeCount = activeStudentRows.filter((row) => row.is_active).length;

                if (studentsSummary) {
                    studentsSummary.textContent = 'Total siswa: ' + activeStudentRows.length + ' | Aktif: ' + activeCount + ' | Tidak aktif: ' + Math.max(0, activeStudentRows.length - activeCount);
                }

                renderStudentsPage();
            };

            const renderStudentsPage = () => {
                const totalRows = activeStudentRows.length;
                const totalPages = totalRows > 0 ? Math.ceil(totalRows / studentsPageSize) : 1;
                currentStudentsPage = Math.max(1, Math.min(currentStudentsPage, totalPages));

                if (!studentsBody) {
                    return;
                }

                if (!totalRows) {
                    studentsBody.innerHTML = '<tr><td colspan="5" class="px-5 py-6 text-sm text-gray-500 text-center">Belum ada siswa pada kelas ini.</td></tr>';
                    if (studentsCounter) {
                        studentsCounter.textContent = '0 / 0';
                    }
                    if (studentsPrevBtn) {
                        studentsPrevBtn.disabled = true;
                    }
                    if (studentsNextBtn) {
                        studentsNextBtn.disabled = true;
                    }
                    return;
                }

                const startIndex = (currentStudentsPage - 1) * studentsPageSize;
                const pageRows = activeStudentRows.slice(startIndex, startIndex + studentsPageSize);

                if (studentsCounter) {
                    studentsCounter.textContent = currentStudentsPage + ' / ' + totalPages;
                }

                if (studentsPrevBtn) {
                    studentsPrevBtn.disabled = currentStudentsPage <= 1;
                }

                if (studentsNextBtn) {
                    studentsNextBtn.disabled = currentStudentsPage >= totalPages;
                }

                studentsBody.innerHTML = pageRows.map((row, index) => {
                    const jenisKelamin = row.jenis_kelamin === 'L' ? 'Laki-laki' : (row.jenis_kelamin === 'P' ? 'Perempuan' : '-');
                    const statusClass = row.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800';
                    const statusLabel = row.is_active ? 'Aktif' : 'Nonaktif';

                    return `
                        <tr class="border-b hover:bg-gray-50 transition">
                            <td class="px-5 py-3 text-gray-700">${startIndex + index + 1}</td>
                            <td class="px-5 py-3 text-gray-700">${escapeHtml(row.nis)}</td>
                            <td class="px-5 py-3 text-gray-800 font-semibold">${escapeHtml(row.nama)}</td>
                            <td class="px-5 py-3 text-gray-700">${escapeHtml(jenisKelamin)}</td>
                            <td class="px-5 py-3"><span class="inline-block px-3 py-1 rounded-full text-xs font-semibold ${statusClass}">${statusLabel}</span></td>
                        </tr>
                    `;
                }).join('');
            };

            const openModal = () => {
                modal.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            };

            const closeModal = () => {
                modal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            };

            classBoxes.forEach((box) => {
                box.addEventListener('click', (event) => {
                    const kelasId = box.getAttribute('data-kelas-id');
                    const kelasNama = box.getAttribute('data-kelas-nama') || 'Kelas';
                    renderSlides(kelasId, kelasNama);
                    renderStudents(kelasId);
                    openModal();

                    if (event.target.closest('[data-kelas-siswa-link]')) {
                        setActiveTab('siswa');
                        return;
                    }

                    if (event.target.closest('[data-kelas-jadwal-link]')) {
                        setActiveTab('jadwal');
                        return;
                    }

                    setActiveTab('jadwal');
                });

                box.addEventListener('keydown', (event) => {
                    if (event.key !== 'Enter' && event.key !== ' ') {
                        return;
                    }

                    event.preventDefault();
                    const kelasId = box.getAttribute('data-kelas-id');
                    const kelasNama = box.getAttribute('data-kelas-nama') || 'Kelas';
                    renderSlides(kelasId, kelasNama);
                    renderStudents(kelasId);
                    openModal();
                    setActiveTab('jadwal');
                });
            });

            showJadwalBtn?.addEventListener('click', () => setActiveTab('jadwal'));
            showStudentsBtn?.addEventListener('click', () => setActiveTab('siswa'));
            studentsPrevBtn?.addEventListener('click', () => {
                currentStudentsPage -= 1;
                renderStudentsPage();
            });
            studentsNextBtn?.addEventListener('click', () => {
                currentStudentsPage += 1;
                renderStudentsPage();
            });

            prevBtn?.addEventListener('click', () => slideTo(getActiveIndex() - 1));
            nextBtn?.addEventListener('click', () => slideTo(getActiveIndex() + 1));
            closeBtn?.addEventListener('click', closeModal);
            overlay?.addEventListener('click', closeModal);

            slider.addEventListener('scroll', () => window.requestAnimationFrame(updateControls));
            window.addEventListener('resize', updateControls);
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
                    closeModal();
                }
            });
        });
    </script>
    @endif
</div>
@endsection
