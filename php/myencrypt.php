<?php
/*
 * Password Hashing With PBKDF2 (http://crackstation.net/hashing-security.htm).
 * Copyright (c) 2013, Taylor Hornby
*/

// These constants may be changed without breaking existing hashes.
define("PBKDF2_HASH_ALGORITHM", "sha256");
define("PBKDF2_ITERATIONS", 1000);
define("PBKDF2_SALT_BYTE_SIZE", 24);
define("PBKDF2_HASH_BYTE_SIZE", 24);

define("HASH_SECTIONS", 4);
define("HASH_ALGORITHM_INDEX", 0);
define("HASH_ITERATION_INDEX", 1);
define("HASH_SALT_INDEX", 2);
define("HASH_PBKDF2_INDEX", 3);

function create_hash($password) {
  $salt = base64_encode(mcrypt_create_iv(PBKDF2_SALT_BYTE_SIZE, MCRYPT_DEV_URANDOM));
  return 
    base64_encode(pbkdf2(
      PBKDF2_HASH_ALGORITHM,
      $password,
      $salt,
      PBKDF2_ITERATIONS,
      PBKDF2_HASH_BYTE_SIZE,
      true
    )) . $salt;
}

function check_hash($password, $correct_hash) {
  $n = strlen($correct_hash) / 2;
  $salt = substr($correct_hash, $n);
  $hash = base64_encode(pbkdf2(
      PBKDF2_HASH_ALGORITHM,
      $password,
      $salt,
      PBKDF2_ITERATIONS,
      PBKDF2_HASH_BYTE_SIZE,
      true
    ));
    return ($hash === substr($correct_hash, 0, $n));
}

/*
 * PBKDF2 key derivation function as defined by RSA's PKCS #5: https://www.ietf.org/rfc/rfc2898.txt
 * $algorithm - The hash algorithm to use. Recommended: SHA256
 * $password - The password.
 * $salt - A salt that is unique to the password.
 * $count - Iteration count. Higher is better, but slower. Recommended: At least 1000.
 * $key_length - The length of the derived key in bytes.
 * $raw_output - If true, the key is returned in raw binary format. Hex encoded otherwise.
 * Returns: A $key_length-byte key derived from the password and salt.
 *
 * Test vectors can be found here: https://www.ietf.org/rfc/rfc6070.txt
 *
 * This implementation of PBKDF2 was originally created by https://defuse.ca
 * With improvements by http://www.variations-of-shadow.com
 */
function pbkdf2($algorithm, $password, $salt, $count, $key_length, $raw_output = false){
  $algorithm = strtolower($algorithm);
  if(!in_array($algorithm, hash_algos(), true))
      trigger_error('PBKDF2 ERROR: Invalid hash algorithm.', E_USER_ERROR);
  if($count <= 0 || $key_length <= 0)
      trigger_error('PBKDF2 ERROR: Invalid parameters.', E_USER_ERROR);
  if (function_exists("hash_pbkdf2")) {
    // The output length is in NIBBLES (4-bits) if $raw_output is false!
    if (!$raw_output) {
        $key_length = $key_length * 2;
    }
    return hash_pbkdf2($algorithm, $password, $salt, $count, $key_length, $raw_output);
  }
  $hash_length = strlen(hash($algorithm, "", true));
  $block_count = ceil($key_length / $hash_length);
  $output = "";
  for($i = 1; $i <= $block_count; $i++) {
    // $i encoded as 4 bytes, big endian.
    $last = $salt . pack("N", $i);
    // first iteration
    $last = $xorsum = hash_hmac($algorithm, $last, $password, true);
    // perform the other $count - 1 iterations
    for ($j = 1; $j < $count; $j++) {
        $xorsum ^= ($last = hash_hmac($algorithm, $last, $password, true));
    }
    $output .= $xorsum;
  }
  if($raw_output)
      return substr($output, 0, $key_length);
  else
      return bin2hex(substr($output, 0, $key_length));
}

?>
