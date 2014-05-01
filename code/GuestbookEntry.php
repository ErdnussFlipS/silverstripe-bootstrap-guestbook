<?php

/**
 * Represents each entry in a guestbook.
 */
class GuestbookEntry extends DataObject {
	/**
	 * @return array All defined smileys.
	 */
	public static function Smileys() {
		return array(
			new Smiley(":)", "smile.gif"),
			new Smiley(":e", "biggrin.gif"),
			new Smiley(":D", "supergrin.gif"),
			new Smiley(";)", "wink.gif"),
			new Smiley(":h", "cool.gif"),
			new Smiley(":P", "tongue.gif"),
			new Smiley(":-/", "undecided.gif"),
			new Smiley(":(", "frown.gif"),
			new Smiley(":o", "surprised.gif"),
			new Smiley(":r", "embarassed.gif"),
			new Smiley(":'(", "cry.gif"),
			new Smiley(":@", "angry.gif"),
			new Smiley("x(", "dead.gif"),
			new Smiley(":i", "innocent.gif"),
			new Smiley(":f", "flirt.gif"),			
			new Smiley(":#", "sealed.gif"),
			new Smiley(':$', "money.gif"),
		);
	}
	
	private static $db = array(
		'PortalId' => 'Int', // This is only used for importing entries from old guestbook
		'Date' => 'SS_DateTime',
		'Name' => 'Varchar',
		'Email' => 'Varchar',
		'Website' => 'Varchar',
		'IpAddress' => 'Varchar',
		'Host' => 'Varchar',
		'Headline' => 'Varchar',
		'Message' => 'Text',
		'Comment' => 'Text'
	);

	private static $has_one = array(
		'Guestbook' => 'GuestbookPage'
	);

	private static $default_sort = 'Date DESC';

	private static $searchable_fields = array(
		'Name', 'Email', 'Website', 'Headline', 'Message', 'Comment'
	);

	private static $summary_fields = array(
			'Date',
			'Name',
			'Email',
			'Headline',
			'Message'
	   );

	/**
	 * Creates a guestbook entry with default values.
	 * @return \GuestbookEntry
	 */
	public static function create() {
		$entry = new GuestbookEntry();
		$entry->Date = SS_DateTime::now()->getValue();
		$entry->IpAddress = $_SERVER['REMOTE_ADDR'];
		$entry->Host = gethostbyaddr($entry->IpAddress);
		return $entry;
	}

	/**
	 * Gets the message where smileys have been replaced with images.
	 * @return string
	 */
	public function FormattedMessage() {
		return $this->FormattedText($this->Message);
	}

	/**
	 * Gets the comment where smileys have been replaced with images.
	 * @return string
	 */
	public function FormattedComment() {
		return $this->FormattedText($this->Comment);
	}


	/**
	 * Formats text so it can be displayed as raw HTML. Also replaces smileys
	 * with images.
	 * @return string
	 */
	public function FormattedText($text) {
		$text = Convert::raw2xml($text);
		if ($this->Guestbook()->EnableEmoticons) {
			$text = $this->ReplaceSmileys($text);
		}
		$text = nl2br($text);
		return $text;
	}

	/**
	 * Replace smiley symbols with images.
	 * @param type $text
	 * @return type
	 */
	public function ReplaceSmileys($text) {
		foreach (self::Smileys() as $smiley) {
			$text = $smiley->replaceSymbols($text);
		}
		return $text;
	}

	public function getCMSFields() {
		$labels = $this->fieldLabels();

		$fields = parent::getCMSFields();
		$fields->removeByName("PortalId");
		$dateField = new DatetimeField("Date", $labels["Date"]);
		$dateField->getDateField()->setConfig('showcalendar', true);

		$fields->addFieldToTab("Root.Main", $dateField);
		$fields->addFieldToTab("Root.Main", new TextField("Name", $labels["Name"]));
		$fields->addFieldToTab("Root.Main", new EmailField("Email", $labels["Email"]));
		$fields->addFieldToTab("Root.Main", new TextField("Website", $labels["Website"]));
		$fields->addFieldToTab("Root.Main", new ReadonlyField("IpAddress", $labels["IpAddress"]));
		$fields->addFieldToTab("Root.Main", new ReadonlyField("Host", $labels["Host"]));
		$fields->addFieldToTab("Root.Main", new TextField("Headline", $labels["Headline"]));
		$fields->addFieldToTab("Root.Main", new TextareaField("Message", $labels["Message"]));
		$fields->addFieldToTab("Root.Main", new TextareaField("Comment", $labels["Comment"]));
		return $fields;
	}

	public function EditLink() {
		$id = $this->ID;
		return Director::baseURL() .
				"admin/guestbook/GuestbookEntry/EditForm/field/GuestbookEntry/item/$id/edit";
	}

	public function EmailURL() {
		if ($this->Guestbook()->canSeeEmailAddresses()) {
			return 'mailto:' . $this->Email;
		} else {
			return $this->Guestbook()->Link('unlockemails');
		}
	}

	public function fieldLabels($includerelations = true) {
		$labels = parent::fieldLabels($includerelations);

		$labels['Guestbook'] = _t('GuestbookEntry.Guestbook', "Guestbook");
		$labels['Date'] = _t('GuestbookEntry.Date', "Date");
		$labels['Name'] = _t('GuestbookEntry.Name', "Name");
		$labels['Email'] = _t('GuestbookEntry.Email', "Email");
		$labels['Website'] = _t('GuestbookEntry.Website', "Website");
		$labels['IpAddress'] = _t('GuestbookEntry.IpAddress', "IP address");
		$labels['Host'] = _t('GuestbookEntry.Host', "Host");
		$labels['Headline'] = _t('GuestbookEntry.Headline', "Headline");
		$labels['Message'] = _t('GuestbookEntry.Message', "Message");
		$labels['Comment'] = _t('GuestbookEntry.Comment', "Comment");

		return $labels;
   }
}
