<div class="min-h-screen bg-gray-100">
    <div id="update-modal" tabindex="-1" class="overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full flex" aria-modal="true" role="dialog">
        <div class="relative p-4 w-full max-w-md max-h-full">
            <div class="relative bg-white rounded-lg shadow-sm dark:bg-gray-700">
                <form wire:submit="uploadOrderForm">
                    <div class="p-4 md:p-5 text-center">
                        <h3 class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400">
                            Nieuw order formulier uploaden
                        </h3>
                        <input wire:model="orderForm" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400" id="file_input" type="file">
                        <div class="text-red-500">@error('orderForm') {{ $message }} @enderror</div>
                    </div>
                    <div class="p-4 md:p-5">
                        <div class="relative">
                            <button type="submit" wire:loading.attr="disabled" wire:target="orderForm" class="disabled:cursor-not-allowed py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                                Uploaden
                            </button>
                            <button wire:click="cancelUploadOrderForm" type="button" class="relative md:absolute right-0 text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 dark:focus:ring-red-800 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center">
                                Annuleren
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
