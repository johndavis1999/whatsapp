<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Contact;
use App\Models\Chat;
use App\Models\Message;

use Illuminate\Support\Facades\Notification;

class ChatComponent extends Component
{
    public $search;
    
    public $contactChat, $chat, $chat_id;
    
    public $bodyMessage;

    public $users;

    public function mount(){
        $this->users = collect();
    }

    //listenners notifications
    public function getListeners()
    {   
        $user_id = auth()->user()->id;

        return [
            "echo-notification:App.Models.User.{$user_id},notification" => 'render',
            "echo-presence:chat.1,here" => 'chatHere',
            "echo-presence:chat.1,joining" => 'chatJoining',
            "echo-presence:chat.1,leaving" => 'chatLeaving',
        ];
    }

    //Ciclo de Vida
    public function updatedBodyMessage($value){
        //if($value){
        //    #dd('has escrito: ' . $value);
        //    Notification::send($this->users_notifications, new \App\Notifications\UserTyping($this->chat->id));
        //}
        //fix abrir nuevo chat
        
        if (empty($this->users_notifications->first()->id)){
            return collect();
        }
        else{
            if($value){
                Notification::send($this->users_notifications, new \App\Notifications\UserTyping($this->chat->id));  
            }
        }
    }


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
            $this->chat_id = $chat->id;
            $this->reset('contactChat', 'bodyMessage', 'search');
        }else{
            $this->contactChat = $contact;
            $this->reset('chat', 'bodyMessage', 'search');
        }
    }

    public function open_chat(Chat $chat)
    {
        $this->chat = $chat;
        $this->chat_id = $chat->id;
        $this->reset('contactChat', 'bodyMessage');
        
        $chat->messages()->where('user_id', '!=', auth()->id())->where('is_read', false)->update([
            'is_read' => true
        ]);
        Notification::send($this->users_notifications, new \App\Notifications\NewMessage());
    }

    public function sendMessage(Contact $contact)
    {
        $this->validate([
            'bodyMessage' => 'required'
        ]);

        if(!$this->chat){
            $this->chat = Chat::create();
            //$this->chat_id = $chat->id;
            // fix envio de mensajes a contacto nuevo, revisar y verificar si funca en otros casos de uso
            $this->chat_id = $this->chat;
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
        return $this->chat ? $this->chat->users->where('id', '!=', auth()->id()) : collect();
    }

    public function getActiveProperty(){
        //return $this->users->contains($this->users_notifications->first()->id);
        //fix error al abrir nuevo chat
        if (empty($this->users_notifications->first()->id)){
            return collect();
        }else{
            return $this->users->contains($this->users_notifications->first()->id);
        }
    }

    public function chatHere($users){
        #dd($event);
        $this->users = collect($users)->pluck('id');
    }

    public function chatJoining($user){
        #dd($event);
        $this->users->push($user['id']);
    }

    public function chatLeaving($user){
        #dd($event);
        $this->users = $this->users->filter(function($id) use($user){
            return $id != $user['id'];
        });
    }
    
    public function render()
    {
        if($this->chat){
            //buscar otras alternativas a esta funcionalidad, ya que se genera un bucle y llega a consumir muchos recursos
            //----------
            $this->chat->messages()->where('user_id', '!=', auth()->id())->where('is_read', false)->update([
                'is_read' => true
            ]);
            //----------
            Notification::send($this->users_notifications, new \App\Notifications\NewMessage());
            $this->dispatch('scrollIntoView');
        }
        return view('livewire.chat-component')->layout('layouts.chat');
    }
}