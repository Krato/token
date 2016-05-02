# Token

Manages random tokens for password resets and other one-time actions

### Installation

  * Install via composer: `composer require infinety-es/token`

  * Add services provider to `config/app.php`:
    - `Infinety\Token\TokenServiceProvider::class,`
    - `Vinkla\Hashids\HashidsServiceProvider::class`
	
  * Add facade to `config/app.php`:
      - `'Hashids' => Vinkla\Hashids\Facades\Hashids::class`

  * Run `php artisan token:migration` then `php artisan migrate` to add the Token database table
  * Run `php artisan vendor:publish`to publish vendor files

### Usage

  * Tokens are handled via `Infinety\Token\Token`, which can be instantiated by the container. Example:

  ```php
    $token = new Token;
    $token->add($myId, 'test', 1)
  ```


  * Token parameters:
	* **Reference**: integer ID of the object referred to by the token, e.g. User ID
	* **Type**: string determining the type of the token
	* **Expires**: integer determining how many minutes the token will remain valid for, null (forever) is default
  * `Token::new(int $ref, string $type, int $expires = null)` returns a hasID 40-character code
  * `Token::find(string $code, string $type, bool $returnId = false)` returns the reference ID or null if $returnId is = true, or Hashids decoded strings
  * `Token::remove(string $code, string $type)` deletes the token associated with the code (if found)
	 