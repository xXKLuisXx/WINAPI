<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>
    <br>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div>
                    <div>
                        <h2 class="font-semibold text-xl text-gray-800 leading-tight">User Token</h2>
                    </div>
                    <div>
                        {{ Auth::user()->sagmCredential->user_token }}
                    </div>
                </div>
                <div>
                    <div>
                        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Access Token</h2>
                    </div>
                    <div>
                        {{ App\Models\AesCrypt::decrypt(Auth::user()->sagmCredential->access_token, "574b454c53326e7069634f4d") }}
                    </div>
                    <form action="{{ url('/oxxo_charge') }}" method="POST">
                        @csrf
                        <label for="amount">Monto</label>
                        <input name="amount" type="number">
                        <button type="submit">Crear cargo</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
