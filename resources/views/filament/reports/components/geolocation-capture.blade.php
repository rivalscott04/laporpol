<div
    x-data="{
        status: 'idle',
        error: null,
        accuracy: null,
        capture() {
            if (! navigator.geolocation) {
                this.status = 'error';
                this.error = 'Browser tidak mendukung fitur lokasi.';
                return;
            }

            this.status = 'loading';
            this.error = null;

            navigator.geolocation.getCurrentPosition(
                (position) => {
                    $wire.set('data.latitude', position.coords.latitude.toFixed(7));
                    $wire.set('data.longitude', position.coords.longitude.toFixed(7));
                    this.accuracy = Math.round(position.coords.accuracy);
                    this.status = 'success';
                },
                (err) => {
                    this.status = 'error';
                    this.error = {
                        1: 'Izin lokasi ditolak. Aktifkan izin lokasi di pengaturan browser.',
                        2: 'Lokasi tidak tersedia. Pastikan GPS atau Wi-Fi aktif.',
                        3: 'Waktu habis saat mengambil lokasi. Coba lagi.',
                    }[err.code] ?? 'Gagal mengambil lokasi.';
                },
                { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 },
            );
        },
        init() {
            if (! $wire.data.latitude || ! $wire.data.longitude) {
                this.capture();
            }
        },
    }"
    class="fi-fo-field-wrp"
>
    <div class="flex flex-wrap items-center gap-3">
        <x-filament::button
            type="button"
            size="sm"
            color="primary"
            x-on:click="capture()"
            x-bind:disabled="status === 'loading'"
        >
            <span x-show="status !== 'loading'">Ambil Lokasi</span>
            <span x-show="status === 'loading'" x-cloak>Mengambil lokasi…</span>
        </x-filament::button>

        <span
            x-show="status === 'success'"
            x-cloak
            class="text-sm text-success-600 dark:text-success-400"
        >
            Lokasi berhasil diambil
            <template x-if="accuracy !== null">
                <span x-text="'(akurasi ±' + accuracy + ' m)'"></span>
            </template>
        </span>

        <span
            x-show="status === 'error'"
            x-cloak
            class="text-sm text-danger-600 dark:text-danger-400"
            x-text="error"
        ></span>
    </div>

    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
        Koordinat diisi otomatis dari browser. Di laptop akurasi bisa lebih rendah daripada GPS ponsel.
    </p>
</div>
