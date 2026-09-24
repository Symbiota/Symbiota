const banner_style = document.createElement('style');
banner_style.textContent = `
  .ask {
		position: fixed;
		bottom: 10px;
		right: 10px;
		max-width: 50%;
		background-color: #fafafa;
		border-color: var(--bright-color);
		padding: 15px 20px;
		border-radius: 8px;
		border-style: solid;
		border-width: 6px;
		z-index: 9999;
		animation: slideUp 0.6s ease-out forwards;
	}
	@keyframes slideUp {
		0% { opacity: 0; transform: translateY(70%); }
		100% { opacity: 1; transform: translateY(0); }
	}
	@media (max-width: 768px) {
		.ask{
			top: 10px;
			right: 10px;
			left: 10px;
			max-width: none;
  	}
}
`;
document.head.appendChild(banner_style);

const banner_div = document.createElement('div');
const portal_domain = window.location.hostname.toUpperCase();
banner_div.innerHTML = `
  <div id="ask" class="ask">
		<div style="position:absolute; top:12px; right:10px; display:flex; gap:10px;">
			<a href="https://tinyurl.com/supportsymbiota" target="_blank" class="button" style="background-color:#b9d432; text-decoration:none;" onclick="hideDonation(30*30*24*31);">
				Donate
			</a>
		</div>
		<p>Hello Portal User!
		<br><br>
		Do you use and love ${portal_domain} and its collections?
		<br><br>
		This portal, and others like it, relies on a small, dedicated group of people, the Symbiota Support Hub (SSH) for website support.  
		<br><br>
		Federal funding for the SSH has ended, and this small team is now maintaining 52+ portals and 90 million occurrence records of life on earth… and still growing!
		<br><br>
		Please support this portal through a donation to the SSH.  Doing so helps each collection that shares data here.
		<br><br>
		Thank you very much.
		<br>
		-${portal_domain}, Nico, Ed, Jenn, Katie, Greg
		<br><br>
		<a href="https://symbiota.org/donate/" target="_blank" onclick="hideDonation(30*30*24*31);">More Ways to Support the Portal</a> 
		<div style="position:absolute; bottom:12px; right:10px; display:flex; gap:10px;">
			<button class="button" onclick="hideDonation(30*30*24*7);">Close</button>
		</div>
		</p>
	</div>
`;

if (!(document.cookie.match(/^(.*;)?\s*hide_donate\s*=\s*[^;]+(.*)?$/))) {
    document.body.appendChild(banner_div);
}

function hideDonation(time){
    document.cookie = "hide_donate=true; max-age=" + time + "; path=/; Secure; SameSite=Strict";
    document.getElementById('ask').style.display = 'none';
};
