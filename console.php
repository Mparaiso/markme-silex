<?php

/**
 * console application
 * console.php
 * @usage " php console.php [arguments] "
 */
use MarkMe\App;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\Input;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\Output;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;

require __DIR__ . '/vendor/autoload.php';

$app = new App(array('debug' => true));

$console = $app['console'];

/** @var  Application $console */
# register doctrine orm commands

$app['orm.console.boot_commands']();

/** encode a password with the application encoder */
$console->register('password:encode')
        ->setDescription('encode a password with a salt')
        ->addArgument('password', InputArgument::REQUIRED, 'the password to encode')
        ->addArgument('salt', InputArgument::REQUIRED, 'the password salt')
        ->setCode(function (Input $input, Output $output) use ($app) {
            $digest = $app['security.encoder.digest'];
            /** @var MessageDigestPasswordEncoder $digest */
            $encodedPassword = $digest->encodePassword($input->getArgument('password'), $input->getArgument(('salt')));
            $output->writeln("The encoded password is : ");
            $output->writeln($encodedPassword);
            exit(0);
        });

$app->boot();

$app->flush();

$console->run();
