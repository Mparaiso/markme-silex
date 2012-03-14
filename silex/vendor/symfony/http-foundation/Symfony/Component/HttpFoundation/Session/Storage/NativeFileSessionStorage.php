<?php










namespace Symfony\Component\HttpFoundation\Session\Storage;








class NativeFileSessionStorage extends AbstractSessionStorage
{
    


    private $savePath;

    







    public function __construct($savePath = null, array $options = array())
    {
        if (null === $savePath) {
            $savePath = sys_get_temp_dir();
        }

        if (!is_dir($savePath)) {
            mkdir($savePath, 0777, true);
        }

        $this->savePath = $savePath;

        parent::__construct($options);
    }

    


    protected function registerSaveHandlers()
    {
        ini_set('session.save_handler', 'files');
        ini_set('session.save_path', $this->savePath);
    }
}
