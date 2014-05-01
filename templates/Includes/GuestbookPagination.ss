	<article>
		<p class="pagination">
			<% with PaginatedEntries %>
				<% include PageControls %>
			<% end_with %>
		</p>
		<p><%t GuestbookPage_ss.ENTRIES "Entries: <strong>{count}</strong>" count=$PaginatedEntries.Count %></p>
	</article>
