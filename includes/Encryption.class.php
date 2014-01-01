<?php

  /**
   * This class offers a wrapper for the php5-mcrypt module that helps you
   * encrypting and decrypting data. The encrypted data can be returned and
   * expected in binary or hexadecimal form.
   * 
   * <code>
   * // Encrypt text
   * $encrypted_text = Encryption::encrypt('this text is unencrypted');
   * // Decrypt text
   * $decrypted_text = Encryption::decrypt($encrypted_text); 
   * </code>           
   *    
   * @copyright Copyright (c) 2013, Björn Ebbrecht
   * @author Björn Ebbrecht <bjoern.ebbrecht@gmail.com>
   * @version 1.0
   */                 
  class Encryption {
  
    /**
     * The encryption/decryption algorithm.
     * @const string
     */                   
    const CYPHER = MCRYPT_BLOWFISH;
    
    /**
     * The encryption/decryption mode.
     * @const string
     */   
    const MODE = MCRYPT_MODE_CBC;
    
    /**
     * The very secret key for encrypting. Use your own here!
     * @const string
     */   
    CONST DEFAULT_KEY = 'iXK3ugsBJJSlBkb';
    
    /**
     * This method encrypts a given string and returns the encrypted data in
     * binary or hexadecimal form.
     * 
     * @param string $string the string to be encrypt
     * @param bool $hex optional returns the encrypted data in hexadecimal form
     * @return string the encrypted data
     */                                  
    public static function encrypt($string, $raw_output = false) {
    
      /**
       * Initializing the mcrypt module.
       */             
      $td = mcrypt_module_open(self::CYPHER, '', self::MODE, '');
      
      /**
       * Create initialization vector. Constant MCRYPT_RAND is also supported on
       * windows, so we can offer cross-platform running.
       */                    
      $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
      
      /**
       * Initializing all buffers needed for encryption
       */             
      mcrypt_generic_init($td, self::DEFAULT_KEY, $iv);
      
      /**
       * encrypt the given string
       */             
      $crypttext = mcrypt_generic($td, $string);
      
      /**
       * Deinitializes the mcrypt-module, deleting from ram.
       */             
      mcrypt_generic_deinit($td);
      
      /**
       * Returning the encrypted data either in a binary or hexadecimal way
       */             
      if ($raw_output === false) {
        $return = bin2hex($iv.$crypttext);
      } else {
        $return = $iv.$crypttext;
      }
      
      return $return;
        
    }
    
    /**
     * This method decrypts a given string and returns the decrypted data as a
     * string. 
     * 
     * @param string $string the encrypted data (binary or hexadecimal)
     * @param bool $hex optional expects binary data, if true hexadecimal
     * @return (string|bool) decrypted version of the encrypted string or false
     */                                 
    public static function decrypt($string, $raw_input = false) {
    
      /**
       * Converting the given string from hex to bin if needed.
       */             
      if ($raw_input === false) {
        $string = pack("H*" , $string);
      }
      
      /**
       * Initializing the mcrypt module.
       */   
      $td = mcrypt_module_open(self::CYPHER, '', self::MODE, '');
      
      /**
       * Getting the initialization vector for decrypting via substring.
       */             
      $ivsize = mcrypt_enc_get_iv_size($td);
      $iv = substr($string, 0, $ivsize);
      
      /**
       * Getting the encrypted text via substring
       */             
      $crypttext = substr($string, $ivsize);
      
      if ($iv) {
      
        /**
         * Decrypt data and return.
         */                 
        mcrypt_generic_init($td, self::DEFAULT_KEY, $iv);
        return mdecrypt_generic($td, $crypttext);
      
      } else {
      
        return false;
      
      }
    }
  }

?>
