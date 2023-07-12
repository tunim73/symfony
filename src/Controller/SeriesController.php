<?php

namespace App\Controller;

use App\Entity\Series;
use App\Form\SeriesType;
use App\Repository\SeriesRepository;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\This;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SeriesController extends AbstractController
{

    public function __construct(private SeriesRepository $seriesRepository,
                                private EntityManagerInterface $entityManager)
    {
    }

    #[Route('/series', name: 'app_series', methods: ['GET'])]
    public function seriesList(Request $request): Response
    {
        $seriesList = $this->seriesRepository->findAll();

        return $this->render('series/index.html.twig', [
            'seriesList' => $seriesList,
        ]);
    }


    #[Route('/series/create', name: 'app_series_form_GET' ,methods: ['GET'])]
    public function addSeriesForm(): Response
    {

        $seriesForm = $this->createForm(SeriesType::class, new Series(''));

       /* $seriesForm=$this->createFormBuilder(new Series(''))
            ->add('name', TextType::class, ['label'=>'Nome: '])
            ->add('save', SubmitType::class, ['label'=>'Adicionar'])
            ->getForm();*/


        return $this->renderForm('series/form.html.twig', compact('seriesForm'));
    }

    #[Route('/series/create', name: 'app_series_form_POST', methods: ['POST'])]
    public function addSeries(Request $req): Response
    {
        $series = new Series("");

        $seriesForm = $this->createForm(SeriesType::class, $series)
            ->handleRequest($req);
        /* Esse handleRequest coloca, de acordo com o nome do campo do form, tudo no objeto
        series, sem eu precisar pegar cada campo e atribuir, manualmente, aos atributos da classe
         series */

        if(!$seriesForm->isValid()){
            return $this->renderForm('series/form.html.twig', compact('seriesForm'));
        }

        $this->addFlash('success', 'Série Adicionada com sucesso');

        $this->seriesRepository->save($series, true);

        return new RedirectResponse('/series');

    }


    /* com requirements, é restringir o que vem na url, por exemplo, aqui to falando que o id
só pode ser digitos, se eu colocar uma string, vai dar erro antes mesmo de checar na função */
    #[Route('/series/delete/{id}',
        name: 'app_delete_series',
        methods: ['DELETE'],
        requirements: ['id' => '[0-9]+']
    )]
    public function deleteSeries(int $id, Request $request): Response
    {
        $this->seriesRepository->removeById($id);

        $this->addFlash('success', 'Série removida com sucesso');

        return new RedirectResponse('/series');
    }

    #[Route('/series/edit/{id}', name: 'app_series_form_edit_GET',
        methods: ['GET'],
        requirements: ['id' => '[0-9]+'])]
    public function editSeriesForm(int $id):Response
    {
        /*
        A injeção de dependência automática não ta funcionando, nas configurações até onde vi
        estão normais, então fui no banco manualmente para pegar o name
        */

        $series = $this->seriesRepository->find($id);

        $seriesForm = $this->createForm(SeriesType::class,
            $series, options: ['is_edit' => true ] );


        return $this->renderForm('series/form.html.twig', compact('seriesForm', 'series'));

       /* Funciona igual em cima, a função compact é para quando a chave tiver o mesmo nome do
        da variavel valor'*/
        /*return $this->render('series/form.html.twig', [
            'series' => $series
        ]);*/
    }


    #[Route('/series/edit/{id}', name: 'app_series_form_edit_PATCH', methods: ['PATCH'],
        requirements: ['id' => '[0-9]+'])]
    public function editSeries(int $id, Request $request):Response
    {
        $series = $this->seriesRepository->find($id);
        $seriesForm = $this->createForm(SeriesType::class,$series, ['is_edit'=>true] );
        $seriesForm->handleRequest($request);

        if(!$seriesForm->isValid()) {
            return $this->renderForm('series/form.html.twig',
                compact('seriesForm', 'series'));
        }

        $this->seriesRepository->save($series, true);

        $this->addFlash('success', 'Série Editada com sucesso');

        return new RedirectResponse('/series');
    }











}
