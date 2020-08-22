<?php


namespace App\Controller;


use App\Entity\Enclosure;
use App\Factory\DinosaurFactory;
use App\Service\EnclosureBuilderService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     * @return Response
     */
    public function index()
    {
        $enclosures = $this->getDoctrine()->getRepository(Enclosure::class)->findAll();

        return $this->render('home/index.html.twig', [
            'enclosures' => $enclosures
        ]);
    }

    /**
     * @Route("/grow", name="grow_dinosaur", methods={"POST"})
     * @param Request $request
     * @param DinosaurFactory $dinosaurFactory
     * @return RedirectResponse
     */
    public function growAction(Request $request, DinosaurFactory $dinosaurFactory)
    {
        $manager = $this->getDoctrine()->getManager();
        $enclosure = $manager->getRepository(Enclosure::class)
            ->find($request->request->get('enclosure'));
        $specification = $request->request->get('specification');
        $dinosaur = $dinosaurFactory->growFromSpecification($specification);
        $dinosaur->setEnclosure($enclosure);
        $enclosure->addDinosaur($dinosaur);
        $manager->flush();

        $this->addFlash('success', sprintf(
            'Grew a %s in enclosure #%d',
            mb_strtolower($specification),
            $enclosure->getId()
        ));

        return $this->redirectToRoute('homepage');
    }
}