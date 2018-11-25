<?php
namespace ServiceProvider;

abstract class ResponseBase {

  public $_response;
  public $_responseArray;

  public function __construct($message){
    $this->_response = json_decode($message);
    $this->_responseArray = json_decode($message, true);
  }
  public abstract function isSuccess();

  public function isError() {
    if (!is_object($this->getResponse()))
      return true;

    if (isset($this->getResponse()->msg))
      return true;

    if (isset($this->getResponse()->data))
      return true;

    return false;
  }

  public function isValid() {
    return !($this->_response === false || $this->_response == null);
  }

  public function getResponse() {
    return $this->_response;
  }

  public function getResponseArray() {
    return $this->_responseArray;
  }

}
?>
