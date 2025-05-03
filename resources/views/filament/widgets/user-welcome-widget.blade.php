<x-filament::widget>
    <x-filament::card>
        <div class="space-y-2">
            <h2 class="text-2xl font-bold">Halo, {{ auth()->user()->name }}! 👋</h2>
            <p>Selamat datang di Threestycake!</p>

            <div class="grid grid-cols-2 gap-4 mt-4">
                <div class="p-4 bg-primary-50 rounded-lg">
                    <p class="text-sm text-gray-600">Total Pesanan</p>
                    <p class="text-2xl font-bold text-primary-700">{{ $orderCount }}</p>
                </div>
                <div class="p-4 bg-warning-50 rounded-lg">
                    <p class="text-sm text-gray-600">Menunggu Pembayaran</p>
                    <p class="text-2xl font-bold text-warning-700">{{ $pendingOrders }}</p>
                </div>
            </div>
        </div>
    </x-filament::card>

    <br>

    <x-filament::card>
        <div class="space-y-2">
            <h6 class="font-bold">Untuk informasi lebih lanjut, silahkan hubungi kami melalui: </h6>
            <ul>
                <li>Alamat: {{ $settings->where('type', 'address')->first()->value ?? '' }}</li>
                <li>Telepon: <a href="https://wa.me/{{ $settings->where('type', 'phone')->first()->value ?? '' }}" class="text-primary-500" target="_blank">+{{ $settings->where('type', 'phone')->first()->value ?? '' }}</a></li>
                <li>Instagram: <a href="https://www.instagram.com/{{ $settings->where('type', 'instagram')->first()->value ?? '' }}" class="text-primary-500" target="_blank">https://www.instagram.com/{{ $settings->where('type', 'instagram')->first()->value ?? '' }}</a></li>
            </ul>
        </div>
    </x-filament::card>
</x-filament::widget>
