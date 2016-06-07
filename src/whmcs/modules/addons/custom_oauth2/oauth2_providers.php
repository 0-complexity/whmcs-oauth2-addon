<?php

require_once 'exceptions.php';

const PROVIDER_ITSYOU_ONLINE = 'It\'s You Online';

function get_oauth_providers() {
	return join(',', array(PROVIDER_ITSYOU_ONLINE));
}

/**
 * @param $vars array provided by the module functions
 * @param $identity array object containing the identity of the user
 * @return OAuthProvider
 * @throws BusinessException
 */
function get_oauth_provider($provider, $identity) {
	switch ($provider) {
		case PROVIDER_ITSYOU_ONLINE:
			return new ItsYouOnlineProvider($identity);
		default:
			throw new BusinessException('Unknown provider: ' . $provider);
	}
}

/**
 * This class will be used to parse the identity of the user, so data like email, address, phone can be prefilled when first logging in with an OAuth service.
 */
abstract class OAuthProvider {
	/**
	 * @var array identity is an object containing user information for a specific OAuth provider
	 */
	protected $identity;


	function __construct($identity) {
		$this->identity = $identity;
	}

	abstract public function getEmail();

	abstract public function getPhone();

	abstract public function getAddress();

	abstract public function getCountry();

	abstract public function getCity();

	abstract public function getState();

	abstract public function getPostcode();

	abstract public function getFirstName();

	abstract public function getLastName();

}


class ItsYouOnlineProvider extends OAuthProvider {
	protected function getMainSetting($property) {
		return $this->identity[$property]['main'];
	}

	protected function getNestedSetting($property, $sub_property) {
		$address_info = $this->getMainSetting($property);
		if ($address_info && isset($address_info[$sub_property])) {
			return $address_info[$sub_property];
		}
		return null;
	}

	function __construct($identity) {
		parent::__construct($identity);
	}


	function getEmail() {
		$email = $this->getMainSetting('email');
		if (!$email) {
			$email = isset($this->identity['username']) ? sprintf('%s@itsyou.online', $this->identity['username']) : null;
		}
		return $email;
	}

	function getPhone() {
		return preg_replace('/[^0-9]/', '', str_replace('+', '00', $this->getMainSetting('phone')));
	}

	public function getAddress() {
		$street = $this->getNestedSetting('address', 'street');
		$number = $this->getNestedSetting('address', 'nr');
		return sprintf('%s %s', $street, $number);
	}

	public function getCountry() {
		// We need a 2 letter code, and the full country name is returned
		return null;
	}

	public function getCity() {
		return $this->getNestedSetting('address', 'city');
	}

	public function getState() {
		return null;
	}

	public function getPostcode() {
		return $this->getNestedSetting('address', 'postalcode');
	}

	public function getFirstName() {
		$firstname = $this->identity['firstname'];
		if ($firstname == '') {
			$firstname = $this->identity['username'];
		}
		return $firstname;
	}

	public function getLastName() {
		return $this->identity['lastname'];
	}
}
