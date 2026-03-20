<?php

namespace App\Livewire\ProductPlanning;

use App\Models\KerndikteColor;
use App\Models\Order;
use App\Models\OrderLines;
use App\Models\OrderPlanning;
use App\Models\ProductPlanningSetting;
use App\Services\OrderPdfService;
use Barryvdh\DomPDF\PDF;
use Livewire\Component;

class ProductPlanning extends Component
{
    public $unplannedOrders;
    public $settings;

    public $pendingPlanningId;
    public $pendingDate;

    public $limitExceededOrder;
    public $limitExceededDate;

    public $blockedDates = [];

    public $showBlockedModal = false;
    public $editingBlockedDate;
    public $editingBlockedTitle;

    public $limitExceededAmount;

    public $blockedDays = [];
    public $max_m2_per_day;

    public $coreThicknessColors;
    public $coreThickness;

    public $printStartDate;
    public $printEndDate;


    protected $listeners = [
        'plan-order' => 'planOrder',
        'unplanOrder' => 'unplanOrder',
        'move-planning' => 'movePlanning',
        'confirm-plan-order' => 'confirmPlanOrder',
            'addBlockedDay' => 'addBlockedDay',
        'moveBlockedDay' => 'moveBlockedDay',
        'removeBlockedDay' => 'removeBlockedDay',
        'editBlockedDay' => 'editBlockedDay',
        'mergeOrderParts' => 'mergeOrderParts',
        ];

    protected $actionLocked = false;

    protected $casts = [
        'blocked_days' => 'array',
    ];


    public function mount()
    {
        $this->settings = ProductPlanningSetting::first() ?? ProductPlanningSetting::create([
            'max_m2_per_day' => 50,
            'blocked_days' => ['zaterdag','zondag'],
            'blocked_dates' => []
        ]);

        $this->blockedDates = collect($this->settings->blocked_dates)
            ->map(fn($item) => is_array($item) ? $item : ['date'=>$item,'title'=>'Geblokkeerd'])
            ->values()
            ->toArray();

        $this->unplannedOrders = Order::whereNull('planned_start')->where('status', 'Bevestigd')->get();

        $this->blockedDays = is_array($this->settings->blocked_days)
            ? $this->settings->blocked_days
            : json_decode($this->settings->blocked_days, true) ?? [];



        $this->max_m2_per_day = $this->settings->max_m2_per_day;

        $this->coreThickness = KerndikteColor::all();

        foreach ($this->coreThickness as $k) {
            $this->coreThicknessColors[$k->id] = $k->color;
        }
    }

    public function render()
    {
        return view('livewire.productPlanning.productPlanning');
    }

    public function saveSettings()
    {
        // 1️⃣ Update kerndikte kleuren
        foreach ($this->coreThicknessColors as $id => $color) {
            KerndikteColor::where('id', $id)->update([
                'color' => $color
            ]);
        }

        $this->settings->update([
            'blocked_days' => $this->blockedDays,
            'max_m2_per_day' => $this->max_m2_per_day,
        ]);

        // 3️⃣ Feedback + modal sluiten
        $this->dispatch('hideSettingModal');
        session()->flash('success', 'Instellingen opgeslagen!');

        return redirect('productPlanning');
    }

    /*---------------------------------------
    | ORDERS VOOR FULLCALENDAR
    ----------------------------------------*/
    public function getOrders()
    {
    $orders = Order::whereHas('planning')
        ->with('planning')
        ->get()
        ->flatMap(fn($order) => $order->planning->map(fn($plan) => [
            'id' => $plan->id,
            'title' => $order->klantnaam.' '.$order->project_naam.' ('.$plan->planned_m2.' m²)',
            'start' => $plan->planned_date,
            'backgroundColor' => 'red',
            'borderColor' => $order->kerndikte_color,
            'extendedProps' => [
                'type' => 'order',
                'originalId' => $order->id,
                'planning_id' => $plan->id,
                'order_id' => $order->id,
                'planned_m2' => $plan->planned_m2,
                'title' => $order->klantnaam.' '.$order->project_naam.' ('.$plan->planned_m2.' m²)',// ✅ hier het echte getal gebruiken
            ]
        ]));

    return $orders->merge($this->getBlockedDaysEvents());
}

