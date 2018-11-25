<?php
namespace ServiceProvider;

class Customer {
  protected $_customer_ip;
  protected $_customer_email;

  protected $_customer_first_name;
  protected $_customer_last_name;
  protected $_customer_company;
  protected $_customer_address;
  protected $_customer_city;
  protected $_customer_state;
  protected $_customer_zip;
  protected $_customer_country;
  protected $_customer_phone;
  protected $_customer_fax;
  protected $_customer_birth_date = NULL;


  public function setIP() {
   // $this->_customer_ip = $this->_setNullIfEmpty($ip);
    $this->_customer_ip = $this->get_client_ip();
  }
  public function getIP() {
    return $this->_customer_ip;
  }



  public function get_client_ip() {
    $ipaddress = '';
    if (getenv('HTTP_CLIENT_IP'))
        $ipaddress = getenv('HTTP_CLIENT_IP');
    else if(getenv('HTTP_X_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    else if(getenv('HTTP_X_FORWARDED'))
        $ipaddress = getenv('HTTP_X_FORWARDED');
    else if(getenv('HTTP_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_FORWARDED_FOR');
    else if(getenv('HTTP_FORWARDED'))
       $ipaddress = getenv('HTTP_FORWARDED');
    else if(getenv('REMOTE_ADDR'))
        $ipaddress = getenv('REMOTE_ADDR');
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}

  public function setEmail($email) {
    $this->_customer_email = $this->_setNullIfEmpty($email);
  }
  public function getEmail() {
    return $this->_customer_email;
  }

  public function setFirstName($first_name) {
    $this->_customer_first_name = $this->_setNullIfEmpty($first_name);
  }
  public function getFirstName() {
    return $this->_customer_first_name;
  }

  public function setLastName($last_name) {
    $this->_customer_last_name = $this->_setNullIfEmpty($last_name);
  }
  public function getLastName() {
    return $this->_customer_last_name;
  }

  public function setCompany($customer_company) {
    $this->_customer_company = $this->_setNullIfEmpty($customer_company);
  }
  public function getCompany() {
    return $this->_customer_company;
  }


  

  public function setAddress($address) {
    $this->_customer_address = $this->_setNullIfEmpty($address);
  }

  public function getAddress() {
    return $this->_customer_address;
  }

  public function setCity($city) {
    $this->_customer_city = $this->_setNullIfEmpty($city);
  }
  public function getCity() {
    return $this->_customer_city;
  }

  public function setCountry($country) {
    $this->_customer_country = $this->_setNullIfEmpty($country);
  }
  public function getCountry() {
    return $this->_customer_country;
  }

  public function setState($state) {
     $this->_customer_state = $this->_setNullIfEmpty($state);
  }
  public function getState() {
   // return (in_array($this->_customer_country, array( 'US', 'CA'))) ? $this->_customer_state : null;
     return $this->_customer_state;
  }

  public function setZip($zip) {
    $this->_customer_zip = $this->_setNullIfEmpty($zip);
  }
  public function getZip() {
    return $this->_customer_zip;
  }

  public function setPhone($phone) {
    $this->_customer_phone = $this->_setNullIfEmpty($phone);
  }
  public function getPhone() {
    return $this->_customer_phone;
  }

  public function setFax($phone) {
    $this->_customer_fax = $this->_setNullIfEmpty($phone);
  }
  public function getFax() {
    return $this->_customer_fax;
  }


  public function setBirthDate($birthdate) {
    $this->_customer_birth_date = $this->_setNullIfEmpty($birthdate);
  }
  public function getBirthDate() {
    return $this->_customer_birth_date;
  }

  private function _setNullIfEmpty(&$resource) {
    return (strlen($resource) > 0) ? $resource : null;
  }
}
?>
