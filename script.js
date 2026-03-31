$(() => {
	if (!document.cookie.includes("user_timezone") && navigator.cookieEnabled) {
		const userTimezone = Intl.DateTimeFormat().resolvedOptions().timeZone
		document.cookie = `user_timezone=${encodeURIComponent(userTimezone)}; max-age=${30 * 24 * 60 * 60}; path=/; SameSite=Lax`
		location.reload()
	}
	
	load_details_toggle_state()
	
	const url_params = new URLSearchParams(window.location.search)
	if (url_params.has('reply')) {
		const id = url_params.get('reply')
		quick_reply(id)
	}

	//update the highlighted div to the hash parameter
	update_highlighted_reply()
	$(window).on('hashchange', update_highlighted_reply)

	//listener for id links to make a quick reply
	$('.quick-reply-link').on('click', (e) => {
		if (!window.location.href.includes('/t/')) return
		let id = e.target.name
		quick_reply(id)
	})

	$(document).on('paste', (e) => {
		const clipboardData = e.clipboardData || e.originalEvent.clipboardData
		const items = clipboardData.items
		
		for (let i = 0; i < items.length; i++) {
			if (items[i].type.indexOf('image') !== -1) {
				const blob = items[i].getAsFile()
				const dataTransfer = new DataTransfer()
				dataTransfer.items.add(blob)
				$(':file')[0].files = dataTransfer.files
				break
			}
		}
	})

	$('details').on('toggle', (e) => {
		let id = e.target.id
		let is_open = $(`#${id}`).attr('open') === 'open'
		localStorage.setItem(id, is_open)
	})
	
	function update_highlighted_reply() {
		const hash = parseInt(window.location.hash.substring(1))
		if (isNaN(hash)) return
		$(`.highlighted`).removeClass('highlighted')
		$(`#${hash}`).addClass('highlighted')
	}

	function load_details_toggle_state() {
		let thread_toggle_id = 'thread-details'
		let reply_toggle_id = 'reply-details'
		let thread_toggle = localStorage.getItem(thread_toggle_id) === 'true'
		let reply_toggle = localStorage.getItem(reply_toggle_id) === 'true'
		
		if (thread_toggle) $(`#${thread_toggle_id}`).attr('open', '')
		if (reply_toggle) $(`#${reply_toggle_id}`).attr('open', '')    
	}

	//open the comment field and append a reply
	function quick_reply(id) {
		$('details').attr('open', '')
		let contents = $('#comment').val()
		$('#comment').val(contents + '>>' + id + '\n')
		$('#comment').focus()
	}
})