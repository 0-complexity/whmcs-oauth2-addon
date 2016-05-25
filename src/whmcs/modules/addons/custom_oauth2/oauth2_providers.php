<?php

require_once 'exceptions.php';
require_once 'utils.php';

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

	protected function getFirstSetting($property) {
		return get_first_value_from_array($this->identity, $property);
	}

	protected function getNestedSetting($property, $sub_property) {
		$address_info = $this->getFirstSetting($property);
		if ($address_info && isset($address_info[$sub_property])) {
			return $address_info[$sub_property];
		}
		return null;
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

	function __construct($identity) {
		parent::__construct($identity);
	}


	function getEmail() {
		$email = $this->getFirstSetting('email');
		if (!$email) {
			$email = isset($this->identity['username']) ? sprintf('%s@itsyou.online', $this->identity['username']) : null;
		}
		return $email;
	}

	function getPhone() {
		return $this->getFirstSetting('phone');
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
		return $this->identity['username'];
	}

	public function getLastName() {
		return null;
	}
}
