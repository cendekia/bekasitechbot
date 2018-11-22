<?php

namespace App\Http\Controllers;

use BotMan\BotMan\BotMan;
use Illuminate\Http\Request;
use BotMan\BotMan\Middleware\Dialogflow;
use App\Conversations\ExampleConversation;

class BotManController extends Controller
{
    /**
     * Place your BotMan logic here.
     */
    public function handle()
    {
        $dialogflow = Dialogflow::create(env('DIALOGFLOW_TOKEN'))->listenForAction();

        $botman = app('botman');
        $botman->middleware->received($dialogflow);

        $botman->hears('btt:.*', function (BotMan $bot) {
            $extras = $bot->getMessage()->getExtras();
            $user = $bot->getUser();

            // mockup user data
            $bot->userStorage()->save([
                'id' => $user->getId(),
                'first_name' => $user->getFirstName(),
                'last_name' => $user->getLastName(),
                'username' => $user->getUsername(),
                'user_info' => [],
            ]);

            \Log::debug($extras);
            foreach ($extras['apiTextResponses'] as $key => $data) {
                $bot->typesAndWaits(rand(1,2));
                $bot->reply($data['speech']);   
            }
        })->middleware($dialogflow);

        $botman->listen();
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function tinker()
    {
        return view('tinker');
    }

    /**
     * Loaded through routes/botman.php
     * @param  BotMan $bot
     */
    public function startConversation(BotMan $bot)
    {
        $bot->startConversation(new ExampleConversation());
    }
}