    public function checkLimit($order)
    {
        // huidige totaal m² voor de dag
        $currentM2 = $this->getDayM2($this->limitExceededDate);

        // m² van de order
        $orderM2 = $order->orderLines->sum('m2');

        // bereken overschrijding
        $this->limitExceededAmount = ($currentM2 + $orderM2) - $this->settings->max_m2_per_day;

        // alleen positieve overschrijding tonen
        if ($this->limitExceededAmount < 0) {
            $this->limitExceededAmount = 0;
        }

        // open de modal
        $this->dispatch('showLimitModal');
    }

    public function getBlockedDaysEvents()
    {
        return collect($this->blockedDates)->map(fn($b) => [
            'id' => 'block-'.$b['date'],
            'title' => $b['title'],
            'start' => $b['date'],
            'allDay' => true,
            'backgroundColor' => '#dc3545',
            'borderColor' => '#dc3545',
            'extendedProps' => [
                'type' => 'manual-block',
                'originalId' => 'block-'.$b['date']
            ]
        ]);
    }

    public function events()
    {
        return response()->json(app(ProductPlanning::class)->getOrders());
    }

    /*---------------------------------------
    | PLANNING
    ----------------------------------------*/
    public function planOrder($orderId, $date)
    {

        $order = Order::with('orderLines')->findOrFail($orderId);
        $orderM2 = $order->orderLines->sum('m2');

        if ($this->isBlockedDay($date)) {
            $this->dispatch('showBlockedDayAlert');
            return;
        }

        $current = $this->getDayM2($date);

        if (($current + $orderM2) > $this->settings->max_m2_per_day) {
            $this->limitExceededOrder = $orderId;
            $this->limitExceededDate = $date;
            $this->checkLimit($order);


            return;
        }



        $this->createPlanning($orderId, $date, $orderM2);
    }

    public function showSettingModal() {

        $this->dispatch('showSettingModal');
    }

    public function movePlanning($planningIds, $targetDate)
    {

        if (!$planningIds || !$targetDate) return;

        $planningIds = is_array($planningIds) ? $planningIds : [$planningIds];
        $movingPlans = OrderPlanning::whereIn('id', $planningIds)->get();
        if ($movingPlans->isEmpty()) return;

        $limit = $this->settings->max_m2_per_day;

        foreach ($movingPlans as $plan) {
            $orderId = $plan->order_id;
            $m2 = $plan->planned_m2;

            // --- Merge als er al een split bestaat op target dag ---
            $existing = OrderPlanning::where('order_id', $orderId)
                ->where('planned_date', $targetDate)
                ->first();

            if ($existing && $existing->id != $plan->id && !$this->actionLocked) {
                $existing->planned_m2 += $m2;
                $existing->save();
                $plan->delete();

                // ✅ Verwijder order uit ongeplande orders
                $this->unplannedOrders = $this->unplannedOrders
                    ->reject(fn($o) => $o->id == $orderId)
                    ->values();

                continue;
            }

            // --- Limiet check voor andere orders ---
            $otherOrdersM2 = OrderPlanning::where('planned_date', $targetDate)
                ->where('order_id', '!=', $orderId)
                ->sum('planned_m2');

            if (($otherOrdersM2 + $m2) > $limit || $this->isBlockedDay($targetDate)) {
                $this->pendingPlanningId = $plan->id;
                $this->pendingDate = $targetDate;
                $this->dispatch('showLimitModal');
                continue;
            }

            // Anders gewoon verplaatsen
            $plan->planned_date = $targetDate;
            $plan->save();

            // ✅ Verwijder order uit ongeplande orders
            $this->unplannedOrders = $this->unplannedOrders
                ->reject(fn($o) => $o->id == $orderId)
                ->values();
        }

        // Update startdatum van orders
        $orderIds = $movingPlans->pluck('order_id')->unique();
        foreach ($orderIds as $id) {
            $firstDate = OrderPlanning::where('order_id', $id)
                ->orderBy('planned_date')
                ->value('planned_date');
            Order::where('id', $id)->update(['planned_start' => $firstDate]);
        }

        $this->dispatch('ordersUpdated', ['orders' => $this->getOrders()]);
    }



