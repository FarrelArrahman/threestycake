<ul class="space-y-2 text-sm">
    @foreach ($order->orderItems as $item)
        <li class="border p-3 rounded shadow">
            <div><strong>Produk:</strong> {{ $item?->productStock?->product->name }}</div>
            <div><strong>Jumlah:</strong> {{ $item->quantity }}</div>
            <div><strong>Harga:</strong> Rp {{ number_format($item->price, 0, ',', '.') }}</div>
            <div><strong>Total:</strong> Rp {{ number_format($item->total, 0, ',', '.') }}</div>

            @if ($item->customizations && $item->customizations->count())
                <div class="mt-2">
                    <strong>Kustomisasi:</strong>
                    <ul class="list-disc list-inside">
                        @foreach ($item->customizations as $custom)
                            <li>
                                {{ $custom->productCustomization->customization_type }} -
                                Rp {{ number_format($custom->price, 0, ',', '.') }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </li>
    @endforeach
</ul>
