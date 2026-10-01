const verbage = {
    header: 'Hello Portal User!',
    main: (content) => `<p style="font-size:17px; font-weight:600;">Do you use and love ${content.domain} and its collections?</p>
        <p>This portal, and others like it, relies on a <strong>small, dedicated group of people</strong>, the Symbiota Support Hub (SSH) for website support.</p>
        <p><strong style="color:red;">Federal funding for the SSH has ended</strong>, and this small team is now maintaining <mark>52+ portals</mark> and <mark>90 million occurrence records</mark> of life on earth... and still growing!</p>
        <p><a href="${content.donate_url}" target="_blank" onclick="setDonateCookie(60*60*24*31);">Please support this portal through a donation to the SSH.</a> Doing so helps each collection that shares data here.</p>
        <p>Thank you very much.<br>-${content.domain}, Nico, Ed, Jenn, Katie, Greg</p>`,
    close: 'Close',
    ask: 'Donate',
    more_ways: 'More Ways to Support the Portal'
};