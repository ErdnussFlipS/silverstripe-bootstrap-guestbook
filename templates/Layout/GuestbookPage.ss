<% require css(guestbook/css/guestbook.css) %>
<% require javascript(guestbook/javascript/guestbook.js) %>

<div class="guestbook">
	<article>
		<h1>$Title <small>$SubTitle</small></h1>
		<% if $Content %>
			<div class="content">$Content</div>
		<% end_if %>
	</article>

<% include GuestbookEntries %>

<% include GuestbookPagination %>

<% include GuestbookNewEntry %>
</div>
