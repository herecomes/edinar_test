<?php
namespace ServiceProvider;

class Card {
  protected $_entry_type;
  protected $_expiration_date;
  protected $_condition;
  protected $_eci;
  protected $_cavv;
  protected $_xid;
  protected $_card_number;
  protected $_card_holder;
  protected $_card_exp_month;
  protected $_card_exp_year;
  protected $_card_cvc;
  protected $_first_1;
  protected $_last_4;
  protected $_brand;
  protected $_card_token = null;
  protected $_card_skip_threed_secure = false;


  public function setEntryType($entry_type) {
    $this->_entry_type = $entry_type;
  }
  public function getEntryType() {
    return $this->_entry_type;
  }
  public function setExpirationDate() {
    $this->_expiration_date = $this->_card_exp_month."/". substr($this->_card_exp_year, -2);
  }
  public function getExpirationDate() {
    return $this->_expiration_date;
  }

  public function setCondition($condition) {
    $this->_condition = $condition;
  }
  public function getCondition() {
    return $this->_condition;
  }

  public function setEci($eci) {
    $this->_eci = $eci;
  }
  public function getEci() {
    return $this->_eci;
  }

   public function setCavv($cavv) {
    $this->_cavv = $cavv;
  }
  public function getCavv() {
    return $this->_cavv;
  }
  public function setXid($xid) {
    $this->_xid = $xid;
  }
  public function getXid() {
    return $this->_xid;
  }

  public function setCardNumber($number) {
    $this->_card_number = $number;
  }
  public function getCardNumber() {
    return $this->_card_number;
  }

  public function setCardHolder($holder) {
    $this->_card_holder = $holder;
  }
  public function getCardHolder() {
    return $this->_card_holder;
  }

  public function setCardExpMonth($exp_month) {
    $this->_card_exp_month = sprintf('%02d', $exp_month);
  }
  public function getCardExpMonth() {
    return $this->_card_exp_month;
  }

  public function setCardExpYear($exp_year) {
    $this->_card_exp_year = $exp_year;
  }
  public function getCardExpYear() {
    return $this->_card_exp_year;
  }

  public function setCardCvc($cvc) {
    $this->_card_cvc = $cvc;
  }
  public function getCardCvc() {
    return $this->_card_cvc;
  }

  public function setCardToken($token) {
    $this->_card_token = $token;
  }
  public function getCardToken() {
    return $this->_card_token;
  }

  public function setSkip3D($skip = false) {
    $this->_card_skip_threed_secure = $skip;
  }
  public function getSkip3D() {
    return $this->_card_skip_threed_secure;
  }

  public function setBrand($brand) {
    $this->_brand = $brand;
  }
  public function getBrand() {
    return $this->_brand;
  }
  public function setFirst_1($digit) {
    $this->_first_1 = $digit;
  }
  public function getFirst_1() {
    return $this->_first_1;
  }
  public function setLast_4($digits) {
    $this->_last_4 = $digits;
  }
  public function getLast_4() {
    return $this->_last_4;
  }
}
?>
