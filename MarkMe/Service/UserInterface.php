<?php

namespace MarkMe\Service;

use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use MarkMe\Entity\User as UserEntity;

interface UserInterface
{
    function setEncoderFactory(EncoderFactory $encoderFactory);

    /**
     * @return EncoderFactory
     */
    function getEncoderFactory();

    function register(UserEntity $user);
}