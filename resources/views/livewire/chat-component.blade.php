<div x-data="data()" class="bg-gray-50 rounded-lg shadow border border-gray-200 overflow-hidden">
    {{-- To attain knowledge, add things every day; To attain wisdom, subtract things every day. --}}
    <div class="grid grid-cols-3 divide-x divide-gray-200">
        <div class="col-span-1">
            <div class="bg-gray-100 h-16 flex items-center px-4">
                <img class="w-10 h-10 object-cover object-center" src="{{ auth()->user()->profile_photo_url }}" alt="{{ auth()->user()->name }}">
            </div>
            <div class="bg-white h-14 flex items-center px-4">
                <x-input wire:model.live="search" type="text" class="w-full" placeholder="Buscar Chat o Contacto"/>

            </div>
            <div class="h-[calc(100vh-10.5rem)] overflow-auto border-t border-gray-200">
                @if ($this->chats->count() == 0 || $this->search)
                    <div class="px-4 py-3">
                        <h2 class="text-teal-600 text-lg mb-4">Contáctos</h2>
                        <ul class="space-y-4">
                            @forelse ($this->contacts as $contact)
                                <li class="cursor-pointer" wire:click="open_chat_contact({{ $contact }})">
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
                @else
                    @foreach ($this->chats as $chatItem)
                        <div wire:key="chats-({{ $chatItem->id }})"
                            wire:click="open_chat({{ $chatItem }})"
                            class="flex items-center justify-between {{ $chat && $chat->id == $chatItem->id ? 'bg-gray-100' : 'bg-white' }} hover:bg-gray-100 cursor-pointer px-3" >

                            <figure>
                                <img class="h-12 w-12 object-cover object-center rounded-full" src="{{ $chatItem->image }}" alt="{{ $chatItem->name }}">
                            </figure>
                            <div class="w-[calc(100%-4rem)] py-4 border-b border-gray-200">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <p>
                                            {{ $chatItem->name }}
                                        </p>
                                        <p class="text-sm text-gray-700 mt-1 truncate">
                                            {{ $chatItem->messages->last()->body }}
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-xs">
                                            {{ $chatItem->lastMessageAt->format('h:i A') }}
                                        </p>
                                        @if ($chatItem->unread_messages)
                                            <span class="inline-flex items-center justify-center px-2 py-1 mr-2 text-xs font-bold leading-none text-green-100 bg-green-600 rounded-full">
                                                {{ $chatItem->unread_messages }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
        <div class="col-span-2">
            @if ($contactChat || $chat)
                <div class="bg-gray-100 h-16 flex items-center px-3">
                    <figure>
                        @if ($chat)
                            <img class="h-12 w-12 rounded-full" src="{{ $chat->image }}" alt="{{ $chat->name }}">
                        @else
                            <img class="h-12 w-12 rounded-full" src="{{ $contactChat->user->profile_photo_url }}" alt="{{ $contactChat->name }}">
                        @endif
                    </figure>
                    <div class="flex-1 ml-5">
                        <p class="text-gray-800">
                            @if ($chat)
                                {{ $chat->name }}
                            @else
                                {{ $contactChat->name }}
                            @endif
                        </p>
                        <p class="text-gray-600 text-xs" x-show="chat_id == typingChatId">
                            Escribiendo ...
                        </p>
                        @if($this->active)
                            <p class="text-green-600 text-xs" x-show="chat_id != typingChatId" wire:key="online">
                                En linea
                            </p>
                        @else
                            <p class="text-red-600 text-xs" x-show="chat_id != typingChatId" wire:key="offline">
                                Desconectado
                            </p>
                        @endif
                    </div>
                </div>
                <div class="h-[calc(100vh-11rem)] px-3 py-2 overflow-auto">
                    {{-- contenido del chat --}}
                    @foreach ($this->messages as $message)
                        <div class="flex {{ $message->user_id == auth()->id() ? 'justify-end' : 'justify-start' }} mb-2">
                            <div class="rounderd px-3 py-2 {{ $message->user_id == auth()->id() ? 'bg-green-100' : 'bg-gray-200' }}">
                                <p class="text-sm">
                                    {{ $message->body }}
                                </p>
                                <p class="{{ $message->user_id == auth()->id() ? 'text-right' : 'text-left' }} text-xs text-gray-600 mt-1">
                                    {{ $message->created_at->format('d-m-y h:i A') }}
                                    @if ($message->user_id == auth()->id())
                                        <i class="fas fa-check-double ml-2 {{ $message->is_read == true ? 'text-blue-500' : 'text-gray-600' }}"></i>
                                    @endif
                                </p>
                            </div>
                        </div>
                    @endforeach
                    <span id="final"></span>
                </div>
                <form class="bg-gray-100 h-16 flex items-center px-4" wire:submit.prevent="sendMessage()">
                    <x-input wire:model.live="bodyMessage" class="flex-1" type="text" placeholder="Escribe un mensaje" autofocus/>
                    <button class="flex-shrink-0 ml-4">
                        <i class="fas fa-share"></i>
                    </button>
                </form>
            @else
                <div class="w-full h-full flex justify-center items-center">
                    <div>
                        <div class="_al_n" style="opacity: 1;"><img src="https://static.whatsapp.net/rsrc.php/v3/y6/r/wa669aeJeom.png" width="450" alt=""></div>
                        <h1 class="text-center text-gray-500 text-2xl mt-4">Descarga Whatsapp para Windows</h1>
                    </div>
                </div>
            @endif
        </div>
    </div>
    @push('js')
        <script>
            function data(){
                return{
                    chat_id: @entangle('chat_id'),
                    typingChatId: null,
                    init(){
                        Echo.private('App.Models.User.' + {{ auth()->id() }})
                        .notification((notification) => {
                            if(notification.type == 'App\\Notifications\\UserTyping'){
                                //console.log("Escribiendo..");
                                this.typingChatId = notification.chat_id;
                                setTimeout(() => {
                                    this.typingChatId = null;
                                }, 2000);
                            }
                        });
                    }
                }
            }

            Livewire.on('scrollIntoView', function () {
                //console.log("Evento 'scrollIntoView' recibido.");
                setTimeout(() => {
                    var finalElement = document.getElementById('final');
                    if (finalElement) {
                        //console.log("Elemento encontrado, desplazándose.");
                        finalElement.scrollIntoView({ behavior: 'smooth', block: 'end' });
                    } else {
                        //console.error("Elemento con id 'final' no encontrado en el DOM.");
                    }
                }, 5); // Retraso de 200 milisegundos
            });
        </script>
    @endpush

</div>
