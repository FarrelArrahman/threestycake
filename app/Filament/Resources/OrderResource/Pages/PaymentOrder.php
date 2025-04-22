<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Models\Order;
use App\Models\Payment;
use Filament\Resources\Pages\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
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
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Placeholder::make('amount')
                    ->label('Total Pembayaran')
                    ->content("Rp " . number_format($this->record->total_price, 0, ',', '.'))
                    ->columnSpanFull(),
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
                FileUpload::make('proof_image')
                    ->label('Upload Bukti Pembayaran')
                    ->disk('public')
                    ->directory('payment-proofs')
                    ->openable()
                    ->downloadable()
                    ->image()
                    ->required()
                    ->disabled(auth()->user()->isAdmin()),
                DatePicker::make('payment_date')
                    ->label('Tanggal Pembayaran')
                    ->required()
                    ->default(now())
                    ->disabled(auth()->user()->isAdmin()),
                Textarea::make('notes')
                    ->label('Catatan dari Admin')
                    ->columnSpanFull()
                    ->hidden( ! auth()->user()->isAdmin() && $this->record?->payment?->notes == null)
                    ->readonly( ! auth()->user()->isAdmin()),
            ])
            ->statePath('data');
    }

    public function submit()
    {
        $data = $this->form->getState();

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
                'proof_image' => $data['proof_image'],
                'payment_date' => $this->data['payment_date'],
                'notes' => $this->data['notes'],
                'status' => 'pending',
            ]);
        }

        $this->record->update(['status' => 'paid']);

        Notification::make()
            ->title('Pembayaran berhasil dikirim!')
            ->success()
            ->send();

        return redirect()->route('filament.admin.resources.orders.index');
    }
}
