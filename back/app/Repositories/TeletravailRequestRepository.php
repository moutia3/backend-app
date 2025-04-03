<?php

namespace App\Repositories;

use App\Models\TeletravailRequest;
use Illuminate\Support\Facades\Mail;

class TeletravailRequestRepository implements TeletravailRequestRepositoryInterface
{
    public function create(array $data)
    {
        return TeletravailRequest::create($data);
    }
    
    public function update($id, array $data)
    {
        $teletravailRequest = TeletravailRequest::find($id);

        if (!$teletravailRequest) {
            return null;
        }

        $teletravailRequest->update($data);

        return $teletravailRequest;
    }

    public function find($id)
    {
        return TeletravailRequest::find($id);
    }

    public function findByUser($userId, $page = 1, $limit = 6)
    {
        return TeletravailRequest::where('user_id', $userId)
            ->paginate($limit, ['*'], 'page', $page);
    }

    public function updateStatus($id, $status)
    {
        $request = TeletravailRequest::find($id);
        
        if (!$request) {
            return null;
        }

        $request->status = $status;
        $request->save();

        return $request;
    }

    public function sendStatusEmail($email, $requestDetails)
{
    $statusText = [
        'approved' => 'Approuvé',
        'rejected' => 'Rejeté',
        'pending' => 'En attente'
    ][$requestDetails['status']] ?? $requestDetails['status'];

    $statusClass = strtolower($statusText);

    $html = "<!DOCTYPE html>
    <html>
    <head>
        <title>Mise à jour demande télétravail</title>
        <style>
            .status { padding: 5px 10px; border-radius: 3px; font-weight: bold; }
            .approuvé { background-color: #d4edda; color: #155724; }
            .rejeté { background-color: #f8d7da; color: #721c24; }
            .en-attente { background-color: #fff3cd; color: #856404; }
        </style>
    </head>
    <body>
        <p>Bonjour {$requestDetails['user_name']},</p>
        <p>Votre demande pour le {$requestDetails['date']} est maintenant <span class='status $statusClass'>$statusText</span>.</p>
        " . ($requestDetails['status'] === 'rejected' ? "<p>Contactez votre manager pour plus d'informations.</p>" : "") . "
        <p>Cordialement,<br>RH</p>
    </body>
    </html>";

    Mail::send([], [], function ($message) use ($email, $html) {
        $message->to($email)
               ->subject('Mise à jour demande télétravail')
               ->html($html);
    });
}
}