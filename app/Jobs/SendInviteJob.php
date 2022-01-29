<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use App\Mail\InviteFriendMail;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class SendInviteJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $user, $tournament;
    public function __construct($user , $tournament)
    {
        $this->user = $user;
        $this->tournament = $tournament;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $payload = (object) [
            "from" => env('FROM'),
            "destinations" => [
                [
                    "to" => env('SIGNUP_PHONE_NUMBER'),
                ]
            ],
            "text" => "You have just been invited to ". ucfirst($this->tournament['name']) . " game",
        ];

        $response = Http::withHeaders([
            'Authorization' => env('API_KEY_PREFIX'). " ". env('API_KEY'),
            'Content-Type' =>  'application/json',
            'Accept' => 'application/json'
        ])->post(env('URL_BASE_PATH'). '/sms/2/text/advanced', ['messages' => $payload]);
        
        Mail::to($this->user->email)->send(new InviteFriendMail($this->user, $this->tournament));
    }
}