    /*---------------------------------------
    | MODAL ACTIES
    ----------------------------------------*/
    public function confirmPlanOrder($action)
    {
        if ($this->actionLocked) return;
        $this->actionLocked = true;

        $limit = $this->settings->max_m2_per_day;

        /*
        |--------------------------------------------------------------------------
        | 1. NIEUWE ORDER (vanuit planOrder)
        |--------------------------------------------------------------------------
        */
        if ($this->limitExceededOrder) {

            $order = Order::with('orderLines')->findOrFail($this->limitExceededOrder);
            $totalM2 = $order->orderLines->sum('m2');

            if ($action === 'sameDay') {
                // 🔥 Force op dezelfde dag
                $this->createPlanning($order->id, $this->limitExceededDate, $totalM2);
            }

            elseif ($action === 'nextDay') {

                if ($totalM2 > $limit) {
                    // grote order → splitsen vanaf volgende dag
                    $nextDay = date('Y-m-d', strtotime($this->limitExceededDate . ' +1 day'));
                    $this->planSplit($order->id, $nextDay);
                } else {
                    // kleine order → eerstvolgende plek
                    $next = $this->getNextAvailableDayForOrder(
                        $this->limitExceededDate,
                        $order->id,
                        $totalM2
                    );
                    $this->createPlanning($order->id, $next, $totalM2);
                }
            }

            elseif ($action === 'split') {
                // 🔥 altijd volledige order splitsen
                $this->planSplit($order->id, $this->limitExceededDate);
            }

            $this->reset(['limitExceededOrder', 'limitExceededDate']);
        }

        /*
        |--------------------------------------------------------------------------
        | 2. BESTAANDE PLANNING (drag & drop)
        |--------------------------------------------------------------------------
        */
        elseif ($this->pendingPlanningId && $this->pendingDate) {

            $planningIds = is_array($this->pendingPlanningId)
                ? $this->pendingPlanningId
                : [$this->pendingPlanningId];

            $movingPlans = OrderPlanning::whereIn('id', $planningIds)->get();

            if ($movingPlans->isEmpty()) {
                $this->actionLocked = false;
                return;
            }

            foreach ($movingPlans as $plan) {

                $orderId = $plan->order_id;
                $m2 = $plan->planned_m2;

                // ------------------------
                // SAME DAY → FORCE
                // ------------------------
                if ($action === 'sameDay') {

                    $plan->update([
                        'planned_date' => $this->pendingDate
                    ]);

                    continue;
                }

                // ------------------------
                // SPLIT → ALTIJD HELE ORDER
                // ------------------------
                if ($action === 'split') {

                    $this->planSplit($orderId, $this->pendingDate);

                    continue;
                }

                // ------------------------
                // NEXT DAY
                // ------------------------
                if ($action === 'nextDay') {

                    $nextDay = date('Y-m-d', strtotime($this->pendingDate . ' +1 day'));

                    if ($m2 > $limit) {
                        // grote → splitsen
                        $this->planSplit($orderId, $nextDay);
                    } else {
                        // kleine → eerstvolgende plek
                        $next = $this->getNextAvailableDayForOrder(
                            $nextDay,
                            $orderId,
                            $m2
                        );

                        $plan->update([
                            'planned_date' => $next
                        ]);
                    }
                }

                // ------------------------
                // update startdatum order
                // ------------------------
                $firstDate = OrderPlanning::where('order_id', $orderId)
                    ->orderBy('planned_date')
                    ->value('planned_date');

                Order::where('id', $orderId)
                    ->update(['planned_start' => $firstDate]);
            }

            $this->reset(['pendingPlanningId', 'pendingDate']);
        }

        /*
        |--------------------------------------------------------------------------
        | FINALIZE
        |--------------------------------------------------------------------------
        */
        $this->dispatch('hideLimitModal');

        $this->dispatch('ordersUpdated', [
            'orders' => $this->getOrders()
        ]);

        $this->actionLocked = false;
    }


