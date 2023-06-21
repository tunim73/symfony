<?php

namespace App\Controller;

use App\Entity\Series;
use App\Repository\SeriesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SeriesController extends AbstractController
{

    public function __construct(private SeriesRepository $seriesRepository)
    {
    }

    #[Route('/series', name: 'app_series', methods: ['GET'])]
    public function seriesList(): Response
    {
        $seriesList = $this->seriesRepository->findAll();

        return $this->render('series/index.html.twig', [
            'seriesList' => $seriesList,
        ]);
    }


    #[Route('/series/create', name: 'app_series_form_GET' ,methods: ['GET'])]
    public function addSeriesForm(): Response
    {
        return $this->render('series/form.html.twig'); //atalho alt+enter
    }

    #[Route('/series/create', name: 'app_series_form_POST', methods: ['POST'])]
    public function addSeries(Request $req): Response
    {
        $seriesName =  $req->request->get('name');

        $series = new Series($seriesName);

        $this->seriesRepository->save($series, true);

        return new RedirectResponse('/series');

    }

}
