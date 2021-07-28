<div data-position="auto">
    <form>
        <label>
            <input type="text" name="username" placeholder="Spitzname" required />
            <div class="label">Spitzname</div>
            <div data-info="Suche dir einen noch verfügbaren Spitznamen aus."></div>
            <div data-box="error"></div>
        </label>
        <label>
            <input type="text" name="firstname" placeholder="Vorname" required />
            <div class="label">Vorname</div>
            <div data-info="Bitte gebe hier mindestens einen Vornamen an."></div>
            <div data-box="error"></div>
        </label>
        <label>
            <input type="text" name="lastname" placeholder="Nachname" required />
            <div class="label">Nachname</div>
            <div data-info="Bitte gebe hier deinen Nachnamen an."></div>
            <div data-box="error"></div>
        </label>
        <label>
            <input type="email" name="email" placeholder="E-Mail" required />
            <div class="label">E-Mail</div>
            <div data-info="Bitte gebe hier eine E-Mail-Adresse an, auf die nur du Zugriff hast. Diese
            Adresse wird zur Verifizierung und zur Wiederherstellung benötigt."></div>
            <div data-box="error">Diese E-Mail scheint nicht gültig zu sein.</div>
        </label>
        <label>
            <input type="email" name="email_confirm" placeholder="E-Mail Wiederholung" required />
            <div class="label">E-Mail Wiederholung</div>
            <div data-info="Bitte wiederhole deine E-Mail-Adresse"></div>
            <div data-box="error">E-Mail stimmen nicht überein.</div>
        </label>
        <label>
            <input type="password" name="password" placeholder="Passwort" required />
            <div class="label">Passwort</div>
            <div data-info="Das Passwort muss mindestens 8 Zeichen lang sein und sollte eine Zahl,
            einen Großbuchstaben und ein Sonderzeichen enthalten!" data-mode=""></div>
            <div data-box="error">Keine 8 Zeichen!</div>
        </label>
        <button type="submit" data-redirect="true">Registrieren</button>
        <div data-role="note">Bitte alle Felder richtig ausfüllen.</div>
    </form>
</div>
