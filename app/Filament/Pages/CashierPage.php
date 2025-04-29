<?php

namespace App\Filament\Pages;

use App\Enum\PaymentMethodType;
use App\Models\CartItem;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Traits\InteractsWithCart;
use Filament\Facades\Filament;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Pages\Page;
use Filament\Support\RawJs;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Number;
use Throwable;

class CashierPage extends Page
{
    use InteractsWithCart;
    use InteractsWithFormActions;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    protected static string $view = 'filament.pages.cashier-page';

    protected static ?string $slug = 'cashier';

    protected static ?string $title = 'Kasir';

    public ?array $data = [];

    public array $cart = [];

    public float $subtotal = 0;

    public float $tax = 0;

    public float $total = 0;

    public $paymentMethods;

    public array $paymentMethodsMap = [];

    protected $listeners = [
        'add-to-cart' => 'addToCart',
    ];

    public function mount(): void
    {
        $this->paymentMethods = PaymentMethod::select('id', 'name', 'type')->get();
        $this->paymentMethodsMap = $this->paymentMethods->pluck('type', 'id')->toArray();

        $this->refreshCart();
        $this->form->fill();
    }

    public function refreshCart(): void
    {
        $this->cart = CartItem::query()
            ->cashier()
            ->with('product:id,name,price,stock')
            ->get()
            ->toArray();

        $this->subtotal = collect($this->cart)->sum('price');

        $currentTaxRate = $this->getCurrentTaxRate();
        $this->tax = $this->subtotal * $currentTaxRate;

        $this->total = $this->subtotal + $this->tax;
    }

    public function form(Form $form): Form
    {
        return $form
            ->statePath('data')
            ->schema([
                Section::make('keranjang')
                    ->heading('Keranjang')
                    ->headerActions([
                        Action::make('edit')
                            ->label('Hapus Keranjang')
                            ->color('danger')
                            ->requiresConfirmation()
                            ->action(fn () => $this->clearCart())
                            ->visible(fn () => ! empty($this->cart)),
                    ])
                    ->footerActions([
                        Action::make('checkout')
                            ->label('Checkout')
                            ->color('success')
                            ->action('checkout')
                            ->disabled(fn () => empty($this->cart))
                            ->extraAttributes(['class' => 'w-full']),
                    ])
                    ->schema([
                        Placeholder::make('cart_items')
                            ->hiddenLabel()
                            ->content(function () {
                                return view('filament.partials.cart-display', ['cart' => $this->cart]);
                            }),
                        Group::make()
                            ->extraAttributes(['class' => 'border-y dark:border-gray-700 py-4 [&_div]:gap-2'])
                            ->schema([
                                Placeholder::make('subtotal_display')
                                    ->label('Subtotal')
                                    ->inlineLabel()
                                    ->content(fn () => Number::currency($this->subtotal, 'IDR', 'id', 0))
                                    ->extraAttributes(['class' => 'text-right']),
                                Placeholder::make('tax_display')
                                    ->label(fn () => 'Pajak ('.($this->getCurrentTaxRate() * 100).'%)')
                                    ->inlineLabel()
                                    ->content(fn () => Number::currency($this->tax, 'IDR', 'id', 0))
                                    ->extraAttributes(['class' => 'text-right'])
                                    ->visible(setting('app.tax_feature', false)),
                                Placeholder::make('total_display')
                                    ->label('Total')
                                    ->inlineLabel()
                                    ->content(fn () => Number::currency($this->total, 'IDR', 'id', 0))
                                    ->extraAttributes(['class' => 'font-semibold text-right !text-base']),
                                Placeholder::make('change_due_display')
                                    ->label('Kembalian')
                                    ->inlineLabel()
                                    ->content(function (Get $get) {
                                        $amountPaid = (float) str_replace(['.', ','], '', $get('amount_paid') ?? '0');
                                        $change = max(0, $amountPaid - $this->total);

                                        return Number::currency($change, 'IDR', 'id');
                                    })
                                    ->visible(fn (Get $get) => isset($this->paymentMethodsMap[$get('payment_method_id')]) &&
                                        $this->paymentMethodsMap[$get('payment_method_id')] === PaymentMethodType::CASH->value
                                    )
                                    ->extraAttributes(['class' => 'text-right']),
                            ]),
                        Select::make('payment_method_id')
                            ->label('Metode Pembayaran')
                            ->options($this->paymentMethods->pluck('name', 'id'))
                            ->searchable()
                            ->required()
                            ->live(),
                        TextInput::make('amount_paid')
                            ->label('Jumlah Diterima')
                            ->prefix('Rp.')
                            ->numeric()
                            ->required(fn (Get $get) => isset($this->paymentMethodsMap[$get('payment_method_id')]) &&
                                $this->paymentMethodsMap[$get('payment_method_id')] === PaymentMethodType::CASH->value)
                            ->minValue(fn (Get $get): float => isset($this->paymentMethodsMap[$get('payment_method_id')]) &&
                                $this->paymentMethodsMap[$get('payment_method_id')] === PaymentMethodType::CASH->value ? $this->total : 0)
                            ->mask(RawJs::make('$money($input, \',\', \'.\')'))
                            ->stripCharacters(['.'])
                            ->live(onBlur: true)
                            ->visible(fn (Get $get) => isset($this->paymentMethodsMap[$get('payment_method_id')]) &&
                                $this->paymentMethodsMap[$get('payment_method_id')] === PaymentMethodType::CASH->value),
                    ]),
            ]);
    }

