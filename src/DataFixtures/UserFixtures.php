<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture

{
	/**
	 * @var UserPasswordEncoderInterface
	 */
	private $encoder;

	/**
	 * En général, on peut récupérer des services par autowiring dans les constructeurs des classes
	 */
	public function __construct(UserPasswordEncoderInterface $encoder)
	{
		$this->encoder = $encoder;
	}

	public function load(ObjectManager $manager)
    {
    	// génére 3 admins
        for ($i = 0; $i < 3; $i++) {
        	$user = new User();
        	$hash = $this->encoder->encodePassword($user, 'admin' . $i);

        	$user
				->setEmail('admin' . $i . '@blog.fr')
				->setRoles(['ROLE_ADMIN'])
				->setPassword($hash);

        	$manager->persist($user);
		}

        // génère 5 users
		for ($i = 0; $i < 5; $i++) {
			$user = new User();
			$hash = $this->encoder->encodePassword($user, 'user' . $i);

			$user
				->setEmail('user' . $i . '@blog.fr')
				->setPassword($hash);

			$manager->persist($user);
		}

        $manager->flush();
    }
}
