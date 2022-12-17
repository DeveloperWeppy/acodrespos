<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class SolicitudPqrNotification extends Notification
{
    use Queueable;

    protected $radicat_pqr;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($radicat_pqr)
    {
        $this->radicat_pqr = $radicat_pqr;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $estado = '';
        if ($this->radicat_pqr->status=='en revision') {
            $estado = __('La solicitud de '.$this->radicat_pqr->type_radicate. ' se encuentra En Revisión Pronto tendrás respuesta a tu solicitud.');
        } else if($this->radicat_pqr->status=='Solucionado'){
            $estado = __('La solicitud de '.$this->radicat_pqr->type_radicate. ' ha sido Respondida y Finalizada Para darle seguimiento a la solicitud de clic en el siguiente enlace.');
        }

        $estado=$estado.'<center><br><div>Consecutivo de Solicitud</div><div><b>'.$this->radicat_pqr->consecutive_case.'</b></div></center>';
        
        return (new MailMessage)
        ->greeting(__('Nueva Notificación de Solicitud en Centro de Ayuda'))
        ->subject(__( $this->radicat_pqr->name.' tienes una nueva notificación')."-".$this->radicat_pqr->consecutive_case)
        ->line(new HtmlString($estado))
        ->action(__('Ver Estado de la solicitud'), url('/detalle-solicitud/'.$this->radicat_pqr->id));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
