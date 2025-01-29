<div x-data="data()" class="bg-gray-50 rounded-lg shadow border border-gray-200 overflow-hidden "> 
    
    
    <div class="grid grid-cols-3 divide-x divide-gray-200">

        {{-- Columna de contáctos --}}
        <div class="col-span-1">
            <div class="bg-gray-100 h-16 flex items-center px-4">
                
                <img class="h-10 w-10 rounded-full"
                src="{{ auth()->user()->profile_photo_url }}" alt="{{ auth()->user()->name }}" />

            </div>
            {{-- Busqueda de contactos --}}
            <div class="h-14 flex items-center px-4 bg-white " >
                <x-input type="text" wire:model.live="search" 
                class="w-full" placeholder="Busque un chat o inicie uno nuevo" />
            </div>

            {{-- Lista de contactos --}}
            <div class="h-[76vh] overflow-auto border-t  border-gray-200 ">
                @if ($this->chats->count() == 0 || $search)

                    <div class=" px-4 py-3 ">
                        <h2 class="text-teal-600 text-lg mb-4" >Contáctos</h2>
                    
                        <ul class="divide-y divide-gray-200">
                            @forelse ($this->contacts as $contac)
                                <li class="cursor-pointer" wire:click="open_Chat_contact({{ $contac}})">
                                    <div class="flex items-center px-4 py-3 hover:bg-gray-100" >
                                        <img class="h-10 w-10 rounded-full"
                                        src="{{ $contac->user->profile_photo_url }}" alt="{{ $contac->name }}" />
                                        <div class="ml-4" >
                                            <h3 class="text-lg font-semibold" >{{ $contac->name }}</h3>
                                            <p class="text-gray-500" >{{ $contac->user->email }}</p>
                                        </div>
                                    </div>
                                </li>
                            @empty
                                <li class="px-4 py-3" >
                                    <p class="text-gray-500" >No hay contáctos</p>
                                </li>
                                
                            @endforelse

                        </ul>
                    </div>

                @else
                    @foreach ($this->chats as $chatItem)

                        <div    
                            wire:key="chats-{{ $chatItem->id }}"
                            wire:click="open_Chat({{ $chatItem }})"
                            class="{{ $chat && $chat->id == $chatItem->id ? 'bg-gray-200' : 'bg-white' }} 
                                    hover:bg-gray-100 px-4 cursor-pointer flex items-center" 
                            >
                            
                            <figure>
                                <img class="h-12 w-12 object-cover object-center rounded-full " 
                                    src="{{ $chatItem->image }}" alt="{{ $chatItem->name }}" />
                            </figure>

                            <div class="w-[calc(100%-4rem)] ml-4 py-4 border-b border-gray-200" >
                                <div class="flex justify-between items-center" >
                                    <p class="text-lg font-semibold" >
                                        {{ $chatItem->name }}
                                    </p>
                                    <p class="text-xs" >
                                        {{ $chatItem->messages->last()->created_at->diffForHumans() }}
                                    </p>
                                </div>
                                <p class="text-gray-500 text-xs truncate " >
                                    {{ $chatItem->messages->last()->body }}
                                </p>
                            </div>
                            

                        </div>
                        
                    @endforeach
                @endif
                
            </div>


        </div>

        {{-- Columna del Chat --}}
        <div class="col-span-2">

            @if ($contactChat || $chat)
                
                <div class="bg-gray-100 h-16 flex items-center px-4">
                    <figure>
                        @if ($chat)
                            <img class="h-10 w-10 rounded-full object-cover object-center " src="{{ $chat->image }}"  alt="{{ $chat->name }}" />
                        @else
                            <img class="h-10 w-10 rounded-full object-cover object-center " src="{{ $contactChat->user->profile_photo_url }}"  alt="{{ $contactChat->name }}" />
                        @endif
                    </figure>
                    <div class="ml-4" >
                        <h2 class="text-lg font-semibold" >
                            @if ($chat)
                                {{ $chat->name }}
                            @else
                                {{ $contactChat->name }}
                            @endif
                        </h2>
                        <p class="text-gray-600 text-xs " x-show="chat_id == typingChatId" >
                            Escribiendo...
                        </p>
                        <p class="text-green-500 text-xs " x-show="chat_id != typingChatId " >
                            Online
                        </p>
                    </div>
                </div>

                {{-- Mostrar mensajes --}}
                <div class="h-[75vh] overflow-auto border-t border-gray-200 px-3 py-2">
                    @foreach ($this->messages as $message)

                        <div class=" flex {{ $message->user_id == auth()->id() ? 'justify-end' : 'justify-start' }}">
                            <div class=" rounded px-3 py-2 {{ $message->user_id == auth()->id() ? 'bg-green-100 mt-2' : 'bg-gray-100 mt-2' }}" >
                                <p class="text-sm" >
                                    {{ $message->body }}
                                </p>
                                <p class="text-xs mt-1 text-gray-600 {{ $message->user_id == auth()->id() ? 'text-right' : 'text-left' }}" >
                                    {{ $message->created_at->diffForHumans() }}
                                </p>
                            </div>
                        </div>
                        
                    @endforeach
                    <span id="endChat"></span>
                </div>

                {{-- Boton de envio de mensaje --}}
                <form class="bg-gray-100 h-16 flex items-center px-4" wire:submit.prevent="sendMessage()">

                    <x-input wire:model.live='bodyMessage' type="text" class="flex-1" placeholder="Escribe un mensaje" />

                    <button class="bg-green-500 text-white px-4 py-2 rounded-lg flex-shrink-0 ml-4" >
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </form>

            @else
                <div class="w-full h-full flex items-center justify-center opacity-20">
                    <img srcset="https://cdn.pixabay.com/photo/2017/02/26/00/04/jigsaw-2099124_960_720.png 1x, https://cdn.pixabay.com/photo/2017/02/26/00/04/jigsaw-2099124_1280.png 2x" src="https://cdn.pixabay.com/photo/2017/02/26/00/04/jigsaw-2099124_1280.png" alt="Rompecabezas, Piezas, Resumen, Arte"
                    class="h-64 w-64" />
                    <h1 class=" text-center text-gray-500 text-2xl mt-4" >
                        Originaria Chat
                    </h1>
                    
                </div>
                
            @endif
            

        </div>
    </div>

    @push('js')
        <script>

            function data(){
                return {

                    chat_id: @entangle('chat_id').live,
                    typingChatId: null,

                    init(){
                        Echo.private('App.Models.User.' + {{ auth()->id() }})
                            .notification((notification) => {
                                
                                if(notification.type == 'App\\Notifications\\UserTyping'){
                                    //Livewire.emit('newMessage', notification.message);
                                    this.typingChatId = notification.chat_id;
                                    
                                    setTimeout(() => {
                                        this.typingChatId = null;
                                    }, 3000);
                                }

                            });
                    }
                }
            }

            Livewire.on('scrollIntoView', function(){
                document.getElementById('endChat').scrollIntoView();
            });
        </script>
    @endpush

</div>
