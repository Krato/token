<?php namespace Infinety\Token;

use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;
use Infinety\Token\TokenDb;


class Token
{
    protected $model;

    protected $key;

    public function __construct()
    {
        $this->model = new TokenDb;
        $this->key = 'TokenValues';
    }

    public function add($ref, $type, $expires = null)
    {
        $token = $this->model->newInstance();
        $token->type = $type;
        $token->ref = $ref;
        $token->code = $this->uniqueCode($type, $ref);
        $token->expires = $expires;
        $token->save();
        return $token->code;
    }

    protected function uniqueCode($type, $ref)
    {
        $mcrypt = new Crypt($this->key, 'AES-128-CBC');
        $code = $mcrypt::encrypt($ref."-".$type);
        return $code;
    }


    public function find($code, $type, $returnRef = true)
    {

        $token = $this->model->whereCode($code)->whereType($type)->first();
        if (is_null($token)) {
            return null;
        }
        $this->expire();
        if($returnRef){
            return $token->ref;
        } else {
            $mcrypt = new Crypt($this->key, 'AES-128-CBC');
            $code = $mcrypt::decrypt($token->code);
            $code = explode('-', $code);

            return (object)[
                'ref' => $code[0],
                'type' => $code[1]
            ];
        }
    }

    protected function expire()
    {
        $tokens = $this->model->all();
        foreach($tokens as $token) {
            if (is_null($token->expires)) {
                continue;
            }
            if(Carbon::now()->subMinutes($token->expires)->gt($token->created_at)) {
                $token->delete();
            }
        }
    }

    public function remove($code, $type)
    {
        $token = $this->model->whereCode($code)->whereType($type)->first();
        if ($token) {
            $token->delete();
        }
    }
}
