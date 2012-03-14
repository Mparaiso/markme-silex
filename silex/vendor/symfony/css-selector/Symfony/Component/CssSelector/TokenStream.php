<?php










namespace Symfony\Component\CssSelector;









class TokenStream
{
    private $used;
    private $tokens;
    private $source;
    private $peeked;
    private $peeking;

    





    public function __construct($tokens, $source = null)
    {
        $this->used = array();
        $this->tokens = $tokens;
        $this->source = $source;
        $this->peeked = null;
        $this->peeking = false;
    }

    




    public function getUsed()
    {
        return $this->used;
    }

    






    public function next()
    {
        if ($this->peeking) {
            $this->peeking = false;
            $this->used[] = $this->peeked;

            return $this->peeked;
        }

        if (!count($this->tokens)) {
            return null;
        }

        $next = array_shift($this->tokens);
        $this->used[] = $next;

        return $next;
    }

    









    public function peek()
    {
        if (!$this->peeking) {
            if (!count($this->tokens)) {
                return null;
            }

            $this->peeked = array_shift($this->tokens);

            $this->peeking = true;
        }

        return $this->peeked;
    }
}
