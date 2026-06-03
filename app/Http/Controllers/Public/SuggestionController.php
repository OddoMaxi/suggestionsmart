<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSuggestionRequest;
use App\Models\Agence;
use App\Models\Service;
use App\Models\Suggestion;
use App\Notifications\NouvelleSuggestionNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class SuggestionController extends Controller
{
    public function formulaire(Request $request)
    {
        $services = Service::actif()
            ->with('agence')
            ->orderBy('ordre')
            ->orderBy('nom')
            ->get();

        $agences = Agence::actif()->orderBy('nom')->get();

        $serviceSelectionne = null;
        $agenceSelectionnee = null;

        if ($request->filled('service')) {
            $serviceSelectionne = Service::where('slug', $request->service)->first();
        }

        if ($request->filled('agence')) {
            $agenceSelectionnee = Agence::where('slug', $request->agence)->first();
        }

        return view('public.suggestion', compact(
            'services', 'agences', 'serviceSelectionne', 'agenceSelectionnee'
        ));
    }

    public function enregistrer(StoreSuggestionRequest $request)
    {
        $service = Service::findOrFail($request->service_id);

        $suggestion = Suggestion::create([
            ...$request->validated(),
            'agence_id'  => $service->agence_id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'canal'      => 'qr_code',
        ]);

        // Notifications email
        $this->envoyerNotifications($suggestion);

        return redirect()->route('suggestion.merci', $suggestion->reference);
    }

    public function merci(string $reference)
    {
        $suggestion = Suggestion::where('reference', $reference)->firstOrFail();
        return view('public.merci', compact('suggestion'));
    }

    public function suiviForm()
    {
        return view('public.suivi');
    }

    public function suiviResultat(string $reference)
    {
        $suggestion = Suggestion::where('reference', $reference)
            ->withTrashed(false)
            ->first();

        return view('public.suivi', compact('suggestion', 'reference'));
    }

    private function envoyerNotifications(Suggestion $suggestion): void
    {
        try {
            $adminEmail = \App\Models\Setting::get('email_notification', config('mail.from.address'));
            $emails = [$adminEmail];

            if ($suggestion->service->email_responsable) {
                $emails[] = $suggestion->service->email_responsable;
            }

            foreach (array_unique(array_filter($emails)) as $email) {
                Notification::route('mail', $email)
                    ->notify(new NouvelleSuggestionNotification($suggestion));
            }
        } catch (\Exception $e) {
            \Log::warning('Notification email échouée: ' . $e->getMessage());
        }
    }
}
