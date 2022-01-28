<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InviteFriendMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    private $user , $tournament;

    public function __construct($user, $tournament)
    {
        $this->user = $user;
        $this->tournament = $tournament;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('noreply@domain.com')
                ->markdown('template.inviteFriend')
                ->with([
                        'subject' => 'Invite for a game',
                        'greeting' => 'Hello '. ucfirst($this->user['name']),
                        'message' => 'You have just been invited to '. ucfirst($this->tournament['name']) . ' game',
                    ]);
    }
}
