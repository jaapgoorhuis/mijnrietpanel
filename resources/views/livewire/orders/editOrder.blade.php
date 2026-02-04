
<div class="min-h-screen bg-gray-100">
    @if($currentModal === 'first')
        <div id="update-modal" tabindex="-1" class="overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full flex" aria-modal="true" role="dialog">
            <div class="relative p-4 w-full max-w-lg max-h-full">
                <div class="relative bg-white rounded-lg shadow-sm ">
                    <div class="p-4 md:p-5 text-center">
                        <svg class="mx-auto mb-4 text-gray-400 w-12 h-12 " aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"></path>
                        </svg>
                        <h3 class="mb-5 text-lg font-normal text-gray-500 ">
                            order #{{$order->order_id}} bevestigen
                        </h3>

                        <h5 class="text-md font-normal text-gray-500 "> Opmerkingen klant:</h5>
                        @if($order->comment)
                            {!! $order->comment !!}
                        @else
                            De klant heeft geen op/aanmerkingen
                       @endif<br/><br/>

                        <h5 class="text-md font-normal text-gray-500 ">
                            Prijs invloed:
                            <div class="tooltip">
                                <div class="tooltip-content">
                                    Beschrijf in eigen woorden de wens van de klant. Doe dit zo kort en duidelijk mogelijk. Jouw beschrijving komt op de pakketlijst, fabriekslijst en bevestigde order te staan. De prijs komt bovenop de normale prijs van de order. Heeft de klant geen opmerking? Laat de regel en prijs dan leeg.

                                </div>
                                <i wire:click.prevent="" class="fa-solid fa-circle-info hover:cursor-pointer" id="tooltip-marge"></i>
                            </div>
                        </h5>


                        <div class="grid md:grid-cols-2 md:gap-6">
                            <div class="relative z-0 w-full mb-5 group">
                                <label for="rule" class="text-gray-400">Regel:</label>
                                <input type="text"  wire:model="rule" name="rule" id="rule" class="block py-2.5 px-0 w-full text-md text-gray-900 border-0 border-b-2 border-gray-300 appearance-none dark:text-gray-900 dark:border-gray-600 focus:outline-none focus:ring-0 focus:border-b-[#C0A16E]" placeholder=" " required />
                                <div class="text-red-500">@error('rule') {{ $message }} @enderror</div>
                            </div>
                            <div class="relative z-0 w-full mb-5 group">
                                <label for="price" class="text-gray-400">prijs:</label>
                                <input type="number"  step=".01" wire:model="price" name="price" id="price" class="block py-2.5 px-0 w-full text-md text-gray-900 border-0 border-b-2 border-gray-300 appearance-none dark:text-gray-900 dark:border-gray-600 focus:outline-none focus:ring-0 focus:border-b-[#C0A16E]" placeholder=" " required />
                                <div class="text-red-500">@error('price') {{ $message }} @enderror</div>
                            </div>
                        </div>

                        <div class="relative z-0 w-full mb-5 group">
                            <label for="show-orderlist" class="text-gray-400">Laat opmerking op de bestellijst zien:</label>
                            <input id="show-orderlist" wire:model="show_orderlist" type="checkbox" value="" class="w-4 h-4 border border-default-medium rounded-xs bg-neutral-secondary-medium focus:ring-2 focus:ring-brand-soft">
                        </div>

                        <br/>
                        <div class="relative z-0 w-full mb-5 group">
                            <label for="requested_delivery_date" class="text-gray-400">Leverdatum order:</label>

                            <input
                                type="text"
                                class="datepicker block w-full bg-neutral-secondary-medium text-center border-0 border-b-2 border-gray-300 appearance-none dark:text-gray-900 dark:border-gray-600 focus:outline-none focus:ring-0 focus:border-b-[#C0A16E]"
                                wire:model="delivery_date"
                                placeholder="Selecteer datum"
                            />
                            <div class="text-red-500">@error('delivery_date') {{ $message }} @enderror</div>
                        </div>

                        <button wire:click="NextModal({{$order->id}})" type="button" class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center">
                            Volgende
                        </button>
                        <button wire:click="cancelUpdateOrder()" type="button" class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 ">Annuleren</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if($currentModal === 'next')
            <div id="next-modal" tabindex="-1" class="overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full flex" aria-modal="true" role="dialog">
                <div class="relative p-4 w-full max-w-lg max-h-full">
                    <div class="relative bg-white rounded-lg shadow-sm ">
                        <div class="p-4 md:p-5 text-center">
                            <svg class="mx-auto mb-4 text-gray-400 w-12 h-12 " aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"></path>
                            </svg>
                            <h3 class="mb-5 text-lg font-normal text-gray-500 ">
                                Inkoop order #{{$order->order_id}} verzenden naar <strong> {{$this->existing_purchage_order_suplier}}</strong>
                            </h3>


                            <div class="relative z-0 w-full mb-5 group">
                                <label for="new_purchage_order_email" class="text-gray-400">Inkoop email adres:</label><br/>
                                <input type="text"  wire:model="new_purchage_order_email" name="new_purchage_order_email" id="new_purchage_order_email" class="text-center w-auto text-md text-gray-900 border-0 border-b-2 border-gray-300 appearance-none dark:text-gray-900 dark:border-gray-600 focus:outline-none focus:ring-0 focus:border-b-[#C0A16E]" placeholder=" " required />
                                <div class="text-red-500">@error('new_purchage_order_email') {{ $message }} @enderror</div>
                            </div>

                                <br/>
                            <div class="relative z-0 w-full mb-5 group">
                                <label for="send-copy" class="text-gray-400">Kopie van inkooporder verzenden naar administratie@rietpanel.nl:</label>
                                <input id="send-copy" wire:model="send_copy" type="checkbox" value="" class="w-4 h-4 border border-default-medium rounded-xs bg-neutral-secondary-medium focus:ring-2 focus:ring-brand-soft">
                            </div>



                            <button wire:click="updateOrder({{$order->id}})" type="button" class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center">
                                Order versturen & bevestigen
                            </button>
                            <button wire:click="cancelNextModal()" type="button" class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 ">Vorige</button>
                        </div>
                    </div>
                </div>
            </div>
    @endif

</div>
