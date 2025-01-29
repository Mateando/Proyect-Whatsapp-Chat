<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Editar Contacto
        </h2>
    </x-slot>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

        <form action="{{ route('contacts.update', $contact ) }}" method="POST"
            class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
            @csrf
            @method('PUT')

            <x-validation-errors class="mb-4" :errors="$errors" />
            
            <div class="mb-4">
                <x-label for="name" :value="__('Nombre')" />
                <x-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $contact->name)" 
                required autofocus placeholder="Ingrese el nombre del contacto" />

                <x-label for="name" :value="__('Correo Electrónico')" />
                <x-input id="name" class="block mt-1 w-full" type="email" name="email" :value="old('email', $contact->user->email)" 
                required autofocus placeholder="Ingrese el correo del contacto" />
                
                <x-button class="mt-4 justify-end ">
                    {{ __('Actualizar Contacto') }}
                </x-button>

            </div>

        </form>
    </div>
</x-app-layout>