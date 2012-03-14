<?php










namespace Symfony\Component\HttpKernel\CacheClearer;






class ChainCacheClearer implements CacheClearerInterface
{
    


    protected $clearers;

    




    public function __construct(array $clearers = array())
    {
        $this->clearers = $clearers;
    }

    


    public function clear($cacheDir)
    {
        foreach ($this->clearers as $clearer) {
            $clearer->clear($cacheDir);
        }
    }

    




    public function add(CacheClearerInterface $clearer)
    {
        $this->clearers[] = $clearer;
    }
}
