<?php


namespace App\Tests\Service;


use App\Entity\Dinosaur;
use App\Entity\Enclosure;
use App\Entity\Security;
use App\Exception\DinosaursAreRunningRampantException;
use App\Exception\NotABuffetException;
use App\Service\EnclosureBuilderService;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class EnclosureBuilderServiceIntegrationTest extends KernelTestCase
{

    public function setUp(): void
    {
        self::bootKernel();

        $this->truncateEntities([
            Enclosure::class,
            Security::class,
            Dinosaur::class,
        ]);
    }

    /**
     * @throws DinosaursAreRunningRampantException
     * @throws NotABuffetException
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function testItBuildsEnclosureWithDefaultSpecifications()
    {
        /** @var EnclosureBuilderService $enclosureBuilderService */
        $enclosureBuilderService = self::$kernel->getContainer()
            ->get('test.' . EnclosureBuilderService::class);

        $enclosureBuilderService->buildEnclosure(1, 3);

        $em = $this->getEntityManager();

        $count = (int)$em->getRepository(Security::class)
            ->createQueryBuilder('s')
            ->select('COUNT(s.id)')
            ->getQuery()
            ->getSingleScalarResult();


        $this->assertSame(1, $count, 'Amount of security systems is not the same');

        $count = (int)$em->getRepository(Dinosaur::class)
            ->createQueryBuilder('d')
            ->select('COUNT(d.id)')
            ->getQuery()
            ->getSingleScalarResult();

        $this->assertSame(3, $count, 'Amount of dinosaurs is not the same');

    }

    private function truncateEntities(array $entities)
    {
        $purger = new ORMPurger($this->getEntityManager());
        $purger->purge();
    }

    /**
     * @return EntityManager
     */
    private function getEntityManager()
    {
        return self::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

}