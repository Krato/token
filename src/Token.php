<?php namespace Infinety\Token;

use Carbon\Carbon;
use Infinety\Token\TokenDb;

class Token
{
    protected $model;

    public function __construct()
    {
        $this->model = new TokenDb;
    }

    public function add($ref, $type, $expires = null)
    {
        $token = $this->model->newInstance();
        $token->type = $type;
        $token->ref = $ref;
        $token->code = $this->uniqueCode();
        $token->expires = $expires;
        $token->save();
        return $token->code;
    }

    protected function uniqueCode()
    {
        $code = str_random(40);
        while (!is_null($this->model->whereCode($code)->first())) {
            $code = str_random(40);
        }
        return $code;
    }

    /**
     * @param string $code
     * @param string $type
     * @return int|null
     */
    public function find($code, $type)
    {

        $token = $this->model->whereCode($code)->whereType($type)->first();
        if (is_null($token)) {
            return null;
        }
        $this->expire();
        return $token->ref;
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