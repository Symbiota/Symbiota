const template_values = {
	domain: window.location.hostname.toUpperCase(),
	donate_url: Donate_link
};
const ask_lang = { ...verbage, ...overrides };
const banner_style = document.createElement('style');

banner_style.textContent = `
	.ask {
		position: fixed;
		bottom: 10px;
		right: 10px;
		max-width: 50%;
		max-height: 60%;
		border: none;
		border-radius: 8px;
		overflow: hidden;
		box-shadow: 0 8px 30px rgba(0, 0, 0, 0.16);
		z-index: 9999;
		animation: slideUp 0.6s ease-out forwards;
		display: flex;
    	flex-direction: column;	
	}

	.ask-header {
	    flex-shrink: 0;
		height: 70px;
		padding: 10px 28px;
		background: var(--menu-top-bg-color);
		display: flex;
		align-items: center;
		justify-content: space-between;
	}

	.ask-content {
		padding: 4px 26px;
		background: #fff;
		font-size: 16px;
		line-height: 1.6;
		overflow-y: auto;
		min-height: 0;
	}

	.ask-content mark {
		background-color: #eff8d9;
		color: #186536;
	}

	.ask-footer {
		min-height: 50px;
		padding: 12px 28px;
		border-top: 1px solid #ddd;
		background: #fff;
		display: flex;
		align-items: center;
		justify-content: space-between;
	}

	@keyframes slideUp {
		0% { opacity: 0; transform: translateY(75%); }
		100% { opacity: 1; transform: translateY(0); }
	}

	@media (max-width: 600px) {
		.ask {
			left: 10px;
			max-width: none;
			max-height: 50%;
  		}
	}
`;

const banner_div = document.createElement('div');

banner_div.innerHTML = `
	<div id="ask" class="ask">

		<div class="ask-header">
			<h2 style="color:#fff; font-size:23px;">${ask_lang.header}</h2>

			<button type="button" class="button" onclick="setaskCookie(60*60*24*7); hideDonation();">
				${ask_lang.close}
			</button>
		</div>

		<div class="ask-content">
			${ask_lang.main(template_values)}
		</div>

		<div class="ask-footer">
			<a href="${Donate_link}" target="_blank" class="button" style="background-color:var(--darkest-color); color:#fff; text-decoration:none;" onclick="setaskCookie(60*60*24*31);">
				${ask_lang.ask}
			</a>

			<a href="https://symbiota.org/donate" target="_blank" style="color:var(--link-color); text-decoration:underline;" onclick="setaskCookie(60*60*24*31);">
				${ask_lang.more_ways}
			</a>
		</div>
	</div>
`;

if (!(document.cookie.match(/^(.*;)?\s*hide_ask\s*=\s*[^;]+(.*)?$/))) {
	document.head.appendChild(banner_style);
    document.body.appendChild(banner_div);
}

function setaskCookie(time){
    document.cookie = "hide_ask=true; max-age=" + time + "; path=/; Secure; SameSite=Strict";
};

function hideDonation(){
	document.getElementById('ask').style.display = 'none';
}
