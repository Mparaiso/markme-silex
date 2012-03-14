<?php










namespace Silex\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;

use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\MessageSelector;
use Symfony\Component\Translation\Loader\ArrayLoader;






class TranslationServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['translator'] = $app->share(function () use ($app) {
            $translator = new Translator(isset($app['locale']) ? $app['locale'] : 'en', $app['translator.message_selector']);

            if (isset($app['locale_fallback'])) {
                $translator->setFallbackLocale($app['locale_fallback']);
            }

            $translator->addLoader('array', $app['translator.loader']);
            foreach ($app['translator.messages'] as $locale => $messages) {
                $translator->addResource('array', $messages, $locale);
            }

            return $translator;
        });

        $app['translator.loader'] = $app->share(function () {
            return new ArrayLoader();
        });

        $app['translator.message_selector'] = $app->share(function () {
            return new MessageSelector();
        });

        if (isset($app['translation.class_path'])) {
            $app['autoloader']->registerNamespace('Symfony\\Component\\Translation', $app['translation.class_path']);
        }
    }
}