    /*---------------------------------------
    | SPLIT LOGICA
    ----------------------------------------*/



    /*---------------------------------------
    | HELPERS
    ----------------------------------------*/
    protected function getDayM2($date)
    {
        return OrderPlanning::where('planned_date', $date)->sum('planned_m2');
    }

    protected function getNextWorkingDay($date)
    {
        $next = date('Y-m-d', strtotime($date . ' +1 day'));

        // Loop tot we een werkdag vinden
        while ($this->isBlockedDay($next)) {
            $next = date('Y-m-d', strtotime($next . ' +1 day'));
        }

        return $next;
    }


    protected function isBlockedDay($date)
    {
        $day = strtolower(date('l', strtotime($date)));
        $translate = [
            'maandag' => 'monday',
            'dinsdag' => 'tuesday',
            'woensdag' => 'wednesday',
            'donderdag' => 'thursday',
            'vrijdag' => 'friday',
            'zaterdag' => 'saturday',
            'zondag' => 'sunday'
        ];

        $blocked = array_map(fn($d) => $translate[$d] ?? $d, array_map('strtolower', $this->settings->blocked_days));

        if (in_array($day, $blocked)) return true;

        foreach ($this->blockedDates as $b) {
            if ($b['date'] === $date) return true;
        }

        return false;
    }

    /*---------------------------------------
    | CREATE / DELETE
    ----------------------------------------*/
    protected function createPlanning($orderId, $date, $m2)
    {
        // ✅ Check of er al een planning voor dezelfde order op die datum bestaat
        $existing = OrderPlanning::where('order_id', $orderId)
            ->where('planned_date', $date)
            ->first();

        if ($existing) {
            $existing->planned_m2 = $m2;
            $existing->save();
        } else {
            OrderPlanning::create([
                'order_id' => $orderId,
                'planned_date' => $date,
                'planned_m2' => $m2
            ]);
        }

        // Update startdatum van order
        $firstDate = OrderPlanning::where('order_id', $orderId)
            ->orderBy('planned_date')
            ->value('planned_date');
        Order::where('id', $orderId)->update(['planned_start' => $firstDate]);

        // Verwijder van ongeplande orders
        $this->unplannedOrders = $this->unplannedOrders
            ->reject(fn($order) => $order->id == $orderId)
            ->values();

        $this->dispatch('ordersUpdated', [
            'orders' => $this->getOrders()
        ]);
    }


    public function unplanOrder($orderId)
    {
        $order = Order::with('orderLines','planning')->findOrFail($orderId);

        // Verwijder alle planning records
        $order->planning()->delete();

        // Reset startdatum
        $order->planned_start = null;
        $order->save();

        // Voeg terug toe aan ongeplande orders (geen duplicaten)
        if (!$this->unplannedOrders->contains('id', $orderId)) {
            $this->unplannedOrders->push($order);
        }

        // Update frontend kalender
        $this->dispatch('ordersUpdated', [
            'orders' => $this->getOrders()
        ]);
    }

    public function removeBlockedDay($date)
    {
        $this->blockedDates = collect($this->blockedDates)
            ->reject(fn($b) => $b['date'] === $date)
            ->values()
            ->toArray();

        $this->settings->blocked_dates = $this->blockedDates;
        $this->settings->save();

        $this->dispatch('blockedDatesUpdated', [
            'blockedDates' => $this->blockedDates
        ]);
    }

