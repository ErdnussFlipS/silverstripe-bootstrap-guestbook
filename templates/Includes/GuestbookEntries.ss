<% if PaginatedEntries.Count == 0 %>
	<article>
			<p class="no-entries-message">
				<%t GuestbookPage_ss.NOENTRIES "Be the first to sign this guestbook!" %>
			</p>
	</article>
<% end_if %>

<% loop $PaginatedEntries %>
	<article class="entry well">
		<div class="actions">
			<% if $Email %>
				<a href="$EmailURL" class="action email">
					<%t GuestbookPage_ss.EMAIL "E-mail" %>
				</a>
			<% end_if %>
			<% if $Website %>
				<a href="$Website" rel="popup" class="action website">
					<%t GuestbookPage_ss.WEBSITE "Website" %>
				</a>
			<% end_if %>
			<% if $Top.Moderator %>
				<a href='$EditLink' class="action edit">
					<%t GuestbookPage_ss.EDIT "Edit" %>
				</a>
			<% end_if %>
		</div>
		<div class="title">$Name</div>
		<div class="date">$Date</div>
		<div class="message">
			$FormattedMessage

		<% if $Comment %>
			<div class="comment">
				<strong><%t GuestbookPage_ss.ss.COMMENT "Comment [Administrator]:" %></strong><br />
				$FormattedComment
			</div>
		<% end_if %>
		</div>
	</article>
<% end_loop %>
