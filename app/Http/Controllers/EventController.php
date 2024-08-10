<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Event;

use App\Models\User;

class EventController extends Controller
{
    public function index() {

        $search = request('search');

        if($search) {

            $events = Event::where([
                ['title', 'like', '%'.$search.'%']
            ])->get();

        }else {
            $events = Event::all();
        }
    
        return view('welcome', ['events' => $events, 'search' => $search ]);
        
    }

    public function create() {
        return view('events.create');
    }

    public function store(Request $request) {

        $event = New Event;

        $event->title = $request->title;
        $event->date = $request->date;
        $event->city = $request->city;
        $event->private = $request->private;
        $event->description = $request->description;
        $event->items = $request->items;

        // Image upload
        if($request->hasFile('image') && $request->file('image')->isValid()) {
            
            $requestImage = $request->image;

            // Tipo do arquivo ex: .jpg .jpeg
            $extension = $requestImage->extension();

            // Para que o arquivo seja unico
            $imageName = md5($requestImage->getClientOriginalName().strtotime("now")).".".$extension;

            $requestImage->move(public_path('img/events'), $imageName);

            $event->image = $imageName;
        }

        $user = auth()->user();
        $event->user_id = $user->id;
        

        $event->save();

        return redirect('/')->with('msg', 'Evento Criado com Sucesso!');

    }

    public function show($id) {
        $event = Event::findOrFail($id);


        $eventOwner = User::where('id', $event->user_id)->first()->toArray();

        return view('events.show', ['event' => $event, 'eventOwner' => $eventOwner]);
    }

    public function dashboard() {

        $user = auth()->user();

        $events = $user->events;

        $eventsAsParticipant = $user->eventsAsParticipant;

        return view('events.dashboard', ['events' => $events, 'eventsasparticipantes' => $eventsAsParticipant]);

    }

    public function destroy($id) {

        Event::findOrFail($id)->delete();

        return redirect('/dashboard')->with('msg', 'Evento excluído com sucesso!');

    }

    public function edit($id) {

        $user = auth()->user();

        $event =  Event::findOrFail($id);

        if($user->id != $event->user_id){
            return redirect('/dashboard');
        }
        
        return view('events.edit', ['event' => $event]);

    }

    public function update(Request $request) {

        $data = $request->all();

        // Tenta obter a imagem da requisição
        $requestImage = $request->image;

        if ($requestImage) {
            // Tipo do arquivo ex: .jpg .jpeg
            $extension = $requestImage->extension();

            // Para que o arquivo seja único
            $imageName = md5($requestImage->getClientOriginalName().strtotime("now")).".".$extension;

            // Move a imagem para a pasta de eventos
            $requestImage->move(public_path('img/events'), $imageName);

            // Adiciona o nome da imagem aos dados
            $data['image'] = $imageName;
        } else {
            // Se não houver imagem, não altera o campo 'image' no banco de dados
            unset($data['image']);
        }

        Event::findOrFail($request->id)->update($data);

        return redirect('/dashboard')->with('msg', 'Evento editado com sucesso!');

    }

    public function joinEvent($id) {

        $user = auth()->user();

        // Verificar se o usuário já está confirmado no evento
        if ($user->eventsAsParticipant()->where('event_id', $id)->exists()) {
            return redirect('/dashboard')->with('msg', 'Você já está confirmado nesse evento.');
        }

        // attach faz a ligação
        $user->eventsAsParticipant()->attach($id);

        $event = Event::findOrFail($id);

        return redirect('/dashboard')->with('msg', 'Sua Presença esta confirmada no evento ' . $event->title);

    }

    public function leaveEvent($id) {

        $user = auth()->user();

        $user->eventsAsParticipant()->detach($id);

        $event = Event::findOrFail($id);       

        return redirect('/dashboard')->with('msg', 'Voçê saiu com sucesso do Evento: ' . $event->title);

    }   

}