    public function addBlockedDay($date, $title = 'Geblokkeerd')
    {

        // Check of hij al bestaat
        $exists = collect($this->blockedDates)
            ->firstWhere('date', $date);



        if ($exists) return;

        $this->blockedDates[] = [
            'date' => $date,
            'title' => $title
        ];


        // Opslaan in settings
        $this->settings->blocked_dates = $this->blockedDates;
        $this->settings->save();



        // Update kalender
        $this->dispatch('blockedDatesUpdated', [
            'blockedDates' => $this->blockedDates
        ]);
    }
    protected function planSplit($orderId, $startDate, $totalM2 = null)
    {
        $limit = $this->settings->max_m2_per_day;

        if (is_null($totalM2)) {
            $order = Order::with('orderLines')->findOrFail($orderId);
            $totalM2 = $order->orderLines->sum('m2');
        }

        // ✅ Stap 1: verwijder oude planningen van deze order (alle dagen vanaf startDate)
        OrderPlanning::where('order_id', $orderId)->delete();

        $remaining = $totalM2;
        $currentDate = $startDate;

        // ✅ Stap 2: nieuwe splits aanmaken
        while ($remaining > 0) {
            $dayM2 = OrderPlanning::where('planned_date', $currentDate)->sum('planned_m2');
            $available = $limit - $dayM2;

            if ($available <= 0 || $this->isBlockedDay($currentDate)) {
                $currentDate = $this->getNextWorkingDay($currentDate);
                continue;
            }

            $toPlan = min($available, $remaining);

            OrderPlanning::create([
                'order_id' => $orderId,
                'planned_date' => $currentDate,
                'planned_m2' => $toPlan
            ]);

            $remaining -= $toPlan;

            if ($remaining > 0) {
                $currentDate = $this->getNextWorkingDay($currentDate);
            }
        }

        // ✅ Update startdatum
        $firstDate = OrderPlanning::where('order_id', $orderId)
            ->orderBy('planned_date')
            ->value('planned_date');
        Order::where('id', $orderId)->update(['planned_start' => $firstDate]);

        // ✅ Update ongeplande orders
        $this->unplannedOrders = $this->unplannedOrders
            ->reject(fn($o) => $o->id == $orderId)
            ->values();

        $this->dispatch('ordersUpdated', ['orders' => $this->getOrders()]);
    }



    protected function getNextAvailableDayForOrder($date, $orderId, $m2)
    {
        $limit = $this->settings->max_m2_per_day;

        if ($m2 > $limit) {
            // Veiligheidscheck: grote orders mogen hier niet heen
            throw new \Exception("Order m2 ($m2) is groter dan max per dag ($limit). Gebruik planSplit!");
        }

        $next = $date;

        do {
            $otherM2 = OrderPlanning::where('planned_date', $next)
                ->where('order_id', '!=', $orderId)
                ->sum('planned_m2');

            if ($this->isBlockedDay($next)) {
                $next = date('Y-m-d', strtotime($next . ' +1 day'));
                continue;
            }

            if (($limit - $otherM2) >= $m2) {
                return $next;
            }

            $next = date('Y-m-d', strtotime($next . ' +1 day'));
        } while (true);
    }


    public function editBlockedDay($date)
    {
        $this->editingBlockedDate = $date;

        // Vind de huidige titel
        $block = collect($this->blockedDates)->firstWhere('date', $date);
        $this->editingBlockedTitle = $block['title'] ?? 'Geblokkeerd';

        $this->showBlockedModal = true;
    }

    public function updateBlockedTitle()
    {
        if (!$this->editingBlockedDate) return;

        // Update in blockedDates array
        foreach ($this->blockedDates as &$block) {
            if ($block['date'] === $this->editingBlockedDate) {
                $block['title'] = $this->editingBlockedTitle;
                break;
            }
        }

        // Sla op in DB
        $this->settings->blocked_dates = $this->blockedDates;
        $this->settings->save();

        // Modal sluiten
        $this->showBlockedModal = false;

        // Dispatch naar JS zodat FullCalendar live update
        $date = date('Y-m-d', strtotime($this->editingBlockedDate));


        $this->dispatch('updateBlockedTitleLive', [
            'date' => $date,
            'title' => $this->editingBlockedTitle
        ]);

        // Reset properties
        $this->editingBlockedDate = null;
        $this->editingBlockedTitle = null;
    }



