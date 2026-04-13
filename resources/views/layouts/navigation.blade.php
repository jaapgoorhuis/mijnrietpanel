<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">

    <div class="max-w-9xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">

            {{-- LOGO --}}
            <div class="flex items-center">
                <a href="{{ route('dashboard') }}">
                    <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                </a>
            </div>

            {{-- DESKTOP MENU --}}
            <div class="hidden sm:flex sm:items-center sm:ms-6">

                <x-dropdown align="right" width="48">

                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 text-sm text-gray-500 hover:text-gray-700">
                            {{ Auth::user()->name }}
                        </button>
                    </x-slot>

                    <x-slot name="content">

                        {{-- ALTIJD --}}
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('messages.Profiel') }}
                        </x-dropdown-link>

                        {{-- USER (normaal) --}}
                        @user
                        <x-dropdown-link :href="route('mycompany')">
                            {{ __('messages.Mijn bedrijf') }}
                        </x-dropdown-link>
                        @enduser

                        {{-- ADMIN --}}
                        @admin

                        <x-dropdown-link :href="route('mycompany')">
                            {{ __('messages.Mijn bedrijf') }}
                        </x-dropdown-link>

                        <x-dropdown-link :href="route('productPlanning')">
                            {{ __('messages.Productplanning') }}
                        </x-dropdown-link>

                        <x-dropdown-link :href="route('companys')">
                            {{ __('messages.Bedrijven') }}
                        </x-dropdown-link>

                        <x-dropdown-link :href="route('surcharges')">
                            {{ __('messages.Toeslagen') }}
                        </x-dropdown-link>

                        <x-dropdown-link :href="route('supliers')">
                            {{ __('messages.Leveranciers') }}
                        </x-dropdown-link>

                        <x-dropdown-link :href="route('statistics')">
                            {{ __('messages.Statistieken') }}
                        </x-dropdown-link>

                        <x-dropdown-link :href="route('companys/pricerules')">
                            {{ __('messages.Prijsregels') }}
                        </x-dropdown-link>

                        <x-dropdown-link :href="route('accountrequests')">
                            <div class="flex items-center gap-2">
                                {{ __('messages.Account verzoeken') }}

                                @php
                                    $accountRequests = \App\Models\User::where('bedrijf_id', 0)->count();
                                @endphp

                                @if($accountRequests)
                                    <span class="h-5 w-5 text-xs text-white rounded-full bg-orange-500 flex items-center justify-center">
                                            {{ $accountRequests }}
                                        </span>
                                @endif
                            </div>
                        </x-dropdown-link>

                        @endadmin

                        {{-- LOGOUT (ALTIJD) --}}
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                             onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('messages.Log uit') }}
                            </x-dropdown-link>
                        </form>

                    </x-slot>

                </x-dropdown>
            </div>

            {{-- MOBILE BUTTON --}}
            <div class="flex items-center sm:hidden">
                <button @click="open = ! open">
                    ☰
                </button>
            </div>

        </div>
    </div>

    {{-- MOBILE MENU --}}
    <div :class="{ 'block': open, 'hidden': ! open }" class="hidden sm:hidden">

        {{-- ALTIJD --}}
        <x-responsive-nav-link :href="route('profile.edit')">
            {{ __('messages.Profiel') }}
        </x-responsive-nav-link>

        {{-- USER --}}
        @user
        <x-responsive-nav-link :href="route('mycompany')">
            {{ __('messages.Mijn bedrijf') }}
        </x-responsive-nav-link>
        @enduser

        {{-- ADMIN --}}
        @admin

        <x-responsive-nav-link :href="route('mycompany')">
            {{ __('messages.Mijn bedrijf') }}
        </x-responsive-nav-link>

        <x-responsive-nav-link :href="route('productPlanning')">
            {{ __('messages.Productplanning') }}
        </x-responsive-nav-link>

        <x-responsive-nav-link :href="route('companys')">
            {{ __('messages.Bedrijven') }}
        </x-responsive-nav-link>

        <x-responsive-nav-link :href="route('surcharges')">
            {{ __('messages.Toeslagen') }}
        </x-responsive-nav-link>

        <x-responsive-nav-link :href="route('supliers')">
            {{ __('messages.Leveranciers') }}
        </x-responsive-nav-link>

        <x-responsive-nav-link :href="route('statistics')">
            {{ __('messages.Statistieken') }}
        </x-responsive-nav-link>

        <x-responsive-nav-link :href="route('companys/pricerules')">
            {{ __('messages.Prijsregels') }}
        </x-responsive-nav-link>

        <x-responsive-nav-link :href="route('accountrequests')">
            {{ __('messages.Account verzoeken') }}
        </x-responsive-nav-link>

        @endadmin

        {{-- LOGOUT --}}
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <x-responsive-nav-link :href="route('logout')"
                                   onclick="event.preventDefault(); this.closest('form').submit();">
                {{ __('messages.Log uit') }}
            </x-responsive-nav-link>
        </form>

    </div>

</nav>
