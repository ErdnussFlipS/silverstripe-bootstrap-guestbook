<?php

/**
 * Description of GuestbookPage_Controller
 *
 * @author Hkn
 */
class GuestbookPage_Controller extends Page_Controller implements PermissionProvider {
	private static $allowed_actions = array(
			'NewEntryForm',
			'postEntry',
			'EmailProtectionForm',
			'unlockemails',
			'dounlockemails',
		);

	private $isFormSubmitted = false;

	public function init()
	{
		parent::init();
		Requirements::css("bootstrap-guestbook/css/guestbook.css");
		Requirements::javascript("bootstrap-guestbook/javascript/guestbook.js");

		if ($temp = Session::get('GuestbookFormSubmitted')) {
			$this->isFormSubmitted = $temp;
			Session::clear('GuestbookFormSubmitted');
		}
	}

	/**
	* Returns a paginated list of all pages in the site.
	*/
   public function PaginatedEntries() {
	   $list = new PaginatedList($this->Entries(), $this->request);
	   $list->setPageLength($this->EntriesPerPage);
	   return $list;
   }

   	public function providePermissions() {
		return array(
			'GUESTBOOK_MODERATE' => _t('GuestbookController.GUESTBOOKMODERATE', 'Edit guestbook entries'),
		);
	}

   public function Moderator($member = null) {
	   return self::isModerator($member);
   }

   static public function isModerator($member = null) {
	   return Permission::check('GUESTBOOK_MODERATE', "any", $member);
   }

	public function Smileys() {
		return new ArrayList(GuestbookEntry::Smileys());
	}

	public function SmileyButtons($fieldID) {
		return $this->renderWith("SmileyButtons", array('FieldID' => $fieldID));
	}
	
	public function NewEntryForm() {
		// Create fields
		$entry = new GuestbookEntry();
		$labels = $entry->fieldLabels();

		$fields = new FieldList(
				new TextField('Name', $labels['Name']),
				(new EmailField('Email', $labels['Email']))->setAttribute('type', 'email'),
				TextField::create('Website', $labels['Website'])->setAttribute('type', 'url'),
				new TextField("Headline", $labels['Headline']),
				new TextareaField("Message", $labels['Message'])
		);

		if ($this->EnableEmoticons) {
			$smileyButtons = $this->SmileyButtons("Form_NewEntryForm_Message");
			$smileyField = new LiteralField("Smileys", $smileyButtons);
			$fields->add($smileyField);
		}

		// Create actions
		$actions = new FieldList(
				(new FormAction('postEntry', _t("GuestbookController.POST", 'Post')))->addExtraClass('btn-block-xs')->setStyle('default')
		);

		$validator = new RequiredFields('Name', 'Email', 'Headline', 'Message', 'Captcha');

		$form = new BootstrapForm($this, 'NewEntryForm', $fields, $actions, $validator);
		$form->setRedirectToFormOnValidationError(true);
		if ($this->UseSpamProtection) {
			if (Form::has_extension('FormSpamProtectionExtension')) {
				$form->enableSpamProtection();
			} else {
				$message = _t('GuestbookController.SPAMPROTECTIONNOTINSTALLED', 'Spam protection has been enabled, but no spam protection module is installed!');
				$form->setMessage($message, 'warning');
			}
		}

		return $form;
	}

	public function IsFormSubmitted()
	{
		return $this->isFormSubmitted;
	}

	public function postEntry(array $data, Form $form) {
		$error = false;

		Session::set('GuestbookFormSubmitted', true);

		if (!empty($data['Name'])) {
			$form->addErrorMessage('Name', _t('GuestbookController.EMPTYNAME', "Empty Name."), 'bad');
			$error = true;
		}

		if (!empty($data['Website'])) {
			if (!filter_var($data['Website'], FILTER_VALIDATE_URL)) {
				$form->addErrorMessage('Website', _t('GuestbookController.INVALIDWEBSITEFORMAT', "Invalid format for website."), 'bad');
				$error = true;
			}
		}

		if (Session::get("GuestbookPosted") > time() - $this->FloodLimit) {
			$floodMessage = _t('GuestbookController.FLOODLIMITEXCEEDED', "You have already posted the last {seconds} seconds. Please wait.", "", $this->FloodLimit);
			$form->sessionMessage($floodMessage, 'bad');
			$error = true;
		}

		

		if ($error) {
			return $this->redirectBack();
		}

		$entry = GuestbookEntry::create();
		$entry->GuestbookID = $this->ID;
		$form->saveInto($entry);
		$entry->write();

		$form->sessionMessage(_t('GuestbookController.ENTRYSAVED', "Entry has been saved."), 'good');

		Session::set('GuestbookPosted', time());

		return $this->redirectBack();
	}

	public function EmailProtectionForm() {
		$fields = new FieldList();
		$actions = new FieldList(FormAction::create('dounlockemails'));

		$form = new Form($this, 'EmailProtectionForm', $fields, $actions);
		$form->enableSpamProtection();
		return $form;
	}

	public function unlockemails() {
		if ($this->canSeeEmailAddresses()) {
			// User can already see email addresses.
			// Redirect back to guestboook.
			return $this->redirect($this->Link());
		}


		// Force a login if no spam protection module exists.
		if (Form::has_extension('FormSpamProtectionExtension') == false) {
			return Security::permissionFailure($this, _t('GuestbookController.EMAILPROTECTIONLOGIN', "You must be logged in to see email addresses."));
		}

		// Use spam protection module
		return $this;
	}

	public function dounlockemails(array $data, Form $form) {
		if (!$form->validate()) {
			return $this->redirectBack();
		}

		Session::set('Guestbook-ShowEmails', true);

		// Add a flash message for the user
		if (method_exists('Page_Controller', 'addMessage')) {
			Page_Controller::addMessage(_t('GuestbookController.EMAILPROTECTIONUNLOCKED', 'Successfully unlocked E-mail addresses'), 'Success');
		}

		return $this->redirect($this->Link());
	}
}
