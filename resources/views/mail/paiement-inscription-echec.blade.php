<p>Bonjour {{ $inscription->prenom ?? 'Cher utilisateur' }},</p>

<p>Votre tentative de paiement pour l'inscription a échoué.</p>

<p>Vous pouvez reprendre votre inscription en cliquant ici :</p>

<p>
    <a href="{{ url('/inscription/reprendre/' . $inscription->token) }}">
        Reprendre mon inscription
    </a>
</p>

<p>Merci,<br>L'équipe</p>
