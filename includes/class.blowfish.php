<?php
class bcrypt
{
	//Check if Bcrypt is accessible
	private $rounds;
	public function __construct($rounds = 12)
	{
		if(!CRYPT_BLOWFISH)
		{
			throw new Exception('Make sure Bcrypt is installed. It will work on your PHP version.');
		}
		$this->rounds = $rounds;
	}

	//Make some salt
	public function generateSalt()
	{
		//openssl_random_psuedo_bytes(16) equivalent, since it might not be installed
		$seed = '';
		for ($i = 0; $i < 16; $i++)
		{
			$seed .= chr(mt_rand(0, 255));
		}

		/* Make the salt */
		$salt = substr(strtr(base64_encode($seed), '+', '.'), 0, 22);

		/* Return */
		return $salt;
	}

	public function generateHash($password)
	{
		 //Explanation for '$2a$' . $this->rounds . '$':
		 //	2a selects bcrypt algorithm
		 //	$this->rounds is the workload factor
		 //	Increase the workload for better security, but worse performance

		/* Generate the hash */
		$hash = crypt($password, '$2a$' . $this->rounds . '$' . $this->generateSalt());

		/* Return */
		return $hash;
	}

	/* Verify Password */
	public function verify($password, $existing_hash)
	{
		/* Hash new password with old hash */
		$hash = crypt($password, $existing_hash);

		/* Do Hashes match? */
		if ($hash == $existing_hash)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
}