<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Contact;
use App\Models\Chat;
use App\Models\Message;

use Illuminate\SUpport\Facades\Notification;

class ChatComponent extends Component
{
    public $search;
    
    public $contactChat, $chat;
    
    public $bodyMessage;

    // Metodos
    // Propiedad computada para obtener contactos basados en la búsqueda
    public function getContactsProperty()
    {
        return Contact::where('user_id', auth()->id())
            ->when($this->search, function($query){
                $query->where(function($query){
                    $query->where('name', 'like', '%'.$this->search.'%')
                            ->orWhereHas('user', function($query){
                                $query->where('email', 'like', '%'.$this->search.'%')
                                        ->where('name', 'like', '%'.$this->search.'%');
                            });
                });
            })
            ->get() ?? [];
    }

    public function open_chat_contact(Contact $contact)
    {
        $chat = auth()->user()->chats()
                    ->whereHas('users', function($query) use ($contact){
                        $query->where('user_id', $contact->contact_id);
                    })
                    ->has('users', 2)
                    ->first();
        if($chat){
            $this->chat = $chat;
            $this->reset('contactChat', 'bodyMessage', 'search');
        }else{
            $this->contactChat = $contact;
            $this->reset('chat', 'bodyMessage', 'search');
        }
    }

    public function open_chat(Chat $chat)
    {
        $this->chat = $chat;
        $this->reset('contactChat', 'bodyMessage');
        
    }

    public function sendMessage(Contact $contact)
    {
        $this->validate([
            'bodyMessage' => 'required'
        ]);

        if(!$this->chat){
            $this->chat = Chat::create();
            $this->chat->users()->attach([auth()->user()->id, $this->contactChat->contact_id]);
        }

        $this->chat->messages()->create([
            'body' => $this->bodyMessage,
            'user_id' => auth()->user()->id
        ]);

        #dd($this->users_notifications);
        Notification::send($this->users_notifications, new \App\Notifications\NewMessage($this->chat));

        $this->reset('bodyMessage', 'contactChat');
    }

    public function getMessagesProperty(){
        #return $this->chat ? Message::where('chat_id', $this->chat->id)->get() : [];
        //optimizacion de consulta
        return $this->chat ? $this->chat->messages()->get() : [];
    }

    public function getChatsProperty(){
        return auth()->user()->chats()->get()->sortByDesc('last_message_at');
    }

    public function getUsersNotificationsProperty(){
        return $this->chat ? $this->chat->users->where('id', '!=', auth()->id()) : [];
    }
    
    public function render()
    {
        return view('livewire.chat-component')->layout('layouts.chat');
    }
}
