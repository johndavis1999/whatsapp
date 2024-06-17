<div class="bg-gray-50 rounded-lg shadow border border-gray-200 overflow-hidden">
    {{-- To attain knowledge, add things every day; To attain wisdom, subtract things every day. --}}
    <div class="grid grid-cols-3 divide-x divide-gray-200">
        <div class="col-span-1">
            <div class="bg-gray-100 h-16 flex items-center px-4">
                <img class="w-10 h-10 object-cover object-center" src="{{ auth()->user()->profile_photo_url }}" alt="{{ auth()->user()->name }}">
            </div>
            <div class="bg-white h-14 flex items-center px-4">
                <x-input wire:model.live="search" type="text" class="w-full" placeholder="Buscar Chat o Contacto" oninput="console.log({{ $search }} + 'xd')"/>

            </div>
            <div class="h-[calc(100vh-10.5rem)] overflow-auto border-t border-gray-200">
                <div class="px-4 py-3">
                    <h2 class="text-teal-600 text-lg mb-4">Contáctos</h2>
                    <ul class="space-y-4">
                        @forelse ($this->contacts as $contact)
                            <li class="cursor-pointer">
                                <div class="flex">
                                    <figure>
                                        <img class="h-12 w-12 rounded-full" src="{{ $contact->user->profile_photo_url }}" alt="{{ $contact->name }}">
                                    </figure>
                                    <div class="flex-1 ml-5 border-b border-gray-200">
                                        <p class="text-gray-800">
                                            {{ $contact->name }}
                                        </p>
                                        <p class="text-gray-600 text-xs">
                                            {{ $contact->user->email }}
                                        </p>
                                    </div>
                                </div>
                            </li>
                        @empty
                            
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-span-2">
            <div class="w-full h-full flex justify-center items-center">
                <div>
                    <div class="_al_n" style="opacity: 1;"><img src="https://static.whatsapp.net/rsrc.php/v3/y6/r/wa669aeJeom.png" width="450" alt=""></div>
                    <h1 class="text-center text-gray-500 text-2xl mt-4">Descarga Whatsapp para Windows</h1>
                </div>
            </div>
        </div>
    </div>
</div>
