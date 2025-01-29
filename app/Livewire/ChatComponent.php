<?php

namespace App\Livewire;

use App\Models\Chat;
use App\Models\Contact;
use App\Models\Message;
use App\Notifications\NewMessage;
use App\Notifications\UserTyping;
use Livewire\Component;
use Illuminate\Support\Facades\Notification;

class ChatComponent extends Component
{
    public $search;

    public $contactChat;

    public $chat, $chat_id;

    public $bodyMessage;

    protected $listeners = ['notifyNewOrder' => 'notifyNewOrder'];


    //     Listeners       //

    public function getListeners()
    {
        $user_id = auth()->id();

        return [
            "echo-notification:App.Models.User.{$user_id},notification" => 'render',
        ];
    }


    //      Propiedades      //

    //propiedad computada
    public function getContactsProperty()
    {
        return Contact::where('user_id', auth()->id())
            ->when($this->search, function ($query) {
                $query->where(function($query){

                    $query->where('name', 'like', '%' . $this->search . '%')
                        ->orWhereHas('user', function ($query) {
                            $query->where('email', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->get() ?? [];
    }

    //Recupero mensajes del chat seleccionado si existe
    public function getMessagesProperty()
    {
        return $this->chat ? Message::where('chat_id', $this->chat->id)
            ->with('user')
            ->get() : [];
    }

    //Recupero los chats del usuario logueado
    public function getChatsProperty()
    {
        return auth()->user()->chats()->get()->sortByDesc('lastMessageAt');
    }

    public function getUsersNotificationsProperty()
    {
        return $this->chat ? $this->chat->users->where('id', '!=', auth()->id()) : [];
    }

    //      Ciclos de Vida       //

    public function updatedBodyMessage($value)
    {
        if($value)
        {
            Notification::send($this->Users_Notifications, new UserTyping($this->chat->id));
        }
    }

    //      Metodos         //
    public function open_Chat_contact(Contact $contact)
    {
        $chat = auth()->user()->chats()
                    ->whereHas('users', function ($query) use ($contact) {
                        $query->where('user_id', $contact->contact_id);
                    })
                    ->has('users', 2)
                    ->first();
        
        if ($chat) {
            $this->chat = $chat;
            $this->chat_id = $chat->id;
            $this->reset('contactChat', 'bodyMessage', 'search');
        } else {
            $this->contactChat = $contact;
            $this->reset('chat', 'bodyMessage', 'search');
        }
    }

    public function open_Chat(Chat $chat)
    {
        $this->chat = $chat;
        $this->chat_id = $chat->id;
        $this->reset('contactChat', 'bodyMessage');

        if($this->chat) {
            $this->dispatch('scrollIntoView');
        }

    }

    public function sendMessage()
    {
        $this->validate([
            'bodyMessage' => 'required'
        ]);

        if(!$this->chat) {
            $this->chat = Chat::create();
            $this->chat_id = $this->chat->id;
            $this->chat->users()->attach([auth()->user()->id, $this->contactChat->contact_id ]);
        }

        $this->chat->messages()->create([
            'body' => $this->bodyMessage,
            'user_id' => auth()->user()->id
        ]);

        Notification::send($this->Users_Notifications, new NewMessage());
        
        $this->reset('bodyMessage', 'contactChat');
    }

    public function render()
    {
        if($this->chat) {
            $this->dispatch('scrollIntoView');
        }

        return view('livewire.chat-component')
            ->layout('layouts.chat', ['title' => 'Chat']);
    }
}
