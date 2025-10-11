<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Home') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <main class="min-h-full flex items-center">
            <div class="w-full max-w-7xl mx-auto p-6 sm:p-10">
                <!-- Grid 3x2, responsive -->
                <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
                    <!-- Card -->


                    <!-- Card -->
                    <a href="/offertes" class="group relative rounded-2xl bg-white ring-1 ring-black/5 shadow-sm p-8 sm:p-10 flex flex-col items-center gap-5 transition transform hover:-translate-y-0.5 hover:shadow-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-neutral-800">
                        <!-- Icon: Archive Box -->
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-12 w-12 text-neutral-700">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.375 6A2.625 2.625 0 016 3.375h12A2.625 2.625 0 0120.625 6v.375A.375.375 0 0120.25 6H3.75a.375.375 0 01-.375-.375V6z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 6h16.5V18A2.25 2.25 0 0118 20.25H6A2.25 2.25 0 013.75 18V6z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 11.25h6"/>
                        </svg>
                        <span class="text-lg font-semibold tracking-tight text-neutral-900">Offertes</span>
                        <span class="absolute inset-0 rounded-2xl ring-1 ring-transparent group-hover:ring-neutral-200/80"></span>
                    </a>

                    <a href="/orders" class="group relative rounded-2xl bg-white ring-1 ring-black/5 shadow-sm p-8 sm:p-10 flex flex-col items-center gap-5 transition transform hover:-translate-y-0.5 hover:shadow-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-neutral-800">
                        <!-- Icon: Squares 2x2 -->
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-12 w-12 text-neutral-700">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.75 3.75h-3a3 3 0 00-3 3v3a3 3 0 003 3h3a3 3 0 003-3v-3a3 3 0 00-3-3zm8.5 0h-3a3 3 0 00-3 3v3a3 3 0 003 3h3a3 3 0 003-3v-3a3 3 0 00-3-3zm-8.5 8.5h-3a3 3 0 00-3 3v3a3 3 0 003 3h3a3 3 0 003-3v-3a3 3 0 00-3-3zm8.5 0h-3a3 3 0 00-3 3v3a3 3 0 003 3h3a3 3 0 003-3v-3a3 3 0 00-3-3z"/>
                        </svg>
                        <span class="text-lg font-semibold tracking-tight text-neutral-900">@admin Alle orders @endadmin @user Mijn orders @enduser</span>
                        <span class="absolute inset-0 rounded-2xl ring-1 ring-transparent group-hover:ring-neutral-200/80"></span>
                    </a>
                    <!-- Card -->
                    <a href="/details" class="group relative rounded-2xl bg-white ring-1 ring-black/5 shadow-sm p-8 sm:p-10 flex flex-col items-center gap-5 transition transform hover:-translate-y-0.5 hover:shadow-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-neutral-800">
                        <!-- Icon: Magnifying Glass -->
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-12 w-12 text-neutral-700">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 104.5 4.5a7.5 7.5 0 0012.15 12.15z"/>
                        </svg>
                        <span class="text-lg font-semibold tracking-tight text-neutral-900">Voorbeeld details</span>
                        <span class="absolute inset-0 rounded-2xl ring-1 ring-transparent group-hover:ring-neutral-200/80"></span>
                    </a>

                    <!-- Card -->
                    <a href="/marketing" class="group relative rounded-2xl bg-white ring-1 ring-black/5 shadow-sm p-8 sm:p-10 flex flex-col items-center gap-5 transition transform hover:-translate-y-0.5 hover:shadow-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-neutral-800">
                        <!-- Icon: Chart Bar -->
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-12 w-12 text-neutral-700">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 19.5h18M6 16.5V9.75M12 16.5V6.75M18 16.5v-5.25"/>
                        </svg>
                        <span class="text-lg font-semibold tracking-tight text-neutral-900">Mijn marketing</span>
                        <span class="absolute inset-0 rounded-2xl ring-1 ring-transparent group-hover:ring-neutral-200/80"></span>
                    </a>

                    <!-- Card -->
                    <a href="/documentation" class="group relative rounded-2xl bg-white ring-1 ring-black/5 shadow-sm p-8 sm:p-10 flex flex-col items-center gap-5 transition transform hover:-translate-y-0.5 hover:shadow-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-neutral-800">
                        <!-- Icon: Folder -->
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-12 w-12 text-neutral-700">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 12.75V7.5A2.25 2.25 0 014.5 5.25h3.879a2.25 2.25 0 011.591.659l1.121 1.121a2.25 2.25 0 001.591.659H19.5A2.25 2.25 0 0121.75 9.75v7.5A2.25 2.25 0 0119.5 19.5H4.5A2.25 2.25 0 012.25 17.25v-4.5z"/>
                        </svg>
                        <span class="text-lg font-semibold tracking-tight text-neutral-900">Documentatie</span>
                        <span class="absolute inset-0 rounded-2xl ring-1 ring-transparent group-hover:ring-neutral-200/80"></span>
                    </a>

                    <!-- Card -->
                    <a href="/pricelist" class="group relative rounded-2xl bg-white ring-1 ring-black/5 shadow-sm p-8 sm:p-10 flex flex-col items-center gap-5 transition transform hover:-translate-y-0.5 hover:shadow-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-neutral-800">
                        <!-- Icon: Document Exclamation -->
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-12 w-12 text-neutral-700">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9.75v3.75m0 3.75h.008M6.75 3.75h6.75L18 8.25v12a.75.75 0 01-.75.75h-9a.75.75 0 01-.75-.75v-16.5a.75.75 0 01.75-.75z"/>
                        </svg>
                        <span class="text-lg font-semibold tracking-tight text-neutral-900 text-center">Voorwaarden / Prijslijst</span>
                        <span class="absolute inset-0 rounded-2xl ring-1 ring-transparent group-hover:ring-neutral-200/80"></span>
                    </a>
                </section>
            </div>
        </main>
    </div>
</x-app-layout>
