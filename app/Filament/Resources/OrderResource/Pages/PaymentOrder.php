<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Models\Order;
use App\Models\OrderItemProductStock;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\Setting;
use Filament\Resources\Pages\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\ToggleButtons;
use Filament\Notifications\Notification;

class PaymentOrder extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static string $resource = OrderResource::class;

    protected static string $view = 'filament.resources.order-resource.pages.payment-order';

    public ?Order $record = null;

    public ?array $data = [];

    public function mount($record): void
    {
        // $this->record = OrderResource::resolveRecordRouteBinding($record);
        
        // if (! $this->record || $this->record->status !== 'pending') {
        //     abort(403);
        // }

        $this->record = $record;

        $this->form->fill([
            'amount' => $this->record->total_price,
            'method' => $this->record?->payment?->method,
            'proof_image' => $this->record?->payment?->proof_image,
            'notes' => $this->record?->payment?->notes,
            'payment_date' => $this->record?->payment?->payment_date,
            'status' => $this->record?->payment?->status,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(4)
                    ->schema([
                        Placeholder::make('amount')
                            ->label('Kode Pesanan')
                            ->content($this->record->order_code)
                            ->extraAttributes(['style' => 'font-size: 1.5rem; font-weight: bold;']),
                        Placeholder::make('amount')
                            ->label('Total Pembayaran')
                            ->content("Rp " . number_format($this->record->total_price, 0, ',', '.'))
                            ->extraAttributes(['style' => 'font-size: 1.5rem; font-weight: bold;']),
                        Placeholder::make('order_status')
                            ->label('Status Pesanan')
                            ->content($this->record?->status)
                            ->extraAttributes(['style' => 'font-size: 1.5rem; font-weight: bold; text-transform: capitalize;']),
                        Placeholder::make('payment_status')
                            ->label('Status Pembayaran')
                            ->content($this->record?->payment?->status ?? 'Menunggu Pembayaran')
                            ->extraAttributes(['style' => 'font-size: 1.5rem; font-weight: bold; text-transform: capitalize;']),
                    ]),
                Select::make('method')
                    ->label('Metode Pembayaran')
                    ->options([
                        'BCA' => 'BCA',
                        'Mandiri' => 'Mandiri',
                        'BNI' => 'BNI',
                        'BRI' => 'BRI',
                        'Other' => 'Bank Lainnya',
                    ])
                    ->required()
                    ->disabled(auth()->user()->isAdmin()),
                DatePicker::make('payment_date')
                    ->label('Tanggal Pembayaran')
                    ->required()
                    ->default(now())
                    ->disabled(auth()->user()->isAdmin()),
                Placeholder::make('account_number')
                    ->label('Nomor Rekening')
                    ->content("Harap transfer ke rekening " . Setting::where('type', 'bank_name')->first()->value . " " . Setting::where('type', 'account_number')->first()->value . " atas nama " . Setting::where('type', 'account_name')->first()->value)
                    ->extraAttributes(['style' => 'font-weight: bold; font-size: 1.1rem; text-transform: capitalize;'])
                    ->columnSpanFull(),
                FileUpload::make('proof_image')
                    ->label('Upload Bukti Pembayaran')
                    ->disk('public')
                    ->directory('payment-proofs')
                    ->openable()
                    ->downloadable()
                    ->image()
                    ->disabled(auth()->user()->isAdmin())
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/jpg'])
                    ->helperText('Upload bukti pembayaran dalam format JPG, JPEG, atau PNG.')
                    ->columnSpanFull(),
                ToggleButtons::make('status')
                    ->label('Status Pembayaran')
                    ->options([
                        'pending' => 'Pending',
                        'confirmed' => 'Dikonfirmasi',
                        'rejected' => 'Ditolak',
                    ])
                    ->colors([
                        'pending' => 'warning',
                        'confirmed' => 'success',
                        'rejected' => 'danger',
                    ])
                    ->icons([
                        'pending' => 'heroicon-o-clock',
                        'confirmed' => 'heroicon-o-check',
                        'rejected' => 'heroicon-o-x-mark',
                    ])
                    ->default('pending')
                    ->inline()
                    ->grouped()
                    ->disabled( ! auth()->user()->isAdmin())
                    ->hidden( ! auth()->user()->isAdmin() && $this->record?->payment?->status == null),
                Textarea::make('notes')
                    ->label('Catatan dari Admin')
                    ->columnSpanFull()
                    ->hidden( ! auth()->user()->isAdmin() && $this->record?->payment?->notes == null)
                    ->readonly( ! auth()->user()->isAdmin()),
            ])
            ->columns(2)
            ->statePath('data');
    }

    public function submit()
    {
        $data = $this->form->getState();

        $order = Order::find($this->record->id);
            if($order) {
                $order->update([
                    'status' => 'paid',
                ]);
            }

        $payment = Payment::where('order_id', $this->record->id)->first();

        if( ! $payment) {           
            $payment = Payment::create([
                'order_id' => $this->record->id,
                'amount' => $this->record->total_price,
                'method' => $this->data['method'],
                'proof_image' => $data['proof_image'],
                'payment_date' => $this->data['payment_date'],
                'notes' => $this->data['notes'] ?? null,
            ]);
        } else {
            $payment->update([
                'method' => $this->data['method'],
                'proof_image' => ! auth()->user()->isAdmin() ? $data['proof_image'] : $payment->proof_image,
                'payment_date' => $this->data['payment_date'],
                'notes' => $this->data['notes'],
                'status' => auth()->user()->isAdmin() ? $this->data['status'] : 'pending',
            ]);

            if($this->data['status'] == 'confirmed') {
                $payment->order->orderItems()->each(function ($item) {
                    $products = $item->product->available_stock->orderBy('stock_in_date', 'asc')->limit($item->quantity)->get();

                    $products->each(function ($product) use ($item) {
                        OrderItemProductStock::create([
                            'order_item_id' => $item->id,
                            'product_stock_id' => $product->id
                        ]);

                        $product->update([
                            'stock_out_date' => now()
                        ]);
                    });
                });
            }
        }

        return redirect()->route('filament.admin.resources.orders.index');
    }
}
