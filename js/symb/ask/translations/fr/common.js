const common = {
    header: 'Bonjour, Utilisateur de Portal !',
    main: (content) => `<p style="font-size:17px; font-weight:600;">Utilisez-vous et appréciez-vous ${content.domain} et ses collections ?</p>
        <p>Ce portail, ainsi que d'autres similaires, s'appuie sur un <strong>petit groupe de personnes dévouées</strong>, le Symbiota Support Hub (SSH), pour l'assistance technique du site web.</p>
        <p><strong style="color:red;">Le financement fédéral du SSH a pris fin</strong>, et cette petite équipe assure désormais la maintenance de <mark>plus de 52 portails</mark> et de <mark>90 millions d'enregistrements d'occurrences</mark> de la vie sur Terre… et ce chiffre ne cesse d'augmenter !</p>
        <p><a href="${content.donate_url}" target="_blank" onclick="setDonateCookie(60*60*24*31);">Merci de soutenir ce portail en faisant un don au SSH.</a> Cela aide chacune des collections qui partagent leurs données ici.</p>
        <p>Merci beaucoup.<br>-${content.domain}, Nico, Ed, Jenn, Katie, Greg</p>`,
    close: 'Fermer',
    donate: 'Donner',
    more_ways: ' D\'autres Façons de Soutenir le Portail'
};