    public function checkout(): void
    {
        if (empty($this->cart)) {
            Notification::make()
                ->title('Keranjang Kosong')
                ->body('Tidak dapat checkout dengan keranjang kosong.')
                ->danger()
                ->send();

            return;
        }

        // Last refresh to ensure tax rate are getting applied correctly.
        $this->refreshCart();

        try {
            $validatedData = $this->form->getState();
        } catch (ValidationException $e) {
            Notification::make()
                ->title('Validasi Gagal')
                ->body('Silakan periksa input pada form.')
                ->danger()
                ->send();

            return;
        }

        $paymentMethodId = $validatedData['payment_method_id'];
        $isCash = isset($this->paymentMethodsMap[$paymentMethodId]) && $this->paymentMethodsMap[$paymentMethodId] === PaymentMethodType::CASH->value;
        $amountPaid = null;
        $changeDue = null;

        if ($isCash) {
            $amountPaid = (float) str_replace(['.', ','], '', $validatedData['amount_paid'] ?? '0');
            $changeDue = max(0, $amountPaid - $this->total);

            if ($amountPaid < $this->total) {
                Notification::make()
                    ->title('Jumlah Diterima Kurang')
                    ->body('Jumlah uang yang diterima kurang dari total belanja.')
                    ->danger()
                    ->send();

                return;
            }
        }

        try {
            DB::transaction(function () use ($paymentMethodId, $amountPaid, $changeDue) {
                $sale = Sale::create([
                    'user_id' => Filament::auth()->id(),
                    'payment_method_id' => $paymentMethodId,
                    'subtotal' => $this->subtotal,
                    'tax' => $this->tax,
                    'total' => $this->total,
                    'amount_paid' => $amountPaid,
                    'change_given' => $changeDue,
                ]);

                $saleItemsData = [];
                $stockUpdates = [];

                foreach ($this->cart as $cartItem) {
                    $saleItemsData[] = [
                        'sale_id' => $sale->id,
                        'product_id' => $cartItem['product_id'],
                        'quantity' => 1,
                        'price_at_sale' => $cartItem['price'],
                        'subtotal' => $cartItem['price'],
                    ];

                    if (setting('app.stock_feature')) {
                        $stockUpdates[] = [
                            'product_id' => $cartItem['product_id'],
                            'quantity' => $cartItem['quantity'],
                        ];
                    }
                }

                if (! empty($saleItemsData)) {
                    SaleItem::insert($saleItemsData);
                }

                if (! empty($stockUpdates) && setting('app.stock_feature', false)) {
                    foreach ($stockUpdates as $update) {
                        Product::where('id', $update['product_id'])
                            ->decrement('stock', $update['quantity']);
                    }
                }

                CartItem::query()->cashier()->delete();
            });

            Notification::make()
                ->title('Checkout Berhasil')
                ->success()
                ->send();

            $this->refreshCart();
            $this->form->fill();

        } catch (Throwable $e) {
            report($e);

            Notification::make()
                ->title('Checkout Gagal')
                ->body('Terjadi kesalahan saat menyimpan transaksi. Silakan coba lagi.')
                ->danger()
                ->send();
        }
    }

    protected function getCurrentTaxRate(): float
    {
        // Use ?? 0 as a fallback if setting is null or not found
        return (float) (setting('app.tax_rate', 0) ?? 0) / 100;
    }
}