    /*---------------------------------------
| MERGE SPLITS
----------------------------------------*/
    public function mergeOrderParts($orderId, $targetDate)
    {
        $plans = OrderPlanning::where('order_id', $orderId)
            ->where('planned_date', $targetDate)
            ->get();

        if ($plans->count() < 2) return;

        $totalM2 = $plans->sum('planned_m2');

        $first = $plans->first();
        $first->planned_m2 = $totalM2;
        $first->save();

        // verwijder overige splits
        $plans->skip(1)->each->delete();

        // update order start datum
        $firstDate = OrderPlanning::where('order_id', $orderId)
            ->orderBy('planned_date')
            ->value('planned_date');
        Order::where('id', $orderId)->update(['planned_start' => $firstDate]);

        // ✅ verwijder order uit ongeplande orders als die nog daar is
        $this->unplannedOrders = $this->unplannedOrders
            ->reject(fn($o) => $o->id == $orderId)
            ->values();

        // ✅ Dispatch naar frontend zodat **alle oude events vervangen worden**
        $this->dispatch('ordersUpdated', [
            'orders' => $this->getOrders()
        ]);
    }


    public function moveBlockedDay($oldDate, $newDate)
    {
        foreach ($this->blockedDates as &$block) {
            if ($block['date'] === $oldDate) {
                $block['date'] = $newDate;
                break;
            }
        }

        $this->settings->blocked_dates = $this->blockedDates;
        $this->settings->save();

        // Update kalender live
        $this->dispatch('blockedDatesUpdated', [
            'blockedDates' => $this->blockedDates
        ]);
    }

    public function downloadOrders()
    {
        return $this->downloadZip('order', 'orders','order');
    }

    public function downloadPakketlijst()
    {
        return $this->downloadZip('pakketlijst', 'pakketlijsten','pakketlijst');
    }

    public function downloadFabriekslijst()
    {
        return $this->downloadZip('fabriekslijst', 'fabriekslijsten','fabriekslijst');
    }

    private function downloadZip(string $typeKey, string $zipNamePlural, string $zipNameSingular)
    {
        // ✅ Controleer of datums zijn ingevuld
        if (empty($this->printStartDate) || empty($this->printEndDate)) {
            session()->flash('error', "Vul zowel de start- als einddatum in om {$zipNamePlural} te downloaden.");
            return;
        }

        // Haal alle geplande orders op binnen de opgegeven datums
        $plannings = OrderPlanning::with('order')
            ->orderBy('planned_date', 'asc')
            ->whereDate('planned_date', '>=', $this->printStartDate)
            ->whereDate('planned_date', '<=', $this->printEndDate)
            ->get();

        if ($plannings->isEmpty()) {
            session()->flash('error', "Geen {$zipNamePlural} binnen dit tijdsbestek");
            return;
        }

        // Groepeer per order_id zodat we enkel het eerste planningrecord per order pakken
        $ordersGrouped = $plannings->groupBy(fn($planning) => $planning->order_id);

        $tmpDir = storage_path("app/temp_{$zipNamePlural}_" . time());
        if (!file_exists($tmpDir)) mkdir($tmpDir, 0777, true);

        $zip = new \ZipArchive();

        // ✅ Maak bestandsnaam op basis van start en eind datum
        $start = \Carbon\Carbon::parse($this->printStartDate)->format('d-m-Y');
        $end   = \Carbon\Carbon::parse($this->printEndDate)->format('d-m-Y');
        $zipFilePath = storage_path("app/{$zipNamePlural}_{$start}_tot_{$end}.zip");

        if ($zip->open($zipFilePath, \ZipArchive::CREATE) === TRUE) {
            foreach ($ordersGrouped as $orderId => $plannings) {
                $firstPlanning = $plannings->first();
                $order = $firstPlanning->order;

                $pdfContent = OrderPdfService::generatePdf($order, $typeKey);

                $plannedDate = \Carbon\Carbon::parse($firstPlanning->planned_date)->format('d-m-Y');
                $fileName = "{$plannedDate}-{$zipNameSingular}-{$order->order_id}.pdf";

                $pdfPath = $tmpDir . '/' . $fileName;
                file_put_contents($pdfPath, $pdfContent);
                $zip->addFile($pdfPath, $fileName);
            }

            $zip->close();
        }

        return response()->download($zipFilePath)->deleteFileAfterSend(true);
    }

}
