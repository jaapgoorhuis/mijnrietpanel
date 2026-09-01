
<x-slot name="header">
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
            <li class="inline-flex items-center">
                <a href="/dashboard" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-[#C0A16E]">
                    {{ __('messages.Mijn Rietpanel') }}
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="fa-solid fa-angle-right"></i>
                    <a href="/offertes" class="inline-flex items-center md:ms-2 text-sm font-medium text-gray-700 hover:text-[#C0A16E] ">
                        {{ __('messages.Mijn offertes') }}
                    </a>
                </div>
            </li>

            <li>
                <div class="flex items-center">
                    <i class="fa-solid fa-angle-right"></i>
                    <p class="ms-1 text-sm font-medium text-gray-700 md:ms-2">   {{ __('messages.Offerte bewerken') }}</p>
                </div>
            </li>
        </ol>
    </nav>
</x-slot>

<div class="py-12">
    <div class="max-w-9xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-visible shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="grid">

                    @if($this->showPriceUpdateModal)
                        <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-[9999]">
                            <div class="border-2 border-[#C0A16E] bg-white rounded-lg shadow-lg p-6 max-w-md w-full text-center">

                                <h3 class="text-lg font-bold mb-4">
                                    {{ __('Prijswijziging gedetecteerd') }}
                                </h3>

                                <p class="mb-6">
                                    {{ $priceUpdateMessage }}
                                </p>

                                <div class="flex justify-center gap-4">
                                    <button
                                        type="button"
                                        wire:click="$set('showPriceUpdateModal', false)"
                                        class="px-4 py-2 bg-[#C0A16E] text-white rounded hover:bg-[#d1b079]"
                                    >
                                        {{ __('Begrepen') }}
                                    </button>
                                </div>

                            </div>
                        </div>
                    @endif
                    <form>
                        <div class="relative">
                            <i wire:click="cancelChangeOfferte()" class="absolute right-0 fa-solid fa-xmark text-xl hover:cursor-pointer"></i>
                        </div>

                        {{ __('messages.Project gegevens') }}
                        <br/><br/>
                        <div class="grid md:grid-cols-2 md:gap-6">
                            <div class="relative z-0 w-full mb-5 group">
                                <label for="klant_naam" class="text-gray-400">   {{ __('messages.Klantnaam') }} *</label>
                                <input type="text"  wire:model="klant_naam" name="klant_naam" id="klant_naam" class="block py-2.5 px-0 w-full text-md text-gray-900 border-0 border-b-2 border-gray-300 appearance-none dark:text-gray-900 dark:border-gray-600 focus:outline-none focus:ring-0 focus:border-b-[#C0A16E]" placeholder=" " required />
                                <div class="text-red-500">@error('klant_naam') {{ $message }} @enderror</div>
                            </div>

                            <div class="relative z-0 w-full mb-5 group">
                                <label for="requested_delivery_date" class="text-gray-400">   {{ __('messages.Gewenste leverdatum') }} *</label>

                                <input
                                    type="text"
                                    class="datepicker block w-full bg-neutral-secondary-medium border-0 border-b-2 border-gray-300 appearance-none dark:text-gray-900 dark:border-gray-600 focus:outline-none focus:ring-0 focus:border-b-[#C0A16E]"
                                    wire:model="requested_delivery_date"
                                    placeholder=" {{ __('messages.Selecteer datum') }}"
                                />
                                <div class="text-red-500">@error('requested_delivery_date') {{ $message }} @enderror</div>
                            </div>

                        </div>




                        <div class="grid md:grid-cols-2 md:gap-6">
                            <div class="relative z-0 w-full mb-5 group">
                                <label for="klant_naam" class="text-gray-400">   {{ __('messages.Projectnaam') }} *</label>
                                <input type="text"  wire:model="project_naam" name="project_naam" id="project_naam" class="block py-2.5 px-0 w-full text-md text-gray-900 border-0 border-b-2 border-gray-300 appearance-none dark:text-gray-900 dark:border-gray-600 focus:outline-none focus:ring-0 focus:border-b-[#C0A16E]" placeholder=" " required />
                                <div class="text-red-500">@error('project_naam') {{ $message }} @enderror</div>
                            </div>
                            <div class="relative z-0 w-full mb-5 group">
                                <label for="referentie" class="text-gray-400">   {{ __('messages.Referentie') }} *</label>
                                <input type="text"  wire:model="referentie" name="referentie" id="referentie" class="block py-2.5 px-0 w-full text-md text-gray-900 border-0 border-b-2 border-gray-300 appearance-none dark:text-gray-900 dark:border-gray-600 focus:outline-none focus:ring-0 focus:border-b-[#C0A16E]" placeholder=" " required />
                                <div class="text-red-500">@error('referentie') {{ $message }} @enderror</div>
                            </div>
                        </div>


                        <div class="grid md:grid-cols-2 md:gap-6">
                            <div class="relative z-0 w-full mb-5 group">
                                <label for="aflever_straat" class="text-gray-400">   {{ __('messages.Aflever straat') }} *</label>
                                <input type="text" wire:model="aflever_straat" name="aflever_straat" id="aflever_straat" class="block py-2.5 px-0 w-full text-md text-gray-900 border-0 border-b-2 border-gray-300 appearance-none dark:text-gray-900 dark:border-gray-600 focus:outline-none focus:ring-0 focus:border-b-[#C0A16E]" placeholder=" " />
                                <div class="text-red-500">@error('aflever_straat') {{ $message }} @enderror</div>
                            </div>
                            <div class="relative z-0 w-full mb-5 group">
                                <label for="aflever_postcode" class="text-gray-400">   {{ __('messages.Aflever postcode') }} *</label>
                                <input type="text" wire:model="aflever_postcode" name="aflever_postcode" id="aflever_postcode" class="block py-2.5 px-0 w-full text-md text-gray-900 border-0 border-b-2 border-gray-300 appearance-none dark:text-gray-900 dark:border-gray-600 focus:outline-none focus:ring-0 focus:border-b-[#C0A16E]" placeholder=" " />
                                <div class="text-red-500">@error('aflever_postcode') {{ $message }} @enderror</div>
                            </div>
                        </div>

                        <div class="grid md:grid-cols-2 md:gap-6">
                            <div class="relative z-0 w-full mb-5 group">
                                <label for="aflever_plaats" class="text-gray-400">{{ __('messages.Aflever plaats') }} *</label>
                                <input type="text" wire:model="aflever_plaats" name="aflever_plaats" id="aflever_plaats" class="block py-2.5 px-0 w-full text-md text-gray-900 border-0 border-b-2 border-gray-300 appearance-none dark:text-gray-900 dark:border-gray-600 focus:outline-none focus:ring-0 focus:border-b-[#C0A16E]" placeholder=" " />
                                <div class="text-red-500">@error('aflever_plaats') {{ $message }} @enderror</div>
                            </div>
                            <div class="relative z-0 w-full mb-5 group">
                                <label for="aflever_land" class="text-gray-400">{{ __('messages.Aflever land') }} *</label>
                                <input type="text" wire:model="aflever_land" name="aflever_land" id="aflever_land" class="block py-2.5 px-0 w-full text-md text-gray-900 border-0 border-b-2 border-gray-300 appearance-none dark:text-gray-900 dark:border-gray-600 focus:outline-none focus:ring-0 focus:border-b-[#C0A16E]" placeholder=" " />
                                <div class="text-red-500">@error('aflever_land') {{ $message }} @enderror</div>
                            </div>
                        </div>

                        <div class="grid md:grid-cols-2 md:gap-6">
                            <div class="relative z-0 w-full mb-5 group">
                                <label for="rietkleur" class="text-gray-400">{{ __('messages.Rietkleur') }} *</label>
                                <select id="rietkleur" wire:model="rietkleur" class="block py-2.5 px-0 w-full text-sm text-gray-500 bg-transparent border-0 border-b-2 border-gray-200 appearance-none dark:text-gray-900 focus:outline-none focus:ring-0 focus:border-gray-200 peer">

                                    <option value="Old look">Old look</option>
                                    <option value="New look">New look</option>

                                </select>
                            </div>
                            <div class="relative z-0 w-full mb-5 group">
                                <label for="kerndikte" class="text-gray-400">{{ __('messages.Kerndikte') }} *</label>
                                <select id="kerndikte" wire:change="updatePrice()" wire:model="kerndikte" class="block py-2.5 px-0 w-full text-sm text-gray-500 bg-transparent border-0 border-b-2 border-gray-200 appearance-none dark:text-gray-400 dark:border-gray-700 focus:outline-none focus:ring-0 focus:border-gray-200 peer">
                                    <option value="" selected>{{ __('messages.Selecteer een kerndikte') }}</option>
                                    @foreach($this->panelTypes as $type)
                                        <option value="{{$type->name}}">{{$type->name}}</option>
                                    @endforeach
                                </select>
                                <div class="text-red-500">@error('kerndikte') {{ $message }} @enderror</div>
                            </div>
                        </div>

                        <div class="grid md:grid-cols-2 md:gap-6">
                            <div class="relative z-0 w-full mb-5 group">
                                <label for="intaker_name" class="text-gray-400">{{ __('messages.Verkoper') }} *</label>
                                <input type="text" wire:model="intaker" name="intaker" id="intaker" class="block py-2.5 px-0 w-full text-md text-gray-900 border-0 border-b-2 border-gray-300 appearance-none dark:text-gray-900 dark:border-gray-600 focus:outline-none focus:ring-0 focus:border-b-[#C0A16E]" placeholder=" " required />
                                <div class="text-red-500">@error('intaker') {{ $message }} @enderror</div>
                            </div>


                        </div>
                        <div class="grid md:grid-cols-2 md:gap-6">
                            <div class="relative z-0 w-full mb-5 group">
                                <label for="toepassing" class="text-gray-400">{{ __('messages.Toepassing') }} *</label>
                                <select id="toepassing" wire:model="toepassing" wire:change="updateBrands()" class="block py-2.5 px-0 w-full text-sm text-gray-500 bg-transparent border-0 border-b-2 border-gray-200 appearance-none dark:text-gray-900 dark:border-gray-700 focus:outline-none focus:ring-0 focus:border-gray-200 peer">

                                    <option value="Dak">{{ __('messages.Dak') }}</option>
                                    <option value="Wand">{{ __('messages.Gevel') }}</option>

                                </select>
                            </div>


                            <div class="relative z-0 w-full mb-5 group">
                                <label for="merk_paneel" class="text-gray-400">{{ __('messages.Merk element') }} *</label>
                                <select @if(count($this->offerteLines)) disabled @endif id="merk_paneel" wire:model="merk_paneel" class="disabled:hover:cursor-not-allowed block py-2.5 px-0 w-full text-sm text-gray-500 bg-transparent border-0 border-b-2 border-gray-200 appearance-none dark:text-gray-900 dark:border-gray-700 focus:outline-none focus:ring-0 focus:border-gray-200 peer">
                                    @foreach($this->brands as  $brands)
                                        <option @if($brands->status == 0) disabled @endif class="disabled:bg-[#ededea]" value="{{$brands->name}}">{{$brands->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="relative z-0 w-full mb-5 group">
                            <label for="comment" class="text-gray-400">{{ __('messages.Opmerkingen') }}
                                <div class="tooltip">
                                    <div class="tooltip-content ml-[40px]">
                                        {{ __('messages.Geef hier aan wanneer er een speciale bewerking of actie vereist is Let op: Toegevoegde bewerkingen of acties kan een meerprijs geven Neem hiervoor contact op bij vragen') }}
                                    </div>
                                    <i wire:click.prevent="" class="fa-solid fa-circle-info hover:cursor-pointer"></i>
                                </div>
                                <strong></strong>
                            </label>
                            <textarea wire:model="comment" name="comment" id="comment" class="block py-2.5 px-0 w-full text-md text-gray-900 border-0 border-b-2 border-gray-300 appearance-none dark:text-gray-900 dark:border-gray-600 focus:outline-none focus:ring-0 focus:border-b-[#C0A16E]" placeholder=" "></textarea>
                            <div class="text-red-500">@error('comment') {{ $message }} @enderror</div>
                        </div>

                        <br/><br/>
                        @foreach($offerteLines as $index => $order)
                            @php
                                $selectedOptions = $selectedPanelOption[$index] ?? [];
                            @endphp

                            @if($index > 0)
                                <hr class="border-2 border-[#C0A16E]"/><br/><br/>
                            @endif

                            <div class="text-right">
                                <button wire:click.prevent="removeOfferteLine({{$index}})" type="button" class="text-white bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2">
                                    <i class="fa-solid fa-trash hover:cursor-pointer text-white"></i>
                                </button>
                            </div>

                            <br/>


                                <div class="grid md:grid-cols-2 md:gap-6">
                                    <div class="relative z-0 w-full mb-5 group">
                                        <label for="fillTotaleLengte" class="text-gray-400">{{ __('messages.Totale element lengte') }} (mm) *
                                            <div class="tooltip" wire:ignore>
                                                <div class="tooltip-content">
                                                    {{ __('messages.minpanellength') }}
                                                </div>
                                                <i wire:click.prevent="" class="fa-solid fa-circle-info hover:cursor-pointer"></i>
                                            </div>
                                        </label>
                                        <input
                                            type="number"
                                            min="500"
                                            max="14500"
                                            wire:model.live.debounce.400ms="fillTotaleLengte.{{$index}}"
                                            wire:blur="normalizePanelOptions({{$index}})"
                                            name="fillTotaleLengte"
                                            id="fillTotaleLengte"
                                            class="focus:border-b-[#C0A16E] block py-2.5 px-0 w-full text-md text-gray-900 border-0 border-b-2 border-gray-300 appearance-none dark:text-gray-900 dark:border-gray-600 focus:outline-none focus:ring-0"
                                            required
                                        />
                                        <div class="text-red-500">@error('totaleLengte.'.$index) {{ $message }} @enderror</div>
                                    </div>

                                    <div class="relative z-0 w-full mb-5 group">
                                        <label for="aantal" class="text-gray-400">{{ __('messages.Aantal elementen') }} *
                                            <div class="tooltip">
                                                <div class="tooltip-content">
                                                    {{ __('messages.Vul hier het aantal elementen in welke u nodig heeft met de ingevulde specificaties Heeft u meerdere elementen nodig met andere specificaties? Druk dan op de plus hieronder om een extra rij aan te maken') }}
                                                </div>
                                                <i wire:click.prevent="" class="fa-solid fa-circle-info hover:cursor-pointer"></i>
                                            </div>
                                        </label>
                                        <input type="number" min="1" wire:change="updateM2({{$index}})" wire:keydown="updateM2({{$index}})" wire:model="aantal.{{$index}}" name="aantal" id="aantal" class="focus:border-b-[#C0A16E] block py-2.5 px-0 w-full text-md text-gray-900 border-0 border-b-2 border-gray-300 appearance-none dark:text-gray-900 dark:border-gray-600 focus:outline-none focus:ring-0" placeholder=" " required />
                                        <div class="text-red-500">@error('aantal.'.$index) {{ $message }} @enderror</div>
                                    </div>
                                </div>

                                <x-panel-line-form
                                    :index="$index"
                                    :selected-options="$selectedOptions"
                                    :panel-values="$panelValues[$index] ?? []"
                                    :waterstop-enabled="$waterstopEnabled[$index] ?? false"
                                    :totale-lengte="$totaleLengte[$index] ?? 0"
                                    :m2="$this->m2[$index]"
                                    :layback-price="$this->laybackPrice"
                                    :nokafschuining-price="$this->nokafschuiningPrice"
                                    :vrijeruimte-price="$this->vrijeruimtePrice"
                                    :waterstop-price="$this->waterstopPrice"
                                />
                        @endforeach

                        <div class="text-right">
                            <button wire:click="addOfferteLine()" type="button" class="text-white bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-gray-800 dark:hover:bg-gray-700 dark:focus:ring-gray-700 dark:border-gray-700">
                                <i class="fa fa-plus hover:cursor-pointer"></i>{{ __('messages.Element toevoegen') }}
                            </button>
                        </div>

                        <div
                            x-data="{ show:false, message:'' }"
                            x-on:show-form-error.window="
        message = $event.detail.message;
        show = true;
        setTimeout(() => show = false, 5000);
    "
                        >
                            <div
                                x-show="show"
                                x-transition
                                class="fixed top-5 right-5 z-50 bg-red-500 text-white px-5 py-3 rounded shadow-lg"
                            >
                                <i class="fa-solid fa-circle-exclamation mr-2"></i>
                                <span x-text="message"></span>
                            </div>
                        </div>

                        <button wire:loading.attr="disabled" wire:target="saveOfferte" wire:click.prevent="saveOfferte()" @if(!count($this->offerteLines)) disabled @endif class="text-white bg-[#C0A16E] mt-10 hover:bg-[#d1b079] disabled:bg-[#c0a16e99] disabled:cursor-not-allowed hover:cursor-pointer focus:outline-none font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center">
                            <div wire:loading wire:target="saveOfferte">
                                <i class="fa-solid fa-spinner fa-spin"></i>{{ __('messages.Offerte opslaan') }}
                            </div>
                            <div wire:loading.attr="hidden" wire:target="saveOfferte">
                                {{ __('messages.Offerte updaten') }}
                            </div>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    window.addEventListener('show-form-error', () => {
        setTimeout(() => {
            let error = document.querySelector('.text-red-500');

            if(error){
                error.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
            }
        }, 100);
    });
</script>


<script>
    window.addEventListener('capture-panel-renders', async () => {

        let renders = [];

        const elements = document.querySelectorAll('[id^="panel-render-"]');

        for (let index = 0; index < elements.length; index++) {

            document.documentElement.style.setProperty('--tw-ring-color','#000');

            let canvas = await html2canvas(elements[index], {
                onclone: (clonedDoc) => {
                    clonedDoc.querySelectorAll('*').forEach(el => {
                        const style = window.getComputedStyle(el);

                        if (style.color.includes('oklch')) {
                            el.style.color = '#000000';
                        }

                        if (style.backgroundColor.includes('oklch')) {
                            el.style.backgroundColor = '#ffffff';
                        }

                        if (style.borderColor.includes('oklch')) {
                            el.style.borderColor = '#000000';
                        }
                    });
                },
                scale: 2,
                backgroundColor:'#ffffff'
            });


            // witte ruimte verwijderen
            canvas = cropCanvas(canvas);


            let image = canvas.toDataURL('image/png');


            Livewire.dispatch('save-panel-render',{
                index:index,
                image:image
            });
        }



        await Livewire.dispatch('panel-renders-finished');

    });



    function cropCanvas(canvas) {

        const ctx = canvas.getContext('2d');

        const imageData = ctx.getImageData(
            0,
            0,
            canvas.width,
            canvas.height
        );

        const data = imageData.data;


        let minX = canvas.width;
        let minY = canvas.height;
        let maxX = 0;
        let maxY = 0;


        for (let y = 0; y < canvas.height; y++) {

            for (let x = 0; x < canvas.width; x++) {

                let index = (y * canvas.width + x) * 4;

                let r = data[index];
                let g = data[index + 1];
                let b = data[index + 2];
                let a = data[index + 3];


                // alles wat niet wit is telt als onderdeel van de render
                if (
                    a > 0 &&
                    !(r > 245 && g > 245 && b > 245)
                ) {

                    if (x < minX) minX = x;
                    if (y < minY) minY = y;
                    if (x > maxX) maxX = x;
                    if (y > maxY) maxY = y;

                }
            }
        }


        // niets gevonden
        if (maxX === 0 && maxY === 0) {
            return canvas;
        }


        let width = maxX - minX;
        let height = maxY - minY;


        let croppedCanvas = document.createElement('canvas');

        croppedCanvas.width = width;
        croppedCanvas.height = height;


        croppedCanvas
            .getContext('2d')
            .drawImage(
                canvas,
                minX,
                minY,
                width,
                height,
                0,
                0,
                width,
                height
            );


        return croppedCanvas;
    }

</script>
