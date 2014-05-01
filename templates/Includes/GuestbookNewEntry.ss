	<div id="guestbookFormPanel" class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">
				<span class="caret-right"></span>
				<a data-toggle="collapse" data-parent="#accordion" href="#guestbookForm" class="<% if not $IsFormSubmitted %>collapsed<% end_if %>">
					<%-- <span class="glyphicon glyphicon-chevron-down"></span> --%>
					<%t GuestbookPage_ss.NEWENTRY "New entry" %>
				</a>
			</h4>
		</div>
		<div id="guestbookForm" class="panel-collapse collapse<% if $IsFormSubmitted %> in<% end_if %>">
			<div class="panel-body">
				$NewEntryForm
			</div>
		</div>
	</div>
