<?php $__env->startSection('title', 'Raport Siswa - ' . config('app.name')); ?>

<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-gray-50">

    
    <div class="bg-white border-b shadow-sm">
        <div class="max-w-6xl mx-auto px-4 py-5 flex items-center justify-between gap-3 flex-wrap">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Raport Siswa</h1>
                <p class="text-sm text-gray-500 mt-0.5">Rekap nilai per kelas & semester sepanjang riwayat belajar siswa</p>
            </div>
            <?php if($selectedSiswa && $raportData->isNotEmpty()): ?>
            <a href="<?php echo e(route('akademik.raport.pdf', $selectedSiswa->id)); ?>"
               data-no-loader target="_blank" rel="noopener"
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-lg font-semibold text-sm shadow transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                </svg>
                Export PDF
            </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="max-w-6xl mx-auto px-4 py-6 space-y-6">

        
        <?php
            $siswasJson = $siswas->map(fn($s) => [
                'id'    => $s->id,
                'label' => $s->nama . ($s->nis ? ' (' . $s->nis . ')' : ''),
                'nama'  => $s->nama,
                'nis'   => $s->nis ?? '',
            ])->values();
            $inputValue = $selectedSiswa
                ? $selectedSiswa->nama . ($selectedSiswa->nis ? ' (' . $selectedSiswa->nis . ')' : '')
                : '';
        ?>

        <div class="bg-white rounded-xl shadow p-6" style="overflow: visible;">
            <p class="text-sm font-semibold text-gray-800 mb-4">Pilih Siswa</p>
            <div class="relative" id="siswa-search-wrapper">
                <label class="block text-xs font-medium text-gray-500 mb-1.5">Cari Nama / NIS Siswa</label>
                
                <div id="siswa-input-wrapper"
                     class="flex items-center border border-gray-300 rounded-lg overflow-hidden
                            focus-within:ring-2 focus-within:ring-blue-400 focus-within:border-blue-400 transition">
                    <input
                        type="text"
                        id="siswa-search-input"
                        autocomplete="off"
                        placeholder="Ketik nama atau NIS siswa..."
                        value="<?php echo e($inputValue); ?>"
                        class="flex-1 px-4 py-2.5 text-sm bg-transparent outline-none min-w-0"
                    >
                    <button type="button" id="siswa-search-clear"
                            class="<?php echo e($inputValue !== '' ? 'flex' : 'hidden'); ?>

                                   items-center justify-center mr-2 w-5 h-5 rounded-full flex-shrink-0
                                   bg-gray-200 hover:bg-gray-300 text-gray-500 transition"
                            title="Hapus pilihan">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                  d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>


                
                <div id="siswa-dropdown"
                     class="hidden absolute z-50 w-full mt-1.5 bg-white border border-gray-200
                            rounded-xl shadow-2xl max-h-72 overflow-y-auto divide-y divide-gray-50">
                </div>
                <p id="siswa-no-result" class="hidden mt-2 text-xs text-gray-400 pl-1">
                    Tidak ada siswa yang cocok.
                </p>

                <?php if($selectedSiswa): ?>
                <p class="mt-2 text-xs text-blue-500">
                    ✓ Menampilkan raport untuk <strong><?php echo e($selectedSiswa->nama); ?></strong>
                </p>
                <?php else: ?>
                <p class="mt-2 text-xs text-gray-400">Mulai ketik untuk mencari siswa...</p>
                <?php endif; ?>
            </div>
        </div>

        <script>
        (function () {
            const siswas   = <?php echo json_encode($siswasJson, 15, 512) ?>;
            const input    = document.getElementById('siswa-search-input');
            const dropdown = document.getElementById('siswa-dropdown');
            const noResult = document.getElementById('siswa-no-result');
            const clearBtn = document.getElementById('siswa-search-clear');
            const baseUrl  = '<?php echo e(route("akademik.raport.index")); ?>';

            function renderItems(items) {
                dropdown.innerHTML = '';
                if (!items.length) {
                    dropdown.classList.add('hidden');
                    noResult.classList.remove('hidden');
                    return;
                }
                noResult.classList.add('hidden');
                items.forEach(s => {
                    const div = document.createElement('div');
                    div.className = 'flex items-center gap-3 px-4 py-2.5 cursor-pointer hover:bg-blue-50 transition';
                    div.innerHTML =
                        `<span class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 flex items-center
                                      justify-center text-sm font-bold flex-shrink-0">
                             ${s.nama.charAt(0).toUpperCase()}
                         </span>
                         <span class="min-w-0">
                             <span class="block font-medium text-gray-800 text-sm truncate">${s.nama}</span>
                             ${s.nis ? `<span class="block text-gray-400 text-xs">${s.nis}</span>` : ''}
                         </span>`;
                    div.addEventListener('mousedown', e => { e.preventDefault(); selectSiswa(s); });
                    dropdown.appendChild(div);
                });
                dropdown.classList.remove('hidden');
            }

            function selectSiswa(s) {
                input.value = s.label;
                dropdown.classList.add('hidden');
                noResult.classList.add('hidden');
                clearBtn.classList.remove('hidden');
                window.location.href = baseUrl + '?siswa_id=' + s.id;
            }

            clearBtn.addEventListener('click', () => {
                input.value = '';
                dropdown.classList.add('hidden');
                noResult.classList.add('hidden');
                clearBtn.classList.remove('flex');
                clearBtn.classList.add('hidden');
                input.focus();
                window.location.href = baseUrl;
            });

            input.addEventListener('input', function () {
                const q = this.value.trim().toLowerCase();
                if (q.length > 0) {
                    clearBtn.classList.remove('hidden');
                    clearBtn.classList.add('flex');
                } else {
                    clearBtn.classList.remove('flex');
                    clearBtn.classList.add('hidden');
                }
                if (!q) { dropdown.classList.add('hidden'); noResult.classList.add('hidden'); return; }
                renderItems(siswas.filter(s =>
                    s.nama.toLowerCase().includes(q) || s.nis.toLowerCase().includes(q)
                ).slice(0, 25));
            });

            input.addEventListener('focus', function () {
                const q = this.value.trim().toLowerCase();
                if (q.length >= 1) {
                    renderItems(siswas.filter(s =>
                        s.nama.toLowerCase().includes(q) || s.nis.toLowerCase().includes(q)
                    ).slice(0, 25));
                }
            });

            input.addEventListener('keydown', e => {
                if (e.key === 'Escape') { dropdown.classList.add('hidden'); noResult.classList.add('hidden'); }
            });

            document.addEventListener('click', e => {
                if (!document.getElementById('siswa-search-wrapper').contains(e.target)) {
                    dropdown.classList.add('hidden');
                    noResult.classList.add('hidden');
                }
            });
        })();
        </script>

        
        <?php if($selectedSiswa): ?>
        <div class="bg-white rounded-xl shadow p-5">
            <div class="flex items-center gap-4">
                <?php if($selectedSiswa->foto): ?>
                <img src="<?php echo e(asset('storage/' . $selectedSiswa->foto)); ?>" class="w-14 h-14 rounded-full object-cover border-2 border-indigo-100" alt="Foto">
                <?php else: ?>
                <div class="w-14 h-14 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold text-xl">
                    <?php echo e(mb_substr($selectedSiswa->nama, 0, 1)); ?>

                </div>
                <?php endif; ?>
                <div class="flex-1 grid grid-cols-2 md:grid-cols-4 gap-3 text-sm">
                    <div>
                        <p class="text-gray-500 text-xs">Nama Siswa</p>
                        <p class="font-bold text-gray-900"><?php echo e($selectedSiswa->nama); ?></p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-xs">NIS</p>
                        <p class="font-semibold text-gray-800"><?php echo e($selectedSiswa->nis ?? '-'); ?></p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-xs">Kelas Saat Ini</p>
                        <p class="font-semibold text-gray-800"><?php echo e($selectedSiswa->kelas?->nama_kelas ?? '-'); ?></p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-xs">Jenis Kelamin</p>
                        <p class="font-semibold text-gray-800"><?php echo e($selectedSiswa->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan'); ?></p>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        
        <?php if($selectedSiswa): ?>
            <?php if($raportData->isEmpty()): ?>
            <div class="bg-white rounded-xl shadow p-10 text-center text-gray-400">
                <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <p class="font-semibold text-gray-500">Belum ada data nilai untuk siswa ini.</p>
            </div>
            <?php else: ?>
            
            <?php $__currentLoopData = $raportData->reverse()->values(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kelasGroup): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="bg-white rounded-xl shadow overflow-hidden">
                
                <div class="px-5 py-4 flex items-center justify-between gap-2"
                     style="background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-white/20 flex items-center justify-center text-white font-bold text-lg">
                            <?php echo e($kelasGroup['tingkat'] <= 0 ? '?' : $kelasGroup['tingkat']); ?>

                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-white"><?php echo e($kelasGroup['nama_kelas']); ?></h2>
                            <p class="text-xs text-indigo-200">Tahun Ajaran: <?php echo e($kelasGroup['tahun_ajaran']); ?></p>
                        </div>
                    </div>
                    <?php
                        $statusLabel = match($kelasGroup['status'] ?? '') {
                            'naik'       => ['label' => 'Naik Kelas', 'color' => 'bg-green-400/90 text-white'],
                            'tidak_naik' => ['label' => 'Tidak Naik', 'color' => 'bg-red-400/90 text-white'],
                            'lulus'      => ['label' => 'Lulus', 'color' => 'bg-yellow-300/90 text-gray-900'],
                            'aktif'      => ['label' => 'Aktif', 'color' => 'bg-blue-300/90 text-white'],
                            default      => ['label' => '-', 'color' => 'bg-gray-300/90 text-gray-700'],
                        };
                    ?>
                    <span class="px-3 py-1 rounded-full text-xs font-bold <?php echo e($statusLabel['color']); ?>">
                        <?php echo e($statusLabel['label']); ?>

                    </span>
                </div>

                
                <div class="grid grid-cols-1 md:grid-cols-2 divide-x divide-gray-100">
                    <?php $__currentLoopData = [1, 2]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $semesterNomor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php $semData = $kelasGroup['per_semester'][$semesterNomor]; ?>
                    <div class="p-0">
                        
                        <div class="px-4 py-2.5 bg-gray-50 border-b flex items-center justify-between">
                            <span class="text-sm font-bold text-gray-700"><?php echo e($semData['semester_nama']); ?></span>
                            <?php if($semData['avg_nilai'] !== null): ?>
                            <span class="text-xs font-semibold px-2 py-0.5 rounded-full
                                <?php echo e($semData['avg_grade'] === 'A' ? 'bg-green-100 text-green-700' :
                                   ($semData['avg_grade'] === 'B' ? 'bg-blue-100 text-blue-700' :
                                   ($semData['avg_grade'] === 'C' ? 'bg-yellow-100 text-yellow-700' :
                                   'bg-red-100 text-red-700'))); ?>">
                                Rata-rata: <?php echo e(number_format($semData['avg_nilai'], 2, ',', '.')); ?>

                                (<?php echo e($semData['avg_grade']); ?>)
                            </span>
                            <?php else: ?>
                            <span class="text-xs text-gray-400">Belum ada nilai</span>
                            <?php endif; ?>
                        </div>

                        
                        <?php if($semData['mapel_rows']->isNotEmpty()): ?>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="text-xs text-gray-500 border-b">
                                        <th class="px-3 py-2 text-left font-semibold">Mata Pelajaran</th>
                                        <th class="px-2 py-2 text-center font-semibold">Tugas</th>
                                        <th class="px-2 py-2 text-center font-semibold">UTS</th>
                                        <th class="px-2 py-2 text-center font-semibold">UAS</th>
                                        <th class="px-2 py-2 text-center font-semibold">Akhir</th>
                                        <th class="px-2 py-2 text-center font-semibold">Grade</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $semData['mapel_rows']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mRow): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr class="border-b last:border-0 hover:bg-gray-50 transition">
                                        <td class="px-3 py-2 font-medium text-gray-800 text-xs"><?php echo e($mRow['mapel_nama']); ?></td>
                                        <td class="px-2 py-2 text-center text-gray-700 text-xs">
                                            <?php echo e($mRow['nilai_tugas'] !== null ? number_format((float)$mRow['nilai_tugas'], 1, ',', '.') : '-'); ?>

                                        </td>
                                        <td class="px-2 py-2 text-center text-gray-700 text-xs">
                                            <?php echo e($mRow['nilai_uts'] !== null ? number_format((float)$mRow['nilai_uts'], 1, ',', '.') : '-'); ?>

                                        </td>
                                        <td class="px-2 py-2 text-center text-gray-700 text-xs">
                                            <?php echo e($mRow['nilai_uas'] !== null ? number_format((float)$mRow['nilai_uas'], 1, ',', '.') : '-'); ?>

                                        </td>
                                        <td class="px-2 py-2 text-center font-semibold text-gray-900 text-xs">
                                            <?php echo e($mRow['nilai_akhir'] !== null ? number_format((float)$mRow['nilai_akhir'], 2, ',', '.') : '-'); ?>

                                        </td>
                                        <td class="px-2 py-2 text-center text-xs">
                                            <?php if($mRow['grade']): ?>
                                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full font-bold text-xs
                                                <?php echo e($mRow['grade'] === 'A' ? 'bg-green-100 text-green-700' :
                                                   ($mRow['grade'] === 'B' ? 'bg-blue-100 text-blue-700' :
                                                   ($mRow['grade'] === 'C' ? 'bg-yellow-100 text-yellow-700' :
                                                   'bg-red-100 text-red-700'))); ?>">
                                                <?php echo e($mRow['grade']); ?>

                                            </span>
                                            <?php else: ?>
                                            <span class="text-gray-400">-</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                        <?php else: ?>
                        <div class="px-4 py-6 text-center text-gray-400 text-sm">
                            Belum ada nilai pada semester ini
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>
        <?php else: ?>
        
        <div class="bg-white rounded-xl shadow p-12 text-center text-gray-400">
            <svg class="w-16 h-16 mx-auto mb-4 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            <p class="font-semibold text-gray-400 text-lg">Pilih siswa terlebih dahulu</p>
            <p class="text-sm text-gray-400 mt-1">Gunakan form di atas untuk mencari dan memilih nama siswa</p>
        </div>
        <?php endif; ?>

    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\sriwijaya_kidss\resources\views/akademik/raport/index.blade.php ENDPATH**/ ?>