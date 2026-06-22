<?php

namespace App\Livewire\Orders;

use App\Mail\sendOrderConfirmed;
use App\Mail\sendOrderConfirmedByRietpanel;
use App\Mail\sendOrderList;
use App\Mail\sendOrderListConfirmation;
use App\Models\Order;
use App\Models\OrderRules;
use App\Services\PricingServices;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Livewire\Component;

class EditOrders extends Component
{
    public $orderId;
    public $order;

    public $priceRules = [
        [
            'rule' => '',
            'price' => '',
            'show_orderlist' => false,
        ],
    ];

    public $currentModal = 'first';
    public $delivery_date;

    public $existing_purchage_order_email;
    public $existing_purchage_order_suplier;
    public $new_purchage_order_email;

    public $rietpanel_comment;

    public function mount()
    {
        if (! Auth::user()->is_admin) {
            session()->flash('error', 'U heeft geen rechten voor deze pagina');
            return $this->redirect('/dashboard', navigate: true);
        }

        $this->orderId = Route::current()->parameter('id');

        $this->order = Order::with(['Suplier', 'orderLines'])->findOrFail($this->orderId);

        $this->existing_purchage_order_email = $this->order->Suplier->suplier_email;
        $this->existing_purchage_order_suplier = $this->order->Suplier->suplier_name;
        $this->new_purchage_order_email = $this->order->Suplier->suplier_email;
    }

    public function render()
    {
        return view('livewire.orders.editOrder');
    }

    public function rules()
    {
        return [
            'priceRules.*.rule' => 'required_with:priceRules.*.price',
            'priceRules.*.price' => 'required_with:priceRules.*.rule|nullable|numeric',
            'delivery_date' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'priceRules.*.rule.required_with' => 'De regel is verplicht als je een prijs invult.',
            'priceRules.*.price.required_with' => 'De prijs is verplicht als je een regel invult.',
            'priceRules.*.price.numeric' => 'De prijs mag alleen een nummer zijn.',
            'delivery_date.required' => 'Selecteer een leverdatum.',
        ];
    }

    public function addPriceRule()
    {
        $this->priceRules[] = [
            'rule' => '',
            'price' => '',
            'show_orderlist' => false,
        ];
    }

    public function removePriceRule($index)
    {
        unset($this->priceRules[$index]);
        $this->priceRules = array_values($this->priceRules);
    }

    public function NextModal($id)
    {
        $this->validate();

        $this->currentModal = 'next';
    }

    public function cancelNextModal()
    {
        $this->currentModal = 'first';
    }

    public function closeModal()
    {
        $this->currentModal = null;
    }

    public function updateOrder($id)
    {
        DB::transaction(function () {
            foreach ($this->priceRules as $priceRule) {
                $rule = trim($priceRule['rule'] ?? '');
                $price = $priceRule['price'] ?? null;

                if ($rule !== '' || $price !== null && $price !== '') {
                    OrderRules::create([
                        'order_id' => $this->order->id,
                        'rule' => $rule,
                        'price' => $price,
                        'show_orderlist' => $priceRule['show_orderlist'] ?? false,
                    ]);
                }
            }

            $this->order->status = 'Bevestigd';
            $this->order->rietpanel_comment = $this->rietpanel_comment;
            $this->order->delivery_date = $this->delivery_date;
            $this->order->order_ordered = now();
            $this->order->save();

            $freshOrder = Order::with([
                'user',
                'orderLines',
                'orderRules',
                'surcharges',
            ])->findOrFail($this->order->id);

            app(PricingServices::class)->updateDocumentPricing($freshOrder);
        });

        $order = Order::with([
            'Suplier',
            'orderRules',
            'user',
            'orderLines',
            'surcharges',
        ])->findOrFail($this->order->id);

        $orderLines = $order->orderLines;

        $showNokafschuining = $orderLines->where('nokafschuining', '>', 0)->count() > 0;
        $showVrijeRuimte = $orderLines->where('vrije_ruimte_2', '>', 0)->count() > 0;
        $showCb = $orderLines->where('fillCb', '>', 0)->count() > 0;
        $showLb = $orderLines->where('lb', '>', 0)->count() > 0;

        \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.orderlijst', [
            'order' => $order,
            'leverancier' => $order->Suplier,
        ])->save(public_path('/storage/orderlijst/order-' . $order->order_id . '.pdf'));

        \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.order', [
            'order' => $order,
            'orderLines' => $orderLines,
            'showNokafschuining' => $showNokafschuining,
            'showLb' => $showLb,
            'showCb' => $showCb,
            'showVrijeRuimte' => $showVrijeRuimte,
        ])->save(public_path('/storage/orders/order-' . $order->order_id . '.pdf'));

        try {
            Mail::to(strtolower($this->new_purchage_order_email))
                ->send(new sendOrderList($order));
        } catch (\Exception $e) {
            return redirect('/orders')->with('error', 'Er is een fout opgetreden bij het versturen van de e-mail naar de leverancier. ' . $e->getMessage());
        }

        try {
            Mail::to(['inkoop@rietpanel.nl'])
                ->send(new sendOrderListConfirmation($order, $this->new_purchage_order_email));

            Mail::to(['orders@rietpanel.nl'])
                ->send(new sendOrderConfirmedByRietpanel($order));
        } catch (\Exception $e) {
            return redirect('/orders')->with('error', 'Er is een fout opgetreden bij het versturen van de e-mail naar de administratie. ' . $e->getMessage());
        }

        try {
            Mail::to($order->user->email)
                ->send(new sendOrderConfirmed($order));
        } catch (\Exception $e) {
            return redirect('/orders')->with('error', 'Er is een fout opgetreden bij het versturen van de e-mail naar de klant. ' . $e->getMessage());
        }

        session()->flash(
            'success',
            'De order #' . $order->order_id . ' is bevestigd. Er is een email verstuurd met een bevestiging naar ' . $order->user->email . '. De inkooporder is verstuurd naar inkoop@rietpanel.nl'
        );

        return $this->redirect('/productPlanning');
    }

    public function cancelUpdateOrder()
    {
        return $this->redirect('/orders', navigate: true);
    }
}
