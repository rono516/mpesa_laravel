<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <section>
                        <header>
                            <h2 class="text-lg font-medium text-gray-900">
                                {{ __('Confirm Mpesa Payment') }}
                            </h2>


                        </header>



                        <form method="post" action="{{ route('stk_push') }}" class="mt-6 space-y-6">
                            @csrf

                            {{-- <div>
                                <x-input-label for="phone" :value="__('Phone Number')" />
                                <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full"
                                     required placeholder="0792009556" autofocus autocomplete="phone" />
                                <x-input-error class="mt-2" :messages="$errors->get('phone')" />
                            </div> --}}

                            {{-- <div>
                                <x-input-label for="amount" :value="__('Amount')" />
                                <x-text-input id="amount" name="amount" type="number" class="mt-1 block w-full"
                                    placeholder="1" required  />
                                <x-input-error class="mt-2" :messages="$errors->get('amount')" />


                            </div> --}}

                            <div class="flex items-center gap-4">
                                <x-primary-button>{{ __('Confirm Payment') }}</x-primary-button>
                    
                                
                            </div>


                        </form>
                    </section>

                </div>
            </div>




        </div>
    </div>
</x-app-layout>